<?php

namespace App\Http\Controllers;

use App\Models\UserFile;
use Illuminate\Support\Facades\Storage;

class PublicFileController extends Controller
{
    /**
     * Serve file publik via token
     * URL: /files/{token}/{filename}
     */
    public function serve(string $token, string $filename)
    {
        $file = UserFile::where('public_token', $token)
            ->where('is_public', true)
            ->firstOrFail();

        // Pastikan filename cocok (keamanan extra)
        if ($file->original_name !== urldecode($filename)) {
            abort(404);
        }

        $path = $file->storage_path;

        if (!Storage::disk('local')->exists($path)) {
            abort(404);
        }

        $mime     = $file->mime_type ?: 'application/octet-stream';
        $size     = Storage::disk('local')->size($path);
        $stream   = Storage::disk('local')->readStream($path);

        return response()->stream(function () use ($stream) {
            fpassthru($stream);
        }, 200, [
            'Content-Type'        => $mime,
            'Content-Length'      => $size,
            'Content-Disposition' => 'inline; filename="' . $file->original_name . '"',
            'Cache-Control'       => 'public, max-age=86400',
            'Accept-Ranges'       => 'bytes',
        ]);
    }
}
