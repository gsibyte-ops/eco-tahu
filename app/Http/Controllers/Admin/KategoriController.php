<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class KategoriController extends Controller
{
    public function index()
    {
        $kategori = Kategori::orderByDesc('id')->paginate(10);

        $kategoriJson = Kategori::orderByDesc('id')->get()->map(function ($k) {
            return [
                'id' => $k->id,
                'nama_kategori' => $k->nama_kategori,
                'tipe' => $k->tipe,
            ];
        })->values();

        return view('admin.kategori.index', compact('kategori', 'kategoriJson'));
    }

    public function create()
    {
        return view('admin.kategori.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_kategori' => [
                'required', 'string', 'min:3', 'max:100',
                'regex:/^[a-zA-Z\s]+$/',
                Rule::unique('kategori', 'nama_kategori'),
            ],
            'tipe' => 'required|in:produk_tahu,limbah',
        ], [
            'nama_kategori.required' => 'Nama kategori wajib diisi.',
            'nama_kategori.regex' => 'Nama kategori hanya boleh huruf dan spasi. Tidak boleh angka atau simbol.',
            'nama_kategori.unique' => 'Nama kategori ini sudah ada. Gunakan nama lain.',
            'nama_kategori.min' => 'Nama kategori minimal 3 karakter.',
            'nama_kategori.max' => 'Nama kategori maksimal 100 karakter.',
            'tipe.required' => 'Tipe kategori wajib dipilih.',
            'tipe.in' => 'Tipe kategori tidak valid.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()
                ->with('open_modal', 'create');
        }

        Kategori::create([
            'nama_kategori' => $request->nama_kategori,
            'slug' => Str::slug($request->nama_kategori) . '-' . time(),
            'tipe' => $request->tipe,
        ]);

        return redirect()->route('admin.kategori.index')
            ->with('success', 'Kategori berhasil ditambahkan!');
    }

    public function edit(Kategori $kategori)
    {
        return view('admin.kategori.edit', compact('kategori'));
    }

    public function update(Request $request, Kategori $kategori)
    {
        $validator = Validator::make($request->all(), [
            'nama_kategori' => [
                'required', 'string', 'min:3', 'max:100',
                'regex:/^[a-zA-Z\s]+$/',
                Rule::unique('kategori', 'nama_kategori')->ignore($kategori->id),
            ],
            'tipe' => 'required|in:produk_tahu,limbah',
        ], [
            'nama_kategori.required' => 'Nama kategori wajib diisi.',
            'nama_kategori.regex' => 'Nama kategori hanya boleh huruf dan spasi. Tidak boleh angka atau simbol.',
            'nama_kategori.unique' => 'Nama kategori ini sudah ada. Gunakan nama lain.',
            'nama_kategori.min' => 'Nama kategori minimal 3 karakter.',
            'nama_kategori.max' => 'Nama kategori maksimal 100 karakter.',
            'tipe.required' => 'Tipe kategori wajib dipilih.',
            'tipe.in' => 'Tipe kategori tidak valid.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()
                ->with('open_modal', 'edit')
                ->with('edit_id', $kategori->id);
        }

        $kategori->update([
            'nama_kategori' => $request->nama_kategori,
            'slug' => Str::slug($request->nama_kategori) . '-' . time(),
            'tipe' => $request->tipe,
        ]);

        return redirect()->route('admin.kategori.index')
            ->with('success', 'Kategori berhasil diupdate!');
    }

    public function destroy(Kategori $kategori)
    {
        $kategori->delete();
        return redirect()->route('admin.kategori.index')
            ->with('success', 'Kategori berhasil dihapus!');
    }
}