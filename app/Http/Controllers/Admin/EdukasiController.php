<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Edukasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class EdukasiController extends Controller
{
    public function index(Request $request)
    {
        $query = Edukasi::with('user')->orderByDesc('id');

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

        return view('admin.edukasi.index', compact('edukasi', 'stats'));
    }

    public function create()
    {
        return view('admin.edukasi.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => [
                'required', 'string', 'min:5', 'max:200',
                'regex:/^[a-zA-Z0-9\s.,:!?\-]+$/',
                Rule::unique('edukasi', 'judul'),
            ],
            'konten' => 'required|string|min:20|max:10000',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status' => 'required|in:draft,scheduled,publish',
            'scheduled_at' => 'nullable|required_if:status,scheduled|date|after:+5 minutes',
        ], [
            'judul.required' => 'Judul artikel wajib diisi.',
            'judul.regex' => 'Judul artikel hanya boleh huruf, angka, spasi, dan tanda baca umum (titik, koma, titik dua, tanda tanya, tanda seru, tanda hubung).',
            'judul.unique' => 'Judul artikel ini sudah ada. Gunakan judul lain.',
            'judul.min' => 'Judul artikel minimal 5 karakter.',
            'judul.max' => 'Judul artikel maksimal 200 karakter.',
            'konten.required' => 'Konten artikel wajib diisi.',
            'konten.min' => 'Konten artikel minimal 20 karakter.',
            'konten.max' => 'Konten artikel maksimal 10.000 karakter.',
            'thumbnail.image' => 'File harus berupa gambar.',
            'thumbnail.mimes' => 'Format thumbnail harus JPG, PNG, atau WEBP.',
            'thumbnail.max' => 'Ukuran thumbnail maksimal 2MB.',
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status tidak valid.',
            'scheduled_at.required_if' => 'Tanggal jadwal wajib diisi kalau status Schedule.',
            'scheduled_at.after' => 'Jadwal minimal 5 menit dari sekarang.',
            'scheduled_at.date' => 'Format tanggal jadwal tidak valid.',
        ]);

        $data = $request->only(['judul', 'konten', 'status']);
        $data['user_id'] = Auth::id();
        $data['slug'] = Str::slug($request->judul) . '-' . time();

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

        Edukasi::create($data);

        return redirect()->route('admin.edukasi.index')
            ->with('success', 'Artikel edukasi berhasil ditambahkan!');
    }

    public function edit(Edukasi $edukasi)
    {
        return view('admin.edukasi.edit', compact('edukasi'));
    }

    public function update(Request $request, Edukasi $edukasi)
    {
        $request->validate([
            'judul' => [
                'required', 'string', 'min:5', 'max:200',
                'regex:/^[a-zA-Z0-9\s.,:!?\-]+$/',
                Rule::unique('edukasi', 'judul')->ignore($edukasi->id),
            ],
            'konten' => 'required|string|min:20|max:10000',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status' => 'required|in:draft,scheduled,publish',
            'scheduled_at' => 'nullable|required_if:status,scheduled|date|after:+5 minutes',
            'hapus_thumbnail' => 'nullable|in:0,1',
        ], [
            'judul.required' => 'Judul artikel wajib diisi.',
            'judul.regex' => 'Judul artikel hanya boleh huruf, angka, spasi, dan tanda baca umum.',
            'judul.unique' => 'Judul artikel ini sudah ada. Gunakan judul lain.',
            'judul.min' => 'Judul artikel minimal 5 karakter.',
            'judul.max' => 'Judul artikel maksimal 200 karakter.',
            'konten.required' => 'Konten artikel wajib diisi.',
            'konten.min' => 'Konten artikel minimal 20 karakter.',
            'konten.max' => 'Konten artikel maksimal 10.000 karakter.',
            'thumbnail.image' => 'File harus berupa gambar.',
            'thumbnail.mimes' => 'Format thumbnail harus JPG, PNG, atau WEBP.',
            'thumbnail.max' => 'Ukuran thumbnail maksimal 2MB.',
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status tidak valid.',
            'scheduled_at.required_if' => 'Tanggal jadwal wajib diisi kalau status Schedule.',
            'scheduled_at.after' => 'Jadwal minimal 5 menit dari sekarang.',
            'scheduled_at.date' => 'Format tanggal jadwal tidak valid.',
        ]);

        $data = $request->only(['judul', 'konten', 'status']);
        $data['slug'] = Str::slug($request->judul) . '-' . time();

        switch ($request->status) {
            case 'scheduled':
                $data['scheduled_at'] = $request->scheduled_at;
                $data['published_at'] = null;
                break;

            case 'publish':
                $data['scheduled_at'] = null;
                // Kalau sebelumnya sudah pernah publish, jangan reset published_at
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

        $edukasi->update($data);

        return redirect()->route('admin.edukasi.index')
            ->with('success', 'Artikel edukasi berhasil diupdate!');
    }

    public function destroy(Edukasi $edukasi)
    {
        if ($edukasi->thumbnail && Storage::disk('public')->exists($edukasi->thumbnail)) {
            Storage::disk('public')->delete($edukasi->thumbnail);
        }

        $edukasi->delete();

        return redirect()->route('admin.edukasi.index')
            ->with('success', 'Artikel edukasi berhasil dihapus!');
    }
}