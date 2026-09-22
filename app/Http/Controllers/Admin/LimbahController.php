<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\Limbah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LimbahController extends Controller
{
    public function index()
    {
        $limbah = Limbah::with('kategori')
            ->orderByDesc('id')
            ->paginate(10);

        return view('admin.limbah.index', compact('limbah'));
    }

    public function create()
    {
        $kategori = Kategori::where('tipe', 'limbah')->get();
        return view('admin.limbah.create', compact('kategori'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_limbah' => 'required|string|max:150',
            'kategori_id' => 'required|exists:kategori,id',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'satuan' => 'required|string|max:20',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $data = $request->only(['kategori_id', 'nama_limbah', 'harga', 'stok', 'satuan', 'deskripsi', 'status']);
        $data['slug'] = Str::slug($request->nama_limbah) . '-' . time();

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('limbah', 'public');
        }

        Limbah::create($data);

        return redirect()->route('admin.limbah.index')
            ->with('success', 'Limbah berhasil ditambahkan!');
    }

    public function edit(Limbah $limbah)
    {
        $kategori = Kategori::where('tipe', 'limbah')->get();
        return view('admin.limbah.edit', compact('limbah', 'kategori'));
    }

    public function update(Request $request, Limbah $limbah)
    {
        $request->validate([
            'nama_limbah' => 'required|string|max:150',
            'kategori_id' => 'required|exists:kategori,id',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'satuan' => 'required|string|max:20',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $data = $request->only(['kategori_id', 'nama_limbah', 'harga', 'stok', 'satuan', 'deskripsi', 'status']);
        $data['slug'] = Str::slug($request->nama_limbah) . '-' . time();

        if ($request->hasFile('gambar')) {
            if ($limbah->gambar && Storage::disk('public')->exists($limbah->gambar)) {
                Storage::disk('public')->delete($limbah->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('limbah', 'public');
        }

        $limbah->update($data);

        return redirect()->route('admin.limbah.index')
            ->with('success', 'Limbah berhasil diupdate!');
    }

    public function destroy(Limbah $limbah)
    {
        if ($limbah->gambar && Storage::disk('public')->exists($limbah->gambar)) {
            Storage::disk('public')->delete($limbah->gambar);
        }

        $limbah->delete();

        return redirect()->route('admin.limbah.index')
            ->with('success', 'Limbah berhasil dihapus!');
    }
}