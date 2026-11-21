<?php

namespace App\Support;

use Closure;
use Illuminate\Http\UploadedFile;

class MediaGuard
{
    public const IMAGE_EXTS = ['jpg', 'jpeg', 'png', 'webp'];
    public const IMAGE_MIMES = ['image/jpeg', 'image/png', 'image/webp'];

    public const VIDEO_EXTS = ['mp4', 'mov', 'webm'];
    public const VIDEO_MIMES = ['video/mp4', 'video/quicktime', 'video/webm'];

    /**
     * Strict validation rules for photo-only uploads.
     */
    public static function imageRules(bool $required = false, int $maxKb = 2048): array
    {
        return [
            $required ? 'required' : 'nullable',
            'file',
            'image',
            'mimes:jpg,jpeg,png,webp',
            'mimetypes:image/jpeg,image/png,image/webp',
            "max:{$maxKb}",
        ];
    }

    /**
     * Strict validation rules for video-only uploads.
     */
    public static function videoRules(bool $required = false, int $maxKb = 20480): array
    {
        return [
            $required ? 'required' : 'nullable',
            'file',
            'mimes:mp4,mov,webm',
            'mimetypes:video/mp4,video/quicktime,video/webm',
            "max:{$maxKb}",
        ];
    }

    /**
     * Strict validation rules allowing ONLY photo (JPG/PNG/WEBP) or video (MP4/MOV/WEBM).
     */
    public static function photoOrVideoRules(bool $required = false, int $maxImageKb = 5120, int $maxVideoKb = 20480): array
    {
        return [
            $required ? 'required' : 'nullable',
            'file',
            function (string $attribute, mixed $value, Closure $fail) use ($maxImageKb, $maxVideoKb) {
                if (!($value instanceof UploadedFile) || !$value->isValid()) {
                    $fail('File yang diunggah tidak valid.');
                    return;
                }

                $mime = strtolower((string) $value->getMimeType());
                $ext = strtolower((string) $value->getClientOriginalExtension());

                if (in_array($mime, self::IMAGE_MIMES, true) && in_array($ext, self::IMAGE_EXTS, true)) {
                    if ($value->getSize() > $maxImageKb * 1024) {
                        $fail('Ukuran foto maksimal ' . ($maxImageKb / 1024) . 'MB.');
                    }
                    return;
                }

                if (in_array($mime, self::VIDEO_MIMES, true) && in_array($ext, self::VIDEO_EXTS, true)) {
                    if ($value->getSize() > $maxVideoKb * 1024) {
                        $fail('Ukuran video maksimal ' . ($maxVideoKb / 1024) . 'MB.');
                    }
                    return;
                }

                $fail('Hanya file foto (JPG, PNG, WEBP) atau video (MP4, MOV, WEBM) yang diperbolehkan.');
            },
        ];
    }

    /**
     * Detect whether an uploaded file is a validated image or video.
     */
    public static function detectMediaType(UploadedFile $file): ?string
    {
        $mime = strtolower((string) $file->getMimeType());
        if (in_array($mime, self::IMAGE_MIMES, true)) {
            return 'image';
        }
        if (in_array($mime, self::VIDEO_MIMES, true)) {
            return 'video';
        }
        return null;
    }

    /**
     * Inspect base64-encoded image data and verify real binary magic MIME type.
     */
    public static function validateBase64Image(string $base64Data, int $maxBytes = 2097152): array
    {
        $clean = trim($base64Data);
        if (str_starts_with($clean, 'data:')) {
            if (!preg_match('/^data:image\/(jpeg|jpg|png|webp);base64,/i', $clean)) {
                return [
                    'valid' => false,
                    'message' => 'Hanya file gambar asli (JPG, PNG, WEBP) yang diperbolehkan.',
                ];
            }
            $clean = substr($clean, strpos($clean, ',') + 1);
        }

        $clean = preg_replace('/\s+/', '', $clean);
        $binary = base64_decode((string) $clean, true);

        if ($binary === false || $binary === '') {
            return [
                'valid' => false,
                'message' => 'Data gambar tidak valid.',
            ];
        }

        if (strlen($binary) > $maxBytes) {
            $maxMb = round($maxBytes / (1024 * 1024), 1);
            return [
                'valid' => false,
                'message' => "Ukuran foto maksimal {$maxMb}MB.",
            ];
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = strtolower((string) $finfo->buffer($binary));

        $extMap = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
        ];

        if (!isset($extMap[$mime])) {
            return [
                'valid' => false,
                'message' => 'Hanya file gambar asli (JPG, PNG, WEBP) yang diperbolehkan.',
            ];
        }

        return [
            'valid'  => true,
            'binary' => $binary,
            'ext'    => $extMap[$mime],
            'mime'   => $mime,
        ];
    }

    /**
     * Extract an 11-character YouTube video ID ONLY from official youtube.com / youtu.be URLs.
     */
    public static function extractYoutubeId(?string $url): ?string
    {
        if (!$url) {
            return null;
        }

        $url = trim($url);
        if ($url === '') {
            return null;
        }

        if (!preg_match('#^https?://#i', $url)) {
            return null;
        }

        $parts = parse_url($url);
        if (!$parts || empty($parts['host'])) {
            return null;
        }

        $host = strtolower($parts['host']);
        $allowedHosts = ['youtube.com', 'www.youtube.com', 'm.youtube.com', 'youtu.be'];
        if (!in_array($host, $allowedHosts, true)) {
            return null;
        }

        $path = $parts['path'] ?? '';

        if ($host === 'youtu.be') {
            if (preg_match('#^/([A-Za-z0-9_-]{11})(/.*)?$#', $path, $m)) {
                return $m[1];
            }
            return null;
        }

        if (preg_match('#^/(shorts|embed)/([A-Za-z0-9_-]{11})(/.*)?$#', $path, $m)) {
            return $m[2];
        }

        if (!empty($parts['query'])) {
            parse_str($parts['query'], $query);
            if (!empty($query['v']) && preg_match('/^[A-Za-z0-9_-]{11}$/', (string) $query['v'])) {
                return (string) $query['v'];
            }
        }

        return null;
    }

    /**
     * Closure rule that strictly accepts official YouTube links only.
     */
    public static function youtubeRule(): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail) {
            if ($value !== null && trim((string) $value) !== '' && self::extractYoutubeId((string) $value) === null) {
                $fail('Hanya link YouTube resmi (youtube.com atau youtu.be) yang diperbolehkan.');
            }
        };
    }

    public static function toYoutubeEmbedUrl(?string $url): ?string
    {
        $id = self::extractYoutubeId($url);
        return $id ? "https://www.youtube.com/embed/{$id}" : null;
    }

    public static function toYoutubeThumbnailUrl(?string $url): ?string
    {
        $id = self::extractYoutubeId($url);
        return $id ? "https://img.youtube.com/vi/{$id}/hqdefault.jpg" : null;
    }

    public static function toFileUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }
        $clean = ltrim(str_replace(['storage/', 'public/'], '', $path), '/');
        return url('/files/' . $clean);
    }
}
