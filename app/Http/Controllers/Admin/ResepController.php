<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Resep;
use App\Support\MediaGuard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ResepController extends Controller
{
    public function index(Request $request)
    {
        $query = Resep::with('user')->withCount('likes')->orderByDesc('id');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tingkat_kesulitan')) {
            $query->where('tingkat_kesulitan', $request->tingkat_kesulitan);
        }

        if ($request->filled('q')) {
            $query->where('judul', 'like', '%' . $request->q . '%');
        }

        $resep = $query->paginate(10)->withQueryString();

        $stats = [
            'total'     => Resep::count(),
            'publish'   => Resep::where('status', 'publish')->count(),
            'scheduled' => Resep::where('status', 'scheduled')->count(),
            'draft'     => Resep::where('status', 'draft')->count(),
        ];

        $resepJson = $resep->getCollection()->map(fn ($r) => [
            'id'                => $r->id,
            'judul'             => $r->judul,
            'deskripsi'         => $r->deskripsi,
            'bahan'             => $r->bahan,
            'langkah'           => $r->langkah,
            'durasi_menit'      => (int) $r->durasi_menit,
            'porsi'             => (int) $r->porsi,
            'tingkat_kesulitan' => $r->tingkat_kesulitan,
            'status'            => $r->status,
            'media_type'        => $r->media_type ?? 'image',
            'thumbnail'         => $r->thumbnail_url,
            'video_url'         => $r->video_url,
            'youtube_url'       => $r->youtube_url,
            'youtube_embed_url' => $r->youtube_embed_url,
            'scheduled_at'      => $r->scheduled_at?->format('Y-m-d\TH:i'),
        ]);

        return view('admin.resep.index', compact('resep', 'stats', 'resepJson'));
    }

    public function create()
    {
        return redirect()->route('admin.resep.index');
    }

    public function store(Request $request)
    {
        $mediaType = $request->input('media_type', 'image');
        $validator = $this->validateResep($request);

        if ($validator->fails()) {
            return redirect()->route('admin.resep.index')
                ->withErrors($validator)
                ->withInput()
                ->with('open_modal', 'create');
        }

        $data = $request->only([
            'judul', 'deskripsi', 'bahan', 'langkah',
            'durasi_menit', 'porsi', 'tingkat_kesulitan', 'status',
        ]);
        $data['durasi_menit'] = (int) $data['durasi_menit'];
        $data['porsi'] = (int) $data['porsi'];
        $data['user_id'] = Auth::id();
        $data['slug'] = Str::slug($request->judul) . '-' . time();
        $data['media_type'] = $mediaType;

        $data = array_merge($data, $this->resolveScheduleData($request));
        $data = array_merge($data, $this->handleMediaUpload($request, $mediaType));

        Resep::create($data);

        return redirect()->route('admin.resep.index')
            ->with('success', 'Resep olahan tahu berhasil ditambahkan!');
    }

    public function edit(Resep $resep)
    {
        return redirect()->route('admin.resep.index');
    }

    public function update(Request $request, Resep $resep)
    {
        $mediaType = $request->input('media_type', 'image');
        $validator = $this->validateResep($request, $resep);

        if ($validator->fails()) {
            return redirect()->route('admin.resep.index')
                ->withErrors($validator)
                ->withInput()
                ->with('open_modal', 'edit')
                ->with('edit_id', $resep->id);
        }

        $data = $request->only([
            'judul', 'deskripsi', 'bahan', 'langkah',
            'durasi_menit', 'porsi', 'tingkat_kesulitan', 'status',
        ]);
        $data['durasi_menit'] = (int) $data['durasi_menit'];
        $data['porsi'] = (int) $data['porsi'];
        if ($resep->judul !== $request->judul) {
            $data['slug'] = Str::slug($request->judul) . '-' . time();
        }
        $data['media_type'] = $mediaType;

        $data = array_merge($data, $this->resolveScheduleData($request, $resep));
        $data = array_merge($data, $this->handleMediaUpload($request, $mediaType, $resep));

        $resep->update($data);

        return redirect()->route('admin.resep.index')
            ->with('success', 'Resep olahan tahu berhasil diupdate!');
    }

    public function destroy(Resep $resep)
    {
        $this->deleteStoredFile($resep->thumbnail);
        $this->deleteStoredFile($resep->video_path);

        $resep->delete();

        return redirect()->route('admin.resep.index')
            ->with('success', 'Resep olahan tahu berhasil dihapus!');
    }

    private function validateResep(Request $request, ?Resep $resep = null)
    {
        $mediaType = $request->input('media_type', 'image');
        $needsVideo = $resep
            ? ($mediaType === 'video' && (!$resep->video_path || $request->input('hapus_video') == '1'))
            : ($mediaType === 'video');

        $rules = [
            'judul' => [
                'required', 'string', 'min:4', 'max:200',
                'regex:/^[a-zA-Z0-9\s.,:!?()\-]+$/',
                $resep
                    ? Rule::unique('resep', 'judul')->ignore($resep->id)
                    : Rule::unique('resep', 'judul'),
            ],
            'deskripsi'         => 'nullable|string|max:1000',
            'bahan'             => 'required|string|min:5|max:10000',
            'langkah'           => 'required|string|min:10|max:15000',
            'durasi_menit'      => 'required|integer|min:1|max:1440',
            'porsi'             => 'required|integer|min:1|max:100',
            'tingkat_kesulitan' => 'required|in:mudah,sedang,sulit',
            'media_type'        => 'required|in:image,video,youtube',
            'thumbnail'         => MediaGuard::imageRules(false, 2048),
            'video_file'        => $needsVideo ? MediaGuard::videoRules(true, 20480) : MediaGuard::videoRules(false, 20480),
            'youtube_url'       => [$mediaType === 'youtube' ? 'required' : 'nullable', 'string', 'max:255', MediaGuard::youtubeRule()],
            'status'            => 'required|in:draft,scheduled,publish',
            'scheduled_at'      => 'nullable|required_if:status,scheduled|date|after:+5 minutes',
        ];

        if ($resep) {
            $rules['hapus_thumbnail'] = 'nullable|in:0,1';
            $rules['hapus_video'] = 'nullable|in:0,1';
        }

        $messages = [
            'judul.required'           => 'Judul resep wajib diisi.',
            'judul.regex'              => 'Judul resep hanya boleh huruf, angka, spasi, dan tanda baca umum.',
            'judul.unique'             => 'Judul resep ini sudah ada. Gunakan judul lain.',
            'judul.min'                => 'Judul resep minimal 4 karakter.',
            'judul.max'                => 'Judul resep maksimal 200 karakter.',
            'deskripsi.max'            => 'Deskripsi singkat maksimal 1.000 karakter.',
            'bahan.required'           => 'Daftar bahan wajib diisi.',
            'bahan.min'                => 'Daftar bahan minimal 5 karakter.',
            'bahan.max'                => 'Daftar bahan maksimal 10.000 karakter.',
            'langkah.required'         => 'Langkah memasak wajib diisi.',
            'langkah.min'              => 'Langkah memasak minimal 10 karakter.',
            'langkah.max'              => 'Langkah memasak maksimal 15.000 karakter.',
            'durasi_menit.required'    => 'Durasi memasak wajib diisi.',
            'durasi_menit.integer'     => 'Durasi memasak hanya boleh berisi angka.',
            'durasi_menit.min'         => 'Durasi memasak minimal 1 menit.',
            'durasi_menit.max'         => 'Durasi memasak maksimal 1440 menit.',
            'porsi.required'           => 'Jumlah porsi wajib diisi.',
            'porsi.integer'            => 'Jumlah porsi hanya boleh berisi angka.',
            'porsi.min'                => 'Jumlah porsi minimal 1.',
            'porsi.max'                => 'Jumlah porsi maksimal 100.',
            'tingkat_kesulitan.required' => 'Tingkat kesulitan wajib dipilih.',
            'tingkat_kesulitan.in'       => 'Tingkat kesulitan tidak valid.',
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
        ];

        return Validator::make($request->all(), $rules, $messages);
    }

    private function resolveScheduleData(Request $request, ?Resep $resep = null): array
    {
        return match ($request->status) {
            'scheduled' => [
                'scheduled_at' => $request->scheduled_at,
                'published_at' => null,
            ],
            'publish' => [
                'scheduled_at' => null,
                'published_at' => $resep?->published_at ?? now(),
            ],
            default => [
                'scheduled_at' => null,
                'published_at' => null,
            ],
        };
    }

    private function handleMediaUpload(Request $request, string $mediaType, ?Resep $resep = null): array
    {
        $media = [];

        if ($request->hasFile('thumbnail')) {
            if ($resep) {
                $this->deleteStoredFile($resep->thumbnail);
            }
            $media['thumbnail'] = $request->file('thumbnail')->store('resep', 'public');
        } elseif ($resep && $request->input('hapus_thumbnail') == '1') {
            $this->deleteStoredFile($resep->thumbnail);
            $media['thumbnail'] = null;
        }

        if ($mediaType === 'video') {
            $media['youtube_url'] = null;
            if ($request->hasFile('video_file')) {
                if ($resep) {
                    $this->deleteStoredFile($resep->video_path);
                }
                $media['video_path'] = $request->file('video_file')->store('resep/videos', 'public');
            }
        } elseif ($mediaType === 'youtube') {
            if ($resep) {
                $this->deleteStoredFile($resep->video_path);
            }
            $media['video_path'] = null;
            $media['youtube_url'] = trim((string) $request->youtube_url);
        } else {
            if ($resep) {
                $this->deleteStoredFile($resep->video_path);
            }
            $media['video_path'] = null;
            $media['youtube_url'] = null;
        }

        return $media;
    }

    private function deleteStoredFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
