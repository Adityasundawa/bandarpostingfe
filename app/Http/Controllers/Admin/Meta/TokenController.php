<?php

namespace App\Http\Controllers\Admin\Meta;

use App\Http\Controllers\Controller;
use App\Services\MetaApiService;
use Illuminate\Http\Request;

class TokenController extends Controller
{
    public function __construct(protected MetaApiService $api) {}

    /*
    |--------------------------------------------------------------------------
    | Index — list semua token
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $result = $this->api->listTokens();

        $tokens = $result['success'] ? ($result['data']['data'] ?? []) : [];
        $error  = $result['success'] ? null : ($result['data']['message'] ?? 'Gagal mengambil data token.');

        return view('admin.meta.tokens.index', compact('tokens', 'error'));
    }

    /*
    |--------------------------------------------------------------------------
    | Show — detail token + sesi
    |--------------------------------------------------------------------------
    */
    public function show(int $id)
    {
        $result = $this->api->getToken($id);

        if (!$result['success']) {
            return redirect()->route('admin.meta.tokens.index')
                ->with('error', $result['data']['message'] ?? 'Token tidak ditemukan.');
        }

        $token = $result['data']['data'] ?? [];
        return view('admin.meta.tokens.show', compact('token'));
    }

    /*
    |--------------------------------------------------------------------------
    | Create form
    |--------------------------------------------------------------------------
    */
    public function create()
    {
        return view('admin.meta.tokens.create');
    }

    /*
    |--------------------------------------------------------------------------
    | Store — buat token baru
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_name' => ['required', 'string', 'max:100'],
            'role'        => ['required', 'in:admin,client'],
            'expired_at'  => ['nullable', 'date_format:Y-m-d H:i:s'],
        ], [
            'client_name.required' => 'Nama klien wajib diisi.',
            'role.required'        => 'Role wajib dipilih.',
            'expired_at.date_format'=> 'Format expired harus: YYYY-MM-DD HH:MM:SS',
        ]);

        // Buang expired_at jika kosong
        if (empty($validated['expired_at'])) {
            unset($validated['expired_at']);
        }

        $result = $this->api->createToken($validated);

        if (!$result['success']) {
            return back()->withInput()
                ->with('error', $result['data']['message'] ?? 'Gagal membuat token.');
        }

        $newToken = $result['data']['data']['token'] ?? null;

        return redirect()->route('admin.meta.tokens.index')
            ->with('success', "Token untuk '{$validated['client_name']}' berhasil dibuat!")
            ->with('new_token', $newToken); // tampilkan sekali di flash
    }

    /*
    |--------------------------------------------------------------------------
    | Update — patch token (aktif, nonaktif, perpanjang, ganti nama)
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'client_name' => ['sometimes', 'string', 'max:100'],
            'is_active'   => ['sometimes', 'boolean'],
            'expired_at'  => ['sometimes', 'nullable', 'date_format:Y-m-d H:i:s'],
        ]);

        $result = $this->api->updateToken($id, $validated);

        if (!$result['success']) {
            return back()->with('error', $result['data']['message'] ?? 'Gagal update token.');
        }

        return back()->with('success', 'Token berhasil diupdate.');
    }

    /*
    |--------------------------------------------------------------------------
    | Toggle aktif / nonaktif (AJAX-friendly, bisa juga form POST)
    |--------------------------------------------------------------------------
    */
    public function toggleActive(Request $request, int $id)
    {
        $active = (int) $request->input('is_active', 1);
        $result = $this->api->updateToken($id, ['is_active' => $active]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => $result['success'],
                'message' => $result['data']['message'] ?? ($result['success'] ? 'OK' : 'Gagal'),
            ]);
        }

        $label = $active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with(
            $result['success'] ? 'success' : 'error',
            $result['success'] ? "Token berhasil {$label}." : ($result['data']['message'] ?? 'Gagal.')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Destroy — hapus token permanen
    |--------------------------------------------------------------------------
    */
    public function destroy(int $id)
    {
        $result = $this->api->deleteToken($id);

        return redirect()->route('admin.meta.tokens.index')->with(
            $result['success'] ? 'success' : 'error',
            $result['success']
                ? 'Token berhasil dihapus.'
                : ($result['data']['message'] ?? 'Gagal menghapus token.')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Sessions — assign sesi ke token
    |--------------------------------------------------------------------------
    */
    public function assignSessions(Request $request, int $id)
    {
        $request->validate([
            'sessions'   => ['required', 'string'],
        ]);

        // Input: textarea newline-separated atau comma-separated
        $raw      = $request->input('sessions');
        $sessions = array_values(array_filter(
            array_map('trim', preg_split('/[\r\n,]+/', $raw))
        ));

        if (empty($sessions)) {
            return back()->with('error', 'Minimal satu nama sesi harus diisi.');
        }

        $result = $this->api->assignSessions($id, $sessions);

        return back()->with(
            $result['success'] ? 'success' : 'error',
            $result['success']
                ? ($result['data']['message'] ?? count($sessions) . ' sesi berhasil di-assign.')
                : ($result['data']['message'] ?? 'Gagal assign sesi.')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Sessions — cabut sesi dari token
    |--------------------------------------------------------------------------
    */
    public function revokeSession(Request $request, int $id)
    {
        $session = $request->input('session');

        if (!$session) {
            return back()->with('error', 'Nama sesi tidak boleh kosong.');
        }

        $result = $this->api->revokeSessions($id, [$session]);

        return back()->with(
            $result['success'] ? 'success' : 'error',
            $result['success']
                ? "Sesi '{$session}' berhasil dicabut."
                : ($result['data']['message'] ?? 'Gagal mencabut sesi.')
        );
    }
}
