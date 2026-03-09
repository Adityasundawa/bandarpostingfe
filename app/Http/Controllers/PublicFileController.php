<?php

namespace App\Http\Controllers;

use App\Models\UserFile;
use Illuminate\Support\Facades\Storage;

class PublicFileController extends Controller
{
    public function serve(string $token, string $filename)
    {
        $file = UserFile::where('public_token', $token)
            ->where('is_public', true)
            ->firstOrFail();

        $sub  = $file->folder ? $file->folder->full_path : 'root';
        $path = "users/{$file->user_id}/{$sub}/{$file->stored_name}";

        abort_unless(Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->download($path, $file->original_name);
    }
}
