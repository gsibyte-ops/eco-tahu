<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Edukasi;
use App\Support\MediaGuard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class EdukasiController extends Controller
{
    public function index(Request $request)
    {
        $query = Edukasi::with('user')->withCount('likes')->orderByDesc('id');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('q')) {
            $query->where('judul', 'like', '%' . $request->q . '%');
        }

        $edukasi = $query->paginate(10)->withQueryString();

        $stats = [
            'total'     => Edukasi::count(),
            'publish'   => Edukasi::where('status', 'publish')->count(),
            'scheduled' => Edukasi::where('status', 'scheduled')->count(),
            'draft'     => Edukasi::where('status', 'draft')->count(),
        ];

        $edukasiJson = $edukasi->getCollection()->map(fn ($e) => [
            'id'                => $e->id,
            'judul'             => $e->judul,
            'konten'            => $e->konten,
            'status'            => $e->status,
            'media_type'        => $e->media_type ?? 'image',
            'thumbnail'         => $e->thumbnail_url,
            'video_url'         => $e->video_url,
            'youtube_url'       => $e->youtube_url,
            'youtube_embed_url' => $e->youtube_embed_url,
            'scheduled_at'      => $e->scheduled_at?->format('Y-m-d\TH:i'),
        ]);

        return view('admin.edukasi.index', compact('edukasi', 'stats', 'edukasiJson'));
    }

    public function create()
    {
        return redirect()->route('admin.edukasi.index');
    }

    public function store(Request $request)
    {
        $mediaType = $request->input('media_type', 'image');

        $validator = Validator::make($request->all(), [
            'judul' => [
                'required', 'string', 'min:5', 'max:200',
                'regex:/^[a-zA-Z0-9\s.,:!?()\-]+$/',
                Rule::unique('edukasi', 'judul'),
            ],
            'konten'      => 'required|string|min:20|max:10000',
            'media_type'  => 'required|in:image,video,youtube',
            'thumbnail'   => MediaGuard::imageRules(false, 2048),
            'video_file'  => $mediaType === 'video' ? MediaGuard::videoRules(true, 20480) : MediaGuard::videoRules(false, 20480),
            'youtube_url' => [$mediaType === 'youtube' ? 'required' : 'nullable', 'string', 'max:255', MediaGuard::youtubeRule()],
            'status'      => 'required|in:draft,scheduled,publish',
            'scheduled_at' => 'nullable|required_if:status,scheduled|date|after:+5 minutes',
        ], [
            'judul.required'           => 'Judul artikel wajib diisi.',
            'judul.regex'              => 'Judul artikel hanya boleh huruf, angka, spasi, dan tanda baca umum.',
            'judul.unique'             => 'Judul artikel ini sudah ada. Gunakan judul lain.',
            'judul.min'                => 'Judul artikel minimal 5 karakter.',
            'judul.max'                => 'Judul artikel maksimal 200 karakter.',
            'konten.required'          => 'Konten artikel wajib diisi.',
            'konten.min'               => 'Konten artikel minimal 20 karakter.',
            'konten.max'               => 'Konten artikel maksimal 10.000 karakter.',
            'media_type.required'      => 'Tipe media wajib dipilih.',
            'media_type.in'            => 'Tipe media tidak valid.',
            'thumbnail.image'          => 'Cover thumbnail harus berupa foto (JPG, PNG, WEBP).',
            'thumbnail.mimes'          => 'Format foto harus JPG, PNG, atau WEBP.',
            'thumbnail.mimetypes'      => 'File foto tidak valid.',
            'thumbnail.max'            => 'Ukuran foto maksimal 2MB.',
            'video_file.required'      => 'Video wajib diunggah saat memilih mode Upload Video.',
            'video_file.mimes'         => 'Format video harus MP4, MOV, atau WEBM.',
            'video_file.mimetypes'     => 'Hanya file video asli (MP4, MOV, WEBM) yang diperbolehkan.',
            'video_file.max'           => 'Ukuran video maksimal 20MB.',
            'youtube_url.required'     => 'Link YouTube wajib diisi saat memilih mode Link YouTube.',
            'status.required'          => 'Status wajib dipilih.',
            'status.in'                => 'Status tidak valid.',
            'scheduled_at.required_if' => 'Tanggal jadwal wajib diisi kalau status Schedule.',
            'scheduled_at.after'       => 'Jadwal minimal 5 menit dari sekarang.',
            'scheduled_at.date'        => 'Format tanggal jadwal tidak valid.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.edukasi.index')
                ->withErrors($validator)
                ->withInput()
                ->with('open_modal', 'create');
        }

        $data = $request->only(['judul', 'konten', 'status']);
        $data['user_id'] = Auth::id();
        $data['slug'] = Str::slug($request->judul) . '-' . time();
        $data['media_type'] = $mediaType;
        $data['tanggal_mengunggah'] = now();

        switch ($request->status) {
            case 'scheduled':
                $data['scheduled_at'] = $request->scheduled_at;
                $data['published_at'] = null;
                break;

            case 'publish':
                $data['scheduled_at'] = null;
                $data['published_at'] = now();
                break;

            case 'draft':
            default:
                $data['scheduled_at'] = null;
                $data['published_at'] = null;
                break;
        }

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('edukasi', 'public');
        }

        if ($mediaType === 'video' && $request->hasFile('video_file')) {
            $data['video_path'] = $request->file('video_file')->store('edukasi/videos', 'public');
            $data['youtube_url'] = null;
        } elseif ($mediaType === 'youtube') {
            $data['youtube_url'] = trim((string) $request->youtube_url);
            $data['video_path'] = null;
        } else {
            $data['video_path'] = null;
            $data['youtube_url'] = null;
        }

        Edukasi::create($data);

        return redirect()->route('admin.edukasi.index')
            ->with('success', 'Artikel edukasi berhasil ditambahkan!');
    }

    public function edit(Edukasi $edukasi)
    {
        return redirect()->route('admin.edukasi.index');
    }

    public function update(Request $request, Edukasi $edukasi)
    {
        $mediaType = $request->input('media_type', 'image');
        $needsVideo = $mediaType === 'video' && (!$edukasi->video_path || $request->input('hapus_video') == '1');

        $validator = Validator::make($request->all(), [
            'judul' => [
                'required', 'string', 'min:5', 'max:200',
                'regex:/^[a-zA-Z0-9\s.,:!?()\-]+$/',
                Rule::unique('edukasi', 'judul')->ignore($edukasi->id),
            ],
            'konten'          => 'required|string|min:20|max:10000',
            'media_type'      => 'required|in:image,video,youtube',
            'thumbnail'       => MediaGuard::imageRules(false, 2048),
            'video_file'      => $needsVideo ? MediaGuard::videoRules(true, 20480) : MediaGuard::videoRules(false, 20480),
            'youtube_url'     => [$mediaType === 'youtube' ? 'required' : 'nullable', 'string', 'max:255', MediaGuard::youtubeRule()],
            'status'          => 'required|in:draft,scheduled,publish',
            'scheduled_at'    => 'nullable|required_if:status,scheduled|date|after:+5 minutes',
            'hapus_thumbnail' => 'nullable|in:0,1',
            'hapus_video'     => 'nullable|in:0,1',
        ], [
            'judul.required'           => 'Judul artikel wajib diisi.',
            'judul.regex'              => 'Judul artikel hanya boleh huruf, angka, spasi, dan tanda baca umum.',
            'judul.unique'             => 'Judul artikel ini sudah ada. Gunakan judul lain.',
            'judul.min'                => 'Judul artikel minimal 5 karakter.',
            'judul.max'                => 'Judul artikel maksimal 200 karakter.',
            'konten.required'          => 'Konten artikel wajib diisi.',
            'konten.min'               => 'Konten artikel minimal 20 karakter.',
            'konten.max'               => 'Konten artikel maksimal 10.000 karakter.',
            'thumbnail.image'          => 'Cover thumbnail harus berupa foto (JPG, PNG, WEBP).',
            'thumbnail.mimes'          => 'Format foto harus JPG, PNG, atau WEBP.',
            'thumbnail.mimetypes'      => 'File foto tidak valid.',
            'thumbnail.max'            => 'Ukuran foto maksimal 2MB.',
            'video_file.required'      => 'Video wajib diunggah saat memilih mode Upload Video.',
            'video_file.mimes'         => 'Format video harus MP4, MOV, atau WEBM.',
            'video_file.mimetypes'     => 'Hanya file video asli (MP4, MOV, WEBM) yang diperbolehkan.',
            'video_file.max'           => 'Ukuran video maksimal 20MB.',
            'youtube_url.required'     => 'Link YouTube wajib diisi saat memilih mode Link YouTube.',
            'status.required'          => 'Status wajib dipilih.',
            'status.in'                => 'Status tidak valid.',
            'scheduled_at.required_if' => 'Tanggal jadwal wajib diisi kalau status Schedule.',
            'scheduled_at.after'       => 'Jadwal minimal 5 menit dari sekarang.',
            'scheduled_at.date'        => 'Format tanggal jadwal tidak valid.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.edukasi.index')
                ->withErrors($validator)
                ->withInput()
                ->with('open_modal', 'edit')
                ->with('edit_id', $edukasi->id);
        }

        $data = $request->only(['judul', 'konten', 'status']);
        $data['slug'] = Str::slug($request->judul) . '-' . time();
        $data['media_type'] = $mediaType;

        switch ($request->status) {
            case 'scheduled':
                $data['scheduled_at'] = $request->scheduled_at;
                $data['published_at'] = null;
                break;

            case 'publish':
                $data['scheduled_at'] = null;
                $data['published_at'] = $edukasi->published_at ?? now();
                break;

            case 'draft':
            default:
                $data['scheduled_at'] = null;
                $data['published_at'] = null;
                break;
        }

        if ($request->hasFile('thumbnail')) {
            if ($edukasi->thumbnail && Storage::disk('public')->exists($edukasi->thumbnail)) {
                Storage::disk('public')->delete($edukasi->thumbnail);
            }
            $data['thumbnail'] = $request->file('thumbnail')->store('edukasi', 'public');
        } elseif ($request->input('hapus_thumbnail') == '1') {
            if ($edukasi->thumbnail && Storage::disk('public')->exists($edukasi->thumbnail)) {
                Storage::disk('public')->delete($edukasi->thumbnail);
            }
            $data['thumbnail'] = null;
        }

        if ($mediaType === 'video') {
            $data['youtube_url'] = null;
            if ($request->hasFile('video_file')) {
                if ($edukasi->video_path && Storage::disk('public')->exists($edukasi->video_path)) {
                    Storage::disk('public')->delete($edukasi->video_path);
                }
                $data['video_path'] = $request->file('video_file')->store('edukasi/videos', 'public');
            }
        } elseif ($mediaType === 'youtube') {
            if ($edukasi->video_path && Storage::disk('public')->exists($edukasi->video_path)) {
                Storage::disk('public')->delete($edukasi->video_path);
            }
            $data['video_path'] = null;
            $data['youtube_url'] = trim((string) $request->youtube_url);
        } else {
            if ($edukasi->video_path && Storage::disk('public')->exists($edukasi->video_path)) {
                Storage::disk('public')->delete($edukasi->video_path);
            }
            $data['video_path'] = null;
            $data['youtube_url'] = null;
        }

        $edukasi->update($data);

        return redirect()->route('admin.edukasi.index')
            ->with('success', 'Artikel edukasi berhasil diupdate!');
    }

    public function destroy(Edukasi $edukasi)
    {
        if ($edukasi->thumbnail && Storage::disk('public')->exists($edukasi->thumbnail)) {
            Storage::disk('public')->delete($edukasi->thumbnail);
        }
        if ($edukasi->video_path && Storage::disk('public')->exists($edukasi->video_path)) {
            Storage::disk('public')->delete($edukasi->video_path);
        }

        $edukasi->delete();

        return redirect()->route('admin.edukasi.index')
            ->with('success', 'Artikel edukasi berhasil dihapus!');
    }
}
