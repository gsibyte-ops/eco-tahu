<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\Limbah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

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
            'nama_limbah' => [
                'required', 'string', 'min:3', 'max:150',
                'regex:/^[a-zA-Z\s]+$/',
                Rule::unique('limbah', 'nama_limbah'),
            ],
            'kategori_id' => 'required|exists:kategori,id',
            'harga' => 'required|numeric|min:100|max:99999999',
            'stok' => 'required|integer|min:1|max:999999',
            'satuan' => 'required|string|max:20',
            'deskripsi' => 'nullable|string|max:1000',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status' => 'required|in:aktif,nonaktif',
        ], [
            'nama_limbah.required' => 'Nama limbah wajib diisi.',
            'nama_limbah.regex' => 'Nama limbah hanya boleh huruf dan spasi. Tidak boleh angka atau simbol.',
            'nama_limbah.unique' => 'Nama limbah ini sudah ada. Gunakan nama lain.',
            'nama_limbah.min' => 'Nama limbah minimal 3 karakter.',
            'nama_limbah.max' => 'Nama limbah maksimal 150 karakter.',
            'kategori_id.required' => 'Kategori wajib dipilih.',
            'kategori_id.exists' => 'Kategori tidak valid.',
            'harga.required' => 'Harga wajib diisi.',
            'harga.numeric' => 'Harga harus berupa angka.',
            'harga.min' => 'Harga minimal Rp 100.',
            'harga.max' => 'Harga maksimal Rp 99.999.999.',
            'stok.required' => 'Stok wajib diisi.',
            'stok.integer' => 'Stok harus berupa angka bulat.',
            'stok.min' => 'Stok minimal 1. Kalau stok habis, ubah lewat menu Edit.',
            'stok.max' => 'Stok maksimal 999.999.',
            'satuan.required' => 'Satuan wajib diisi. Contoh: kg, ikat, karung.',
            'satuan.max' => 'Satuan maksimal 20 karakter.',
            'deskripsi.max' => 'Deskripsi maksimal 1000 karakter.',
            'gambar.image' => 'File harus berupa gambar.',
            'gambar.mimes' => 'Format gambar harus JPG, PNG, atau WEBP.',
            'gambar.max' => 'Ukuran gambar maksimal 2MB.',
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status tidak valid.',
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
            'nama_limbah' => [
                'required', 'string', 'min:3', 'max:150',
                'regex:/^[a-zA-Z\s]+$/',
                Rule::unique('limbah', 'nama_limbah')->ignore($limbah->id),
            ],
            'kategori_id' => 'required|exists:kategori,id',
            'harga' => 'required|numeric|min:100|max:99999999',
            'stok' => 'required|integer|min:0|max:999999',
            'satuan' => 'required|string|max:20',
            'deskripsi' => 'nullable|string|max:1000',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status' => 'required|in:aktif,nonaktif',
            'hapus_gambar' => 'nullable|in:0,1',
        ], [
            'nama_limbah.required' => 'Nama limbah wajib diisi.',
            'nama_limbah.regex' => 'Nama limbah hanya boleh huruf dan spasi. Tidak boleh angka atau simbol.',
            'nama_limbah.unique' => 'Nama limbah ini sudah ada. Gunakan nama lain.',
            'nama_limbah.min' => 'Nama limbah minimal 3 karakter.',
            'nama_limbah.max' => 'Nama limbah maksimal 150 karakter.',
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
            'satuan.required' => 'Satuan wajib diisi. Contoh: kg, ikat, karung.',
            'satuan.max' => 'Satuan maksimal 20 karakter.',
            'deskripsi.max' => 'Deskripsi maksimal 1000 karakter.',
            'gambar.image' => 'File harus berupa gambar.',
            'gambar.mimes' => 'Format gambar harus JPG, PNG, atau WEBP.',
            'gambar.max' => 'Ukuran gambar maksimal 2MB.',
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status tidak valid.',
        ]);

        $data = $request->only(['kategori_id', 'nama_limbah', 'harga', 'stok', 'satuan', 'deskripsi', 'status']);
        $data['slug'] = Str::slug($request->nama_limbah) . '-' . time();

        if ($request->hasFile('gambar')) {
            if ($limbah->gambar && Storage::disk('public')->exists($limbah->gambar)) {
                Storage::disk('public')->delete($limbah->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('limbah', 'public');

        } elseif ($request->input('hapus_gambar') == '1') {
            if ($limbah->gambar && Storage::disk('public')->exists($limbah->gambar)) {
                Storage::disk('public')->delete($limbah->gambar);
            }
            $data['gambar'] = null;
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