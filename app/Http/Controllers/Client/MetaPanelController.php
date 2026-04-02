<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ApiMetaToken; // Disesuaikan dengan nama model di Service kamu
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MetaPanelController extends Controller
{
    /**
     * Tampilkan halaman Client Area (Meta Panel)
     */
    public function index()
    {
        $user = auth()->user();

        if (! $user->apiMetaToken) {
            return view('client.meta.setup');
        }

        $tokenData = $user->apiMetaToken;
        $activeSessions = []; // Siapkan array kosong sebagai default

        // Lakukan HIT API ke /list-sessions
        try {
            $baseUrl = rtrim(config('services.meta_api.url'), '/');
            $response = Http::timeout(10)
                ->withToken($tokenData->token)
                ->acceptJson()
                ->get($baseUrl.'/list-sessions');

            if ($response->successful() && $response->json('status') === 'Success') {
                $activeSessions = $response->json('sessions');
            }
        } catch (\Exception $e) {
            // Jika gagal (misal server down), biarkan $activeSessions kosong
            // Pengecekan server down sudah di-handle oleh animasi AJAX Loader yang kita buat sebelumnya
        }

        return view('client.meta.index', compact('tokenData', 'activeSessions'));
    }

    /**
     * Verifikasi dan Simpan Token
     */
    public function storeToken(Request $request)
    {
        // 1. Validasi input form
        $request->validate([
            'token' => 'required|string',
        ]);

        // 2. HIT API /status untuk mengecek keaslian token
        // Kita gunakan Http facade karena MetaApiService butuh token yang sudah terdaftar di DB
        $baseUrl = rtrim(config('services.meta_api.url'), '/');

        $response = Http::timeout(15)
            ->withToken($request->token)
            ->acceptJson()
            ->get($baseUrl.'/status');

        // 3. Jika gagal (misal 401 Unauthorized), kembalikan ke halaman setup dengan pesan error
        if ($response->failed()) {
            return back()->withErrors([
                'token' => 'Token tidak valid, expired, atau tidak ditemukan di server.',
            ])->withInput();
        }

        // 4. Jika berhasil (200 OK), simpan token ke database lokal
        $user = auth()->user();

        // Menggunakan updateOrCreate agar jika user sudah punya data lama, cukup ditimpa
        ApiMetaToken::updateOrCreate(
            ['user_id' => $user->id],
            [
                'token' => $request->token,
                'client_name' => $user->name,
                'role' => 'client',
                'is_active' => 1,
                'expired_at' => now()->addYear(), // Dummy expired karena API /status tidak me-return masa aktif token
                'sessions' => [
                    // Dummy data awal, nantinya bisa diupdate dengan HIT /list-sessions
                    ['session_name' => 'akun_1', 'created_at' => now()->toISOString()],
                    ['session_name' => 'akun_2', 'created_at' => now()->toISOString()],
                ],
            ]
        );

        return redirect()->route('client.meta.index')->with('success', 'Token Meta berhasil diverifikasi dan dihubungkan!');
    }

    /**
     * Verifikasi Token via AJAX saat masuk halaman Index
     */
    public function verifyToken()
    {
        $user = auth()->user();

        if (! $user->apiMetaToken) {
            return response()->json(['success' => false, 'message' => 'Token tidak ditemukan di database.'], 404);
        }

        $baseUrl = rtrim(config('services.meta_api.url'), '/');

        $response = Http::timeout(15)
            ->withToken($user->apiMetaToken->token)
            ->acceptJson()
            ->get($baseUrl.'/status');

        if ($response->failed()) {
            // Ambil pesan langsung dari API Postman-mu (misal: "Token expired sejak...")
            $message = $response->json('message') ?? 'Token tidak valid atau server bermasalah.';

            // Hapus token lokal yang sudah hangus agar user bisa setup ulang
            $user->apiMetaToken()->delete();

            return response()->json([
                'success' => false,
                'message' => $message,
            ], 401);
        }

        // Jika sukses (200 OK), kembalikan data statusnya sekalian
        return response()->json([
            'success' => true,
            'data' => $response->json(),
        ]);
    }

    /**
     * Hit API Node.js untuk Login via Cookies
     */
    /**
     * Hit API Node.js untuk Login via Cookies
     */
    public function loginCookies(Request $request)
    {
        $request->validate([
            'sessionName' => 'required|string',
            'cookies' => 'required', // Bisa string atau array
        ]);

        // MENGGUNAKAN input() AGAR TIDAK BENTROK DENGAN HTTP COOKIES BAWAAN LARAVEL
        $payloadCookies = $request->input('cookies');

        // Jika data dari JavaScript masih berupa String, kita bersihkan dan decode
        if (is_string($payloadCookies)) {
            $cleanJsonString = preg_replace('/[\x00-\x1F\x7F-\x9F]/u', '', $payloadCookies);
            $cookiesArray = json_decode($cleanJsonString, true);
        } else {
            // Jika data dari JS sudah otomatis di-parse jadi Array oleh Laravel
            $cookiesArray = $payloadCookies;
        }

        // Validasi apakah hasil akhirnya benar-benar Array JSON yang valid
        if (! is_array($cookiesArray)) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Format JSON tidak valid. Pastikan Anda meng-copy utuh dari Cookie Editor.',
            ], 400);
        }

        $user = auth()->user();
        $baseUrl = rtrim(config('services.meta_api.url'), '/');

        try {
            $response = Http::timeout(60)
                ->withToken($user->apiMetaToken->token)
                ->acceptJson()
                ->post($baseUrl.'/login-cookies', [
                    'sessionName' => $request->sessionName,
                    'cookies' => $cookiesArray, // Kirim array yang sudah valid ke Node.js
                ]);

            return response()->json($response->json(), $response->status());

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Gagal terhubung ke server Bot Meta utama: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Tampilkan halaman Asset ID (Facebook Pages)
     */
    public function assets(Request $request)
    {
        $user = auth()->user();

        if (! $user->apiMetaToken) {
            return redirect()->route('client.meta.setup');
        }

        $tokenData = $user->apiMetaToken;
        $sessions = $tokenData->sessions ?? [];

        // Ambil assets dari DB, group by session_name
        $assets = \App\Models\MetaAsset::where('user_id', $user->id)
            ->orderBy('session_name')
            ->orderBy('page_name')
            ->get()
            ->groupBy('session_name');

        return view('client.meta.assets', compact('tokenData', 'sessions', 'assets'));
    }

    /**
     * Sync Asset ID dari API /check-business → simpan ke DB
     */
    public function syncAssets(Request $request)
    {
        $request->validate([
            'session_name' => 'required|string',
        ]);

        $user = auth()->user();
        $sessionName = $request->session_name;
        $baseUrl = rtrim(config('services.meta_api.url'), '/');

        try {
            $response = \Illuminate\Support\Facades\Http::timeout(30)
                ->withToken($user->apiMetaToken->token)
                ->acceptJson()
                ->post($baseUrl.'/check-business', [
                    'sessionName' => $sessionName,
                ]);

            if ($response->failed()) {
                return response()->json([
                    'success' => false,
                    'message' => $response->json('message') ?? 'Gagal menghubungi server Bot Meta.',
                ], $response->status());
            }

            $data = $response->json();
            $pages = $data['pages'] ?? $data['data'] ?? [];

            if (empty($pages)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada page ditemukan dari sesi ini.',
                ]);
            }

            $synced = 0;
            $newCount = 0;

            foreach ($pages as $page) {
                $assetId = $page['asset_id'] ?? $page['id'] ?? null;
                $pageName = $page['page_name'] ?? $page['name'] ?? null;

                if (! $assetId) {
                    continue;
                }

                $existed = \App\Models\MetaAsset::where('user_id', $user->id)
                    ->where('session_name', $sessionName)
                    ->where('asset_id', $assetId)
                    ->exists();

                \App\Models\MetaAsset::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'session_name' => $sessionName,
                        'asset_id' => $assetId,
                    ],
                    [
                        'page_name' => $pageName,
                        'category' => $page['category'] ?? null,
                        'picture' => $page['picture'] ?? null,
                        'raw_data' => $page,
                    ]
                );

                if (! $existed) {
                    $newCount++;
                }
                $synced++;
            }

            return response()->json([
                'success' => true,
                'message' => "Sync berhasil! {$synced} asset ditemukan, {$newCount} baru ditambahkan.",
                'synced' => $synced,
                'new' => $newCount,
                'session' => $sessionName,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Hapus satu asset dari DB
     */
    public function deleteAsset(Request $request, $id)
    {
        $asset = \App\Models\MetaAsset::where('user_id', auth()->id())->findOrFail($id);
        $asset->delete();

        return response()->json(['success' => true, 'message' => 'Asset berhasil dihapus.']);
    }

    /**
     * Ambil assets dari DB berdasarkan session — dipanggil via AJAX saat pilih sesi
     */
    public function assetsBySession(Request $request)
    {
        $sessionName = $request->query('session');

        if (! $sessionName) {
            return response()->json(['assets' => []]);
        }

        $assets = \App\Models\MetaAsset::where('user_id', auth()->id())
            ->where('session_name', $sessionName)
            ->orderBy('page_name')
            ->get(['id', 'asset_id', 'page_name', 'category', 'picture', 'updated_at']);

        return response()->json(['assets' => $assets]);
    }

    /**
     * Ambil posts dari DB berdasarkan session + asset — dipanggil via AJAX
     */
    public function postsByAsset(Request $request)
    {
        $sessionName = $request->query('session');
        $assetId = $request->query('asset_id');

        if (! $sessionName || ! $assetId) {
            return response()->json(['posts' => [], 'stats' => []]);
        }

        $posts = \App\Models\MetaPost::where('user_id', auth()->id())
            ->where('session_name', $sessionName)
            ->where('asset_id', $assetId)
            ->orderByRaw("FIELD(status, 'scheduled', 'published', 'failed')")
            ->orderBy('created_at', 'desc')
            ->get();

        $stats = [
            'total' => $posts->count(),
            'scheduled' => $posts->where('status', 'scheduled')->count(),
            'published' => $posts->where('status', 'published')->count(),
            'failed' => $posts->where('status', 'failed')->count(),
        ];

        return response()->json([
            'posts' => $posts,
            'stats' => $stats,
        ]);
    }

    /**
     * Sync posts dari API /check-posts → simpan ke DB
     *
     * Format response API (exact):
     * {
     *   "status": "Success",
     *   "sessionName": "syifahmelina455",
     *   "assetId": "951949034669021",
     *   "scheduled": {
     *     "total": 0,
     *     "url": "https://business.facebook.com/...",
     *     "data": []
     *   },
     *   "published": {
     *     "total": 19,
     *     "url": "https://business.facebook.com/...",
     *     "data": [
     *       {
     *         "title": "Baris pertama...",
     *         "caption": "Open Drop-down",
     *         "date": "28 February 10:22",       <-- string, bukan timestamp
     *         "status": "Published",              <-- "Published" atau "Scheduled" (kapital)
     *         "reach": "0",                       <-- string angka
     *         "likes_reactions": "0",
     *         "comments": "0",
     *         "shares": "0",
     *         "post_url": null                    <-- bisa null
     *       }
     *     ]
     *   }
     * }
     */
    public function syncPosts(Request $request)
    {
        $request->validate([
            'session_name' => 'required|string',
            'asset_id' => 'required|string',
        ]);

        $user = auth()->user();
        $sessionName = $request->session_name;
        $assetId = $request->asset_id;
        $baseUrl = rtrim(config('services.meta_api.url'), '/');

        try {
            // /check-posts bisa lambat ~10-20 detik (scraping FB Business)
            $response = \Illuminate\Support\Facades\Http::timeout(90)
                ->withToken($user->apiMetaToken->token)
                ->acceptJson()
                ->post($baseUrl.'/check-posts', [
                    'sessionName' => $sessionName,
                    'assetId' => $assetId,
                ]);

            if ($response->failed()) {
                return response()->json([
                    'success' => false,
                    'message' => $response->json('message') ?? 'Gagal menghubungi server Bot Meta.',
                ], $response->status());
            }

            $data = $response->json();

            // Cek status dari API
            if (($data['status'] ?? '') !== 'Success') {
                return response()->json([
                    'success' => false,
                    'message' => $data['message'] ?? 'API mengembalikan status gagal.',
                ]);
            }

            // Ambil data dari kedua bucket — persis sesuai struktur API
            // scheduled.data[] dan published.data[]
            $scheduledItems = $data['scheduled']['data'] ?? [];
            $publishedItems = $data['published']['data'] ?? [];

            $totalFromApi = count($scheduledItems) + count($publishedItems);

            if ($totalFromApi === 0) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tidak ada post ditemukan dari page ini.',
                    'synced' => 0,
                    'new' => 0,
                    'scheduled' => 0,
                    'published' => 0,
                ]);
            }

            $synced = 0;
            $newCount = 0;

            // Proses scheduled posts
            foreach ($scheduledItems as $post) {
                [$existed] = $this->upsertPost($user->id, $sessionName, $assetId, $post, 'scheduled');
                if (! $existed) {
                    $newCount++;
                }
                $synced++;
            }

            // Proses published posts
            foreach ($publishedItems as $post) {
                [$existed] = $this->upsertPost($user->id, $sessionName, $assetId, $post, 'published');
                if (! $existed) {
                    $newCount++;
                }
                $synced++;
            }

            return response()->json([
                'success' => true,
                'message' => "{$synced} post ditemukan, {$newCount} baru disimpan.",
                'synced' => $synced,
                'new' => $newCount,
                'scheduled' => count($scheduledItems),
                'published' => count($publishedItems),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Helper: upsert satu post ke DB, return [existed (bool)]
     *
     * Field mapping dari response API:
     *   title            → title
     *   caption          → caption          ("Open Drop-down")
     *   date             → post_date        ("28 February 10:22")
     *   status           → status           (lowercase: "published"/"scheduled")
     *   reach            → reach            (cast ke int)
     *   likes_reactions  → likes_reactions  (cast ke int)
     *   comments         → comments         (cast ke int)
     *   shares           → shares           (cast ke int)
     *   post_url         → post_url         (bisa null)
     */
    private function upsertPost(int $userId, string $sessionName, string $assetId, array $post, string $statusBucket): array
    {
        $title = trim($post['title'] ?? '');
        $date = trim($post['date'] ?? '');
        $status = $statusBucket; // 'scheduled' atau 'published' — dari bucket, bukan field status

        // Hash unik karena API tidak return post_id
        // Kombinasi: session + asset + title + date
        $hash = md5($sessionName.'|'.$assetId.'|'.$title.'|'.$date);

        $existed = \App\Models\MetaPost::where('user_id', $userId)
            ->where('session_name', $sessionName)
            ->where('asset_id', $assetId)
            ->where('post_hash', $hash)
            ->exists();

        \App\Models\MetaPost::updateOrCreate(
            [
                'user_id' => $userId,
                'session_name' => $sessionName,
                'asset_id' => $assetId,
                'post_hash' => $hash,
            ],
            [
                'title' => $title,
                'caption' => $post['caption'] ?? null,
                'post_date' => $date,
                'status' => $status,
                'post_url' => $post['post_url'] ?: null, // null jika kosong
                'reach' => (int) ($post['reach'] ?? 0),
                'likes_reactions' => (int) ($post['likes_reactions'] ?? 0),
                'comments' => (int) ($post['comments'] ?? 0),
                'shares' => (int) ($post['shares'] ?? 0),
                'raw_data' => $post,
            ]
        );

        return [$existed];
    }

    // Tambahkan method ini di MetaPanelController.php

    /**
     * Schedule konten ke API Bot Meta
     */
    public function schedulePost(Request $request)
    {
        $request->validate([
            'session_name' => 'required|string',
            'asset_id' => 'required|string',
            'file_path' => 'required|string',
            'caption' => 'nullable|string',
            'date' => 'required|string|regex:/^\d{2}\/\d{2}\/\d{4}$/', // DD/MM/YYYY
            'hour' => 'required|integer|min:0|max:23',
        ], [
            'date.regex' => 'Format tanggal harus DD/MM/YYYY (contoh: 28/03/2026)',
            'hour.min' => 'Jam harus antara 0-23',
            'hour.max' => 'Jam harus antara 0-23',
        ]);

        $user = auth()->user();
        $baseUrl = rtrim(config('services.meta_api.url'), '/');

        // Validasi tanggal tidak di masa lalu
        try {
            $scheduledDate = \Carbon\Carbon::createFromFormat('d/m/Y H:i', $request->date.' '.$request->hour.':00');
            if ($scheduledDate->isPast()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Waktu jadwal sudah terlewat. Pilih waktu di masa depan.',
                ], 422);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Format tanggal tidak valid.',
            ], 422);
        }

        try {
            $response = \Illuminate\Support\Facades\Http::timeout(30)
                ->withToken($user->apiMetaToken->token)
                ->acceptJson()
                ->post($baseUrl.'/schedule', [
                    'sessionName' => $request->session_name,
                    'tasks' => [
                        [
                            'assetId' => $request->asset_id,
                            'filePath' => $request->file_path,
                            'caption' => $request->caption ?? '',
                            'date' => $request->date,
                            'hour' => (string) $request->hour,
                        ],
                    ],
                ]);

            $data = $response->json();

            // Handle response berdasarkan status code
            if ($response->status() === 202) {
                // Sukses — masuk antrian
                return response()->json([
                    'success' => true,
                    'status' => $data['status'] ?? 'Queued',
                    'batch_id' => $data['batchId'] ?? null,
                    'position' => $data['queue_position'] ?? null,
                    'message' => $data['message'] ?? 'Konten berhasil masuk antrian!',
                ]);
            }

            if ($response->status() === 422) {
                // Validasi gagal dari API
                $errors = $data['errors'] ?? [];
                $errorMessages = collect($errors)->pluck('message')->implode(', ');

                return response()->json([
                    'success' => false,
                    'message' => $errorMessages ?: ($data['message'] ?? 'Data tidak valid.'),
                    'errors' => $errors,
                ], 422);
            }

            // Error lainnya
            return response()->json([
                'success' => false,
                'message' => $data['message'] ?? 'Gagal menjadwalkan konten.',
            ], $response->status());

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server Bot Meta tidak dapat dihubungi. Pastikan server berjalan.',
            ], 503);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Cek status antrian bot
     */
    public function checkQueueStatus()
    {
        $user = auth()->user();
        $baseUrl = rtrim(config('services.meta_api.url'), '/');

        try {
            $response = \Illuminate\Support\Facades\Http::timeout(10)
                ->withToken($user->apiMetaToken->token)
                ->acceptJson()
                ->get($baseUrl.'/status');

            return response()->json($response->json(), $response->status());

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil status antrian.',
            ], 500);
        }
    }
}
