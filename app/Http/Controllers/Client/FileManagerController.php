<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\UserFile;
use App\Models\UserFolder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileManagerController extends Controller
{
    // ── Helpers ────────────────────────────────────────────────────────

    private function folderPath(UserFolder $folder): string
    {
        return $folder->full_path;
    }

    private function storagePath(int $userId, ?UserFolder $folder, string $filename): string
    {
        $sub = $folder ? $folder->full_path : 'root';
        return "users/{$userId}/{$sub}/{$filename}";
    }

    private function buildTree(int $userId, ?int $parentId = null): array
    {
        $folders = UserFolder::where('user_id', $userId)
            ->where('parent_id', $parentId)
            ->orderBy('name')
            ->get();

        return $folders->map(function ($f) use ($userId) {
            return [
                'id'       => $f->id,
                'name'     => $f->name,
                'children' => $this->buildTree($userId, $f->id),
                'count'    => UserFile::where('folder_id', $f->id)->count(),
            ];
        })->toArray();
    }

    private function fileToArray(UserFile $file): array
    {
        return [
            'id'            => $file->id,
            'original_name' => $file->original_name,
            'extension'     => $file->extension,
            'type'          => $file->type,
            'size'          => $file->size,
            'human_size'    => $file->human_size,
            'description'   => $file->description,
            'created_at'    => $file->created_at->format('d M Y, H:i'),
            'download_url'  => $file->download_url,
            'public_url'    => $file->public_url,
            'is_public'     => $file->is_public,
        ];
    }

    // ── Pages ──────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $user = auth()->user();
        $folderTree = $this->buildTree($user->id);

        // Root files (no folder)
        $files = UserFile::where('user_id', $user->id)
            ->whereNull('folder_id')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($f) => $this->fileToArray($f));

        $totalSize = UserFile::where('user_id', $user->id)->sum('size');

        return view('client.files.index', compact('folderTree', 'files', 'totalSize'));
    }

    // ── AJAX ───────────────────────────────────────────────────────────

    public function getFolderContents(Request $request)
    {
        $user     = auth()->user();
        $folderId = $request->input('folder_id'); // null = root

        if ($folderId) {
            $folder = UserFolder::where('id', $folderId)->where('user_id', $user->id)->firstOrFail();
            $breadcrumb = $folder->breadcrumb;
        } else {
            $folder     = null;
            $breadcrumb = [];
        }

        $files = UserFile::where('user_id', $user->id)
            ->where('folder_id', $folderId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($f) => $this->fileToArray($f));

        $subfolders = UserFolder::where('user_id', $user->id)
            ->where('parent_id', $folderId)
            ->orderBy('name')
            ->get()
            ->map(fn($f) => [
                'id'    => $f->id,
                'name'  => $f->name,
                'count' => UserFile::where('folder_id', $f->id)->count(),
            ]);

        return response()->json([
            'files'      => $files,
            'subfolders' => $subfolders,
            'breadcrumb' => $breadcrumb,
            'folder_id'  => $folderId,
        ]);
    }

    // ── Upload ─────────────────────────────────────────────────────────

    public function upload(Request $request)
    {
        $request->validate([
            'files'     => 'required|array|max:20',
            'files.*'   => 'required|file|max:512000',
            'folder_id' => 'nullable|integer',
        ]);

        $user     = auth()->user();
        $folderId = $request->input('folder_id') ?: null;

        if ($folderId) {
            $folder = UserFolder::where('id', $folderId)->where('user_id', $user->id)->firstOrFail();
        } else {
            $folder = null;
        }

        $uploaded = [];
        foreach ($request->file('files') as $file) {
            $ext        = strtolower($file->getClientOriginalExtension());
            $stored     = $file->getClientOriginalName(); // pakai nama asli
            $path       = $this->storagePath($user->id, $folder, $stored);

            Storage::disk('local')->put($path, file_get_contents($file));

            $record = UserFile::create([
                'user_id'       => $user->id,
                'folder_id'     => $folderId,
                'original_name' => $file->getClientOriginalName(),
                'stored_name'   => $stored,
                'mime_type'     => $file->getMimeType(),
                'size'          => $file->getSize(),
                'extension'     => $ext,
            ]);

            // Langsung generate public URL
            $record->makePublic();
            $record->refresh();

            $uploaded[] = $this->fileToArray($record);
        }

        return response()->json([
            'success' => true,
            'message' => count($uploaded) . ' file berhasil diupload.',
            'files'   => $uploaded,
        ]);
    }

    // ── Download ───────────────────────────────────────────────────────

    public function download(UserFile $file)
    {
        abort_if($file->user_id !== auth()->id(), 403);

        $path = $this->storagePath($file->user_id, $file->folder, $file->stored_name);
        abort_unless(Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->download($path, $file->original_name);
    }

    // ── Delete ─────────────────────────────────────────────────────────

    public function destroy(UserFile $file)
    {
        abort_if($file->user_id !== auth()->id(), 403);

        $path = $this->storagePath($file->user_id, $file->folder, $file->stored_name);
        Storage::disk('local')->delete($path);
        $file->delete();

        return response()->json(['success' => true]);
    }

    // ── Toggle Public ──────────────────────────────────────────────────

    public function togglePublic(UserFile $file)
    {
        abort_if($file->user_id !== auth()->id(), 403);

        if ($file->is_public) {
            $file->revokePublic();
        } else {
            $file->makePublic();
        }

        $file->refresh(); // pastikan data terbaru

        return response()->json([
            'success'    => true,
            'is_public'  => $file->is_public,
            'public_url' => $file->public_url,
        ]);
    }

    // ── Create Folder ──────────────────────────────────────────────────

    public function createFolder(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:60|regex:/^[a-zA-Z0-9_\-]+$/',
            'parent_id' => 'nullable|integer',
        ]);

        $user     = auth()->user();
        $parentId = $request->input('parent_id') ?: null;

        if ($parentId) {
            UserFolder::where('id', $parentId)->where('user_id', $user->id)->firstOrFail();
        }

        $exists = UserFolder::where('user_id', $user->id)
            ->where('parent_id', $parentId)
            ->where('name', $request->name)
            ->exists();

        if ($exists) {
            return response()->json(['success' => false, 'message' => 'Folder sudah ada.'], 422);
        }

        $folder = UserFolder::create([
            'user_id'   => $user->id,
            'parent_id' => $parentId,
            'name'      => $request->name,
        ]);

        return response()->json([
            'success' => true,
            'folder'  => ['id' => $folder->id, 'name' => $folder->name, 'count' => 0],
        ]);
    }

    // ── Update (rename + description) ──────────────────────────────────

    public function update(Request $request, UserFile $file)
    {
        abort_if($file->user_id !== auth()->id(), 403);

        $request->validate([
            'original_name' => 'required|string|max:255',
            'description'   => 'nullable|string|max:500',
        ]);

        $file->update([
            'original_name' => $request->original_name,
            'description'   => $request->description,
        ]);

        return response()->json(['success' => true]);
    }

    // ── Delete Folder ──────────────────────────────────────────────────

    public function destroyFolder(UserFolder $folder)
    {
        abort_if($folder->user_id !== auth()->id(), 403);

        // Delete all files inside recursively
        $this->deleteFolderContents($folder);
        $folder->delete();

        return response()->json(['success' => true]);
    }

    private function deleteFolderContents(UserFolder $folder): void
    {
        foreach ($folder->files as $file) {
            $path = $this->storagePath($file->user_id, $file->folder, $file->stored_name);
            Storage::disk('local')->delete($path);
            $file->delete();
        }
        foreach ($folder->children as $child) {
            $this->deleteFolderContents($child);
            $child->delete();
        }
    }

    // ── Public Serve ───────────────────────────────────────────────────

    public function adminIndex()
    {
        return view('admin.files.index');
    }
}
