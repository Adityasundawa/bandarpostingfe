<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\UserFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileManagerController extends Controller
{
    const MAX_FILE_SIZE_MB = 500;

    /**
     * Halaman utama — tampilkan tree folder + file di path aktif
     */
    public function index(Request $request)
    {
        $user        = auth()->user();
        $activePath  = $request->query('path', 'meta');

        // Sanitize path
        $activePath  = $this->sanitizePath($activePath);

        // Bangun tree folder dari semua path yang ada di DB + session custom
        $folderTree  = $this->buildFolderTree($user->id);

        // Validasi path: harus ada di tree atau root default
        $allPaths    = $this->getAllPaths($user->id);
        if (!in_array($activePath, $allPaths)) {
            $activePath = 'meta';
        }

        // File di path ini saja (tidak rekursif)
        $files = UserFile::where('user_id', $user->id)
            ->where('path', $activePath)
            ->orderBy('created_at', 'desc')
            ->get();

        $filesJson = $files->map(fn($f) => [
            'id'            => $f->id,
            'original_name' => $f->original_name,
            'extension'     => $f->extension,
            'type'          => $f->type,
            'size'          => $f->size,
            'human_size'    => $f->human_size,
            'description'   => $f->description,
            'created_at'    => $f->created_at->format('d M Y, H:i'),
            'download_url'  => $f->download_url,
            'public_url'    => $f->public_url,
            'is_public'     => $f->is_public,
        ])->values();

        // Stats per path (file count + size)
        $pathStats = $this->getPathStats($user->id);

        return view('client.files.index', compact(
            'files', 'filesJson', 'activePath', 'folderTree', 'pathStats'
        ));
    }

    /**
     * AJAX: list file di path tertentu
     */
    public function listFiles(Request $request)
    {
        $user   = auth()->user();
        $path   = $this->sanitizePath($request->query('path', 'meta'));

        $files  = UserFile::where('user_id', $user->id)
            ->where('path', $path)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($f) => [
                'id'            => $f->id,
                'original_name' => $f->original_name,
                'extension'     => $f->extension,
                'type'          => $f->type,
                'size'          => $f->size,
                'human_size'    => $f->human_size,
                'description'   => $f->description,
                'created_at'    => $f->created_at->format('d M Y, H:i'),
                'download_url'  => $f->download_url,
            'public_url'    => $f->public_url,
            'is_public'     => $f->is_public,
            ]);

        return response()->json([
            'files'      => $files,
            'total'      => $files->count(),
            'total_size' => $this->humanSize($files->sum('size')),
            'path'       => $path,
        ]);
    }

    /**
     * Upload file ke path tertentu
     */
    public function upload(Request $request)
    {
        $request->validate([
            'path'   => 'required|string|max:200',
            'files'  => 'required|array|max:20',
            'files.*'=> 'required|file|max:' . (self::MAX_FILE_SIZE_MB * 1024),
        ]);

        $user  = auth()->user();
        $path  = $this->sanitizePath($request->path);
        $saved = 0;

        foreach ($request->file('files') as $file) {
            $ext        = strtolower($file->getClientOriginalExtension());
            $stored     = Str::uuid() . '.' . $ext;
            $storagePath= "users/{$user->id}/{$path}/{$stored}";

            Storage::disk('local')->put($storagePath, file_get_contents($file));

            UserFile::create([
                'user_id'       => $user->id,
                'path'          => $path,
                'original_name' => $file->getClientOriginalName(),
                'stored_name'   => $stored,
                'mime_type'     => $file->getMimeType(),
                'size'          => $file->getSize(),
                'extension'     => $ext,
            ]);
            $saved++;
        }

        return response()->json([
            'success' => true,
            'message' => "{$saved} file berhasil diupload ke /{$path}.",
        ]);
    }

    /**
     * Download file
     */
    public function download(UserFile $file)
    {
        $this->authorizeFile($file);
        $path = $file->storage_path;
        if (!Storage::disk('local')->exists($path)) abort(404);
        return Storage::disk('local')->download($path, $file->original_name);
    }

    /**
     * Update nama / deskripsi file
     */
    public function update(Request $request, UserFile $file)
    {
        $this->authorizeFile($file);
        $request->validate([
            'original_name' => 'sometimes|string|max:255',
            'description'   => 'sometimes|nullable|string|max:500',
        ]);
        $file->update($request->only(['original_name', 'description']));
        return response()->json(['success' => true, 'message' => 'Disimpan.']);
    }

    /**
     * Hapus file
     */
    public function destroy(UserFile $file)
    {
        $this->authorizeFile($file);
        Storage::disk('local')->delete($file->storage_path);
        $file->delete();
        return response()->json(['success' => true]);
    }

    /**
     * Buat subfolder baru di dalam path tertentu
     * Contoh: parent='meta', name='isco' → path='meta/isco'
     * Contoh: parent='meta/isco', name='sd' → path='meta/isco/sd'
     */
    public function createFolder(Request $request)
    {
        $request->validate([
            'parent' => 'required|string|max:200',  // path parent, contoh: 'meta' atau 'meta/isco'
            'name'   => ['required', 'string', 'max:50', 'regex:/^[a-zA-Z0-9_\-]+$/'],
        ], [
            'name.regex' => 'Nama folder hanya boleh huruf, angka, - dan _',
        ]);

        $user      = auth()->user();
        $parent    = $this->sanitizePath($request->parent);
        $name      = strtolower(trim($request->name));
        $newPath   = $parent . '/' . $name;

        // Cek duplikat
        $allPaths = $this->getAllPaths($user->id);
        if (in_array($newPath, $allPaths)) {
            return response()->json(['success' => false, 'message' => 'Folder sudah ada.'], 422);
        }

        // Simpan di session agar folder kosong tetap muncul
        $customPaths = session("custom_paths_{$user->id}", []);
        $customPaths[] = $newPath;
        session(["custom_paths_{$user->id}" => array_unique($customPaths)]);

        return response()->json([
            'success'  => true,
            'message'  => "Folder /{$newPath} berhasil dibuat.",
            'new_path' => $newPath,
            'parent'   => $parent,
            'name'     => $name,
        ]);
    }

    /**
     * AJAX: ambil tree folder untuk sidebar refresh
     */
    public function folderTree(Request $request)
    {
        $user = auth()->user();
        return response()->json([
            'tree'      => $this->buildFolderTree($user->id),
            'pathStats' => $this->getPathStats($user->id),
        ]);
    }

    // ─── Admin ───────────────────────────────────────────────────

    public function adminIndex(Request $request)
    {
        if (auth()->user()->role !== 1) abort(403);
        $query = UserFile::with('user')->orderBy('created_at', 'desc');
        if ($uid = $request->query('user_id')) $query->where('user_id', $uid);
        if ($path = $request->query('path'))   $query->where('path', 'like', $path . '%');
        return view('admin.files.index', ['files' => $query->paginate(50)]);
    }

    // ─── Private helpers ─────────────────────────────────────────

    /**
     * Sanitize path: hilangkan karakter berbahaya, normalize slash
     * 'meta//isco/../x' → 'meta/isco'
     */
    private function sanitizePath(string $path): string
    {
        // Hapus karakter berbahaya
        $path = preg_replace('/[^a-zA-Z0-9\/\-_]/', '', $path);
        // Normalize multiple slashes
        $path = preg_replace('/\/+/', '/', trim($path, '/'));
        // Max 5 level deep
        $parts = array_slice(explode('/', $path), 0, 5);
        // Pastikan root valid
        if (empty($parts[0])) $parts[0] = 'meta';
        return implode('/', $parts);
    }

    /**
     * Bangun folder tree sebagai array nested
     * [
     *   'meta' => [
     *     'name' => 'meta', 'path' => 'meta', 'depth' => 1,
     *     'children' => [
     *       'isco' => ['name'=>'isco','path'=>'meta/isco','depth'=>2,'children'=>[...]]
     *     ]
     *   ],
     *   'x' => [...]
     * ]
     */
    private function buildFolderTree(int $userId): array
    {
        $allPaths = $this->getAllPaths($userId);
        sort($allPaths);

        $tree = [];

        // Pastikan root defaults selalu ada
        foreach (UserFile::ROOT_FOLDERS as $root) {
            if (!in_array($root, $allPaths)) $allPaths[] = $root;
        }
        sort($allPaths);

        foreach ($allPaths as $path) {
            $parts  = explode('/', $path);
            $cursor = &$tree;

            foreach ($parts as $i => $part) {
                if (!isset($cursor[$part])) {
                    $cursor[$part] = [
                        'name'     => $part,
                        'path'     => implode('/', array_slice($parts, 0, $i + 1)),
                        'depth'    => $i + 1,
                        'children' => [],
                    ];
                }
                $cursor = &$cursor[$part]['children'];
            }
        }

        return $tree;
    }

    /**
     * Semua path yang sudah ada (DB + session custom)
     */
    private function getAllPaths(int $userId): array
    {
        $dbPaths     = UserFile::where('user_id', $userId)->distinct()->pluck('path')->toArray();
        $customPaths = session("custom_paths_{$userId}", []);
        $rootPaths   = UserFile::ROOT_FOLDERS;

        return array_values(array_unique(array_merge($rootPaths, $dbPaths, $customPaths)));
    }

    /**
     * Stats (file count + total size) per path
     */
    private function getPathStats(int $userId): array
    {
        $rows = UserFile::where('user_id', $userId)
            ->selectRaw('path, COUNT(*) as cnt, SUM(size) as total_size')
            ->groupBy('path')
            ->get();

        $stats = [];
        foreach ($rows as $row) {
            $stats[$row->path] = [
                'count' => $row->cnt,
                'size'  => $row->total_size,
            ];
        }
        return $stats;
    }

    /**
     * Toggle public URL untuk file
     */
    public function togglePublic(UserFile $file)
    {
        $this->authorizeFile($file);
        if ($file->is_public) {
            $file->revokePublic();
            return response()->json(["success"=>true,"is_public"=>false,"public_url"=>null,"message"=>"Akses publik dicabut."]);
        } else {
            $token = $file->makePublic();
            return response()->json(["success"=>true,"is_public"=>true,"public_url"=>$file->public_url,"message"=>"Link publik aktif."]);
        }
    }

    private function authorizeFile(UserFile $file): void
    {
        if ($file->user_id !== auth()->id()) abort(403);
    }

    private function humanSize(int $bytes): string
    {
        if ($bytes < 1024)       return $bytes . ' B';
        if ($bytes < 1048576)    return round($bytes / 1024, 1) . ' KB';
        if ($bytes < 1073741824) return round($bytes / 1048576, 1) . ' MB';
        return round($bytes / 1073741824, 2) . ' GB';
    }
}
