<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Edukasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
            'total' => Edukasi::count(),
            'publish' => Edukasi::where('status', 'publish')->count(),
            'draft' => Edukasi::where('status', 'draft')->count(),
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
            'judul' => 'required|string|max:200',
            'konten' => 'required|string',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status' => 'required|in:draft,publish',
        ]);

        $data = $request->only(['judul', 'konten', 'status']);
        $data['user_id'] = Auth::id();
        $data['slug'] = Str::slug($request->judul) . '-' . time();

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
            'judul' => 'required|string|max:200',
            'konten' => 'required|string',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status' => 'required|in:draft,publish',
        ]);

        $data = $request->only(['judul', 'konten', 'status']);
        $data['slug'] = Str::slug($request->judul) . '-' . time();

        if ($request->hasFile('thumbnail')) {
            if ($edukasi->thumbnail && Storage::disk('public')->exists($edukasi->thumbnail)) {
                Storage::disk('public')->delete($edukasi->thumbnail);
            }
            $data['thumbnail'] = $request->file('thumbnail')->store('edukasi', 'public');
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