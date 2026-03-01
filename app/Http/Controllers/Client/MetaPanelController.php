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

        if (!$user->apiMetaToken) {
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
                ->get($baseUrl . '/list-sessions');

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
            'token' => 'required|string'
        ]);

        // 2. HIT API /status untuk mengecek keaslian token
        // Kita gunakan Http facade karena MetaApiService butuh token yang sudah terdaftar di DB
        $baseUrl = rtrim(config('services.meta_api.url'), '/');

        $response = Http::timeout(15)
            ->withToken($request->token)
            ->acceptJson()
            ->get($baseUrl . '/status');

        // 3. Jika gagal (misal 401 Unauthorized), kembalikan ke halaman setup dengan pesan error
        if ($response->failed()) {
            return back()->withErrors([
                'token' => 'Token tidak valid, expired, atau tidak ditemukan di server.'
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
                    ['session_name' => 'akun_2', 'created_at' => now()->toISOString()]
                ]
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

        if (!$user->apiMetaToken) {
            return response()->json(['success' => false, 'message' => 'Token tidak ditemukan di database.'], 404);
        }

        $baseUrl = rtrim(config('services.meta_api.url'), '/');

        $response = Http::timeout(15)
            ->withToken($user->apiMetaToken->token)
            ->acceptJson()
            ->get($baseUrl . '/status');

        if ($response->failed()) {
            // Ambil pesan langsung dari API Postman-mu (misal: "Token expired sejak...")
            $message = $response->json('message') ?? 'Token tidak valid atau server bermasalah.';

            // Hapus token lokal yang sudah hangus agar user bisa setup ulang
            $user->apiMetaToken()->delete();

            return response()->json([
                'success' => false,
                'message' => $message
            ], 401);
        }

        // Jika sukses (200 OK), kembalikan data statusnya sekalian
        return response()->json([
            'success' => true,
            'data' => $response->json()
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
            'cookies' => 'required' // Bisa string atau array
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
        if (!is_array($cookiesArray)) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Format JSON tidak valid. Pastikan Anda meng-copy utuh dari Cookie Editor.'
            ], 400);
        }

        $user = auth()->user();
        $baseUrl = rtrim(config('services.meta_api.url'), '/');

        try {
            $response = Http::timeout(60)
                ->withToken($user->apiMetaToken->token)
                ->acceptJson()
                ->post($baseUrl . '/login-cookies', [
                    'sessionName' => $request->sessionName,
                    'cookies' => $cookiesArray // Kirim array yang sudah valid ke Node.js
                ]);

            return response()->json($response->json(), $response->status());

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Gagal terhubung ke server Bot Meta utama: ' . $e->getMessage()
            ], 500);
        }
    }
}
