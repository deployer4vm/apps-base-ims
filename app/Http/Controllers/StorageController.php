<?php

namespace App\Http\Controllers;

use App\Base\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * Serve tenant storage files without exposing the storage directory directly.
 */
class StorageController extends BaseController
{
    private const PUBLIC_IMAGE_PREFIXES = [
        'image/',
        'images/',
        'editor/image/',
        'editor/images/',
        'company/',
        'public/image/',
        'public/images/',
    ];

    private const INLINE_IMAGE_MIMES = [
        'image/gif',
        'image/jpeg',
        'image/png',
        'image/webp',
        'image/bmp',
        'image/x-icon',
        'image/vnd.microsoft.icon',
    ];

    public function index(Request $request)
    {
        return $this->serve($request, Storage::disk(config('filesystems.default')));
    }

    public function publicStorage(Request $request)
    {
        return $this->serve($request, Storage::disk('public'), ['company/']);
    }

    private function serve(Request $request, $disk, array $publicImagePrefixes = null)
    {
        $path = ltrim((string) $request->route('any', ''), '/');
        if (!$this->isSafePath($path) || !$disk->exists($path)) {
            abort(404);
        }

        $mime = $this->mimeType($disk, $path);
        $isPublicImage = $this->hasPublicImagePrefix(
            $path,
            $publicImagePrefixes === null ? self::PUBLIC_IMAGE_PREFIXES : $publicImagePrefixes
        )
            && in_array($mime, self::INLINE_IMAGE_MIMES, true);

        if (!$isPublicImage && !$this->isAuthenticated()) {
            abort(404);
        }

        $forceDownload = $request->boolean('download') || !$isPublicImage;

        return $this->streamFile($disk, $path, $mime, $forceDownload, $isPublicImage);
    }

    private function isSafePath($path)
    {
        if ($path === '' || strpos($path, "\0") !== false || strpos($path, '\\') !== false) {
            return false;
        }

        return !preg_match('#(^|/)\.{1,2}(/|$)#', $path);
    }

    private function hasPublicImagePrefix($path, array $prefixes)
    {
        foreach ($prefixes as $prefix) {
            if (strpos($path, $prefix) === 0) {
                return true;
            }
        }

        return false;
    }

    private function isAuthenticated()
    {
        return Auth::guard('web')->check() || Auth::guard('api')->check();
    }

    private function mimeType($disk, $path)
    {
        try {
            return (string) $disk->mimeType($path);
        } catch (\Throwable $e) {
            return 'application/octet-stream';
        }
    }

    private function streamFile($disk, $path, $mime, $download, $isPublic)
    {
        $stream = $disk->readStream($path);
        if (!is_resource($stream)) {
            abort(404);
        }

        $fileName = preg_replace('/[^A-Za-z0-9._-]/', '_', basename($path));
        $headers = [
            'Content-Type' => $mime,
            'Content-Disposition' => ($download ? 'attachment' : 'inline').'; filename="'.$fileName.'"',
            'X-Content-Type-Options' => 'nosniff',
            'Content-Security-Policy' => "default-src 'none'; sandbox",
            'Cache-Control' => $isPublic
                ? 'public, max-age=2592000, immutable'
                : 'private, no-store, max-age=0',
        ];

        return response()->stream(function () use ($stream) {
            fpassthru($stream);
            fclose($stream);
        }, 200, $headers);
    }
}
