<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\ProdukTahu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

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
            'nama_produk' => [
                'required', 'string', 'min:3', 'max:150',
                'regex:/^[a-zA-Z\s]+$/',
                Rule::unique('produk_tahu', 'nama_produk'),
            ],
            'kategori_id' => 'required|exists:kategori,id',
            'harga' => 'required|numeric|min:100|max:99999999',
            'stok' => 'required|integer|min:1|max:999999',   // ← UBAH jadi min:1
            'deskripsi' => 'nullable|string|max:1000',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status' => 'required|in:aktif,nonaktif',
        ], [
            'nama_produk.required' => 'Nama produk wajib diisi.',
            'nama_produk.regex' => 'Nama produk hanya boleh huruf dan spasi. Tidak boleh angka atau simbol.',
            'nama_produk.unique' => 'Nama produk ini sudah ada. Gunakan nama lain.',
            'nama_produk.min' => 'Nama produk minimal 3 karakter.',
            'nama_produk.max' => 'Nama produk maksimal 150 karakter.',
            'kategori_id.required' => 'Kategori wajib dipilih.',
            'kategori_id.exists' => 'Kategori tidak valid.',
            'harga.required' => 'Harga wajib diisi.',
            'harga.numeric' => 'Harga harus berupa angka.',
            'harga.min' => 'Harga minimal Rp 100.',
            'harga.max' => 'Harga maksimal Rp 99.999.999.',
            'stok.required' => 'Stok wajib diisi.',
            'stok.integer' => 'Stok harus berupa angka bulat.',
            'stok.min' => 'Stok minimal 1. Kalau stok habis, ubah lewat menu Edit.',   // ← PESAN BARU
            'stok.max' => 'Stok maksimal 999.999.',
            'deskripsi.max' => 'Deskripsi maksimal 1000 karakter.',
            'gambar.image' => 'File harus berupa gambar.',
            'gambar.mimes' => 'Format gambar harus JPG, PNG, atau WEBP.',
            'gambar.max' => 'Ukuran gambar maksimal 2MB.',
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status tidak valid.',
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
            'nama_produk' => [
                'required', 'string', 'min:3', 'max:150',
                'regex:/^[a-zA-Z\s]+$/',
                Rule::unique('produk_tahu', 'nama_produk')->ignore($produkTahu->id),
            ],
            'kategori_id' => 'required|exists:kategori,id',
            'harga' => 'required|numeric|min:100|max:99999999',
            'stok' => 'required|integer|min:0|max:999999',
            'deskripsi' => 'nullable|string|max:1000',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status' => 'required|in:aktif,nonaktif',
            'hapus_gambar' => 'nullable|in:0,1',
        ], [
            'nama_produk.required' => 'Nama produk wajib diisi.',
            'nama_produk.regex' => 'Nama produk hanya boleh huruf dan spasi. Tidak boleh angka atau simbol.',
            'nama_produk.unique' => 'Nama produk ini sudah ada. Gunakan nama lain.',
            'nama_produk.min' => 'Nama produk minimal 3 karakter.',
            'nama_produk.max' => 'Nama produk maksimal 150 karakter.',
            'kategori_id.required' => 'Kategori wajib dipilih.',
            'kategori_id.exists' => 'Kategori tidak valid.',
            'harga.required' => 'Harga wajib diisi.',
            'harga.numeric' => 'Harga harus berupa angka.',
            'harga.min' => 'Harga minimal Rp 100.',
            'harga.max' => 'Harga maksimal Rp 99.999.999.',
            'stok.required' => 'Stok wajib diisi.',
            'stok.integer' => 'Stok harus berupa angka bulat.',
            'stok.min' => 'Stok tidak boleh negatif.',
            'stok.max' => 'Stok maksimal 999.999.',
            'deskripsi.max' => 'Deskripsi maksimal 1000 karakter.',
            'gambar.image' => 'File harus berupa gambar.',
            'gambar.mimes' => 'Format gambar harus JPG, PNG, atau WEBP.',
            'gambar.max' => 'Ukuran gambar maksimal 2MB.',
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status tidak valid.',
        ]);

        $data = $request->only(['kategori_id', 'nama_produk', 'harga', 'stok', 'deskripsi', 'status']);
        $data['slug'] = Str::slug($request->nama_produk) . '-' . time();

        // Prioritas 1: User upload gambar baru
        if ($request->hasFile('gambar')) {
            if ($produkTahu->gambar && Storage::disk('public')->exists($produkTahu->gambar)) {
                Storage::disk('public')->delete($produkTahu->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('produk-tahu', 'public');

        // Prioritas 2: User klik tombol X (hapus gambar lama)
        } elseif ($request->input('hapus_gambar') == '1') {
            if ($produkTahu->gambar && Storage::disk('public')->exists($produkTahu->gambar)) {
                Storage::disk('public')->delete($produkTahu->gambar);
            }
            $data['gambar'] = null;
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