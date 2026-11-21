<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FileProxyController extends Controller
{
    /**
     * Serve public media files for Flutter Mobile / CORS clients safely.
     * Enforces strict realpath() containment inside storage/app/public or public/images
     * to prevent directory traversal and sensitive file disclosure.
     */
    public function show(Request $request, string $path): Response
    {
        if ($request->isMethod('OPTIONS')) {
            return response('', 200, [
                'Access-Control-Allow-Origin'  => '*',
                'Access-Control-Allow-Methods' => 'GET, HEAD, OPTIONS',
                'Access-Control-Allow-Headers' => '*',
                'Access-Control-Expose-Headers' => 'Content-Length, Content-Range, Accept-Ranges',
                'Cross-Origin-Resource-Policy' => 'cross-origin',
            ]);
        }

        // Reject null bytes, traversal segments, backslashes, and hidden/code files
        if (
            str_contains($path, "\0")
            || str_contains($path, '..')
            || str_contains($path, '\\')
        ) {
            abort(403, 'Akses file ditolak.');
        }

        $cleanPath = ltrim(preg_replace('#^(storage|public)/#', '', $path), '/');
        if ($cleanPath === '' || str_starts_with(basename($cleanPath), '.')) {
            abort(404);
        }

        // Only allow safe static media extensions
        $ext = strtolower(pathinfo($cleanPath, PATHINFO_EXTENSION));
        $allowedExt = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg', 'mp4', 'webm', 'mov', 'pdf'];
        if (!in_array($ext, $allowedExt, true)) {
            abort(403, 'Tipe file tidak diizinkan.');
        }

        $allowedRoots = array_filter([
            realpath(storage_path('app/public')),
            realpath(public_path('images')),
        ]);

        $candidates = [
            storage_path('app/public/' . $cleanPath),
            public_path($path),
        ];

        $resolvedFile = null;
        foreach ($candidates as $candidate) {
            $real = realpath($candidate);
            if ($real === false || !is_file($real)) {
                continue;
            }
            foreach ($allowedRoots as $root) {
                $prefix = rtrim($root, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
                if (str_starts_with($real, $prefix)) {
                    $resolvedFile = $real;
                    break 2;
                }
            }
        }

        if ($resolvedFile === null) {
            abort(404);
        }

        $mime = mime_content_type($resolvedFile) ?: 'application/octet-stream';

        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        return response()->file($resolvedFile, [
            'Access-Control-Allow-Origin'   => '*',
            'Access-Control-Allow-Methods'  => 'GET, HEAD, OPTIONS',
            'Access-Control-Allow-Headers'  => '*',
            'Access-Control-Expose-Headers' => 'Content-Length, Content-Range, Accept-Ranges',
            'Cross-Origin-Resource-Policy'  => 'cross-origin',
            'Cross-Origin-Embedder-Policy'  => 'unsafe-none',
            'Content-Type'                  => $mime,
            'Cache-Control'                 => 'public, max-age=86400',
        ]);
    }
}
