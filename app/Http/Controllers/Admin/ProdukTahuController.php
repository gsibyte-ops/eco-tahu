<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\ProdukTahu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProdukTahuController extends Controller
{
    public function index()
    {
        $produk = ProdukTahu::with('kategori')
            ->orderByDesc('id')
            ->paginate(10);

        return view('admin.produk-tahu.index', compact('produk'));
    }

    public function create()
    {
        $kategori = Kategori::where('tipe', 'produk_tahu')->get();
        return view('admin.produk-tahu.create', compact('kategori'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_produk' => 'required|string|max:150',
            'kategori_id' => 'required|exists:kategori,id',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $data = $request->only(['kategori_id', 'nama_produk', 'harga', 'stok', 'deskripsi', 'status']);
        $data['slug'] = Str::slug($request->nama_produk) . '-' . time();

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('produk-tahu', 'public');
        }

        ProdukTahu::create($data);

        return redirect()->route('admin.produk-tahu.index')
            ->with('success', 'Produk tahu berhasil ditambahkan!');
    }

    public function edit(ProdukTahu $produkTahu)
    {
        $kategori = Kategori::where('tipe', 'produk_tahu')->get();
        return view('admin.produk-tahu.edit', compact('produkTahu', 'kategori'));
    }

    public function update(Request $request, ProdukTahu $produkTahu)
    {
        $request->validate([
            'nama_produk' => 'required|string|max:150',
            'kategori_id' => 'required|exists:kategori,id',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $data = $request->only(['kategori_id', 'nama_produk', 'harga', 'stok', 'deskripsi', 'status']);
        $data['slug'] = Str::slug($request->nama_produk) . '-' . time();

        if ($request->hasFile('gambar')) {
            if ($produkTahu->gambar && Storage::disk('public')->exists($produkTahu->gambar)) {
                Storage::disk('public')->delete($produkTahu->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('produk-tahu', 'public');
        }

        $produkTahu->update($data);

        return redirect()->route('admin.produk-tahu.index')
            ->with('success', 'Produk tahu berhasil diupdate!');
    }

    public function destroy(ProdukTahu $produkTahu)
    {
        if ($produkTahu->gambar && Storage::disk('public')->exists($produkTahu->gambar)) {
            Storage::disk('public')->delete($produkTahu->gambar);
        }

        $produkTahu->delete();

        return redirect()->route('admin.produk-tahu.index')
            ->with('success', 'Produk tahu berhasil dihapus!');
    }
}