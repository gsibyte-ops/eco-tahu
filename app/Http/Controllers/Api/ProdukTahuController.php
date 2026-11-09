<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProdukTahu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProdukTahuController extends Controller
{
    /**
     * GET /api/produk-tahu
     * Tampilkan semua produk tahu + relasi kategori.
     */
    public function index()
    {
        $produk = ProdukTahu::with('kategori')
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar produk tahu',
            'data'    => $produk,
        ], 200);
    }

    /**
     * POST /api/produk-tahu
     * Simpan produk tahu baru.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kategori_id'  => 'required|exists:kategori,id',
            'nama_produk'  => 'required|string|min:3|max:150',
            'harga'        => 'required|numeric|min:100|max:100000',
            'stok'         => 'required|integer|min:0|max:1000',
            'satuan'       => 'nullable|string|max:20',
            'deskripsi'    => 'nullable|string|max:1000',
            'status'       => 'required|in:aktif,nonaktif',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $data = $request->only([
            'kategori_id', 'nama_produk', 'harga',
            'stok', 'satuan', 'deskripsi', 'status',
        ]);

        $data['slug'] = \Illuminate\Support\Str::slug($request->nama_produk) . '-' . time();

        $produk = ProdukTahu::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Produk tahu berhasil ditambahkan',
            'data'    => $produk->load('kategori'),
        ], 201);
    }

    /**
     * GET /api/produk-tahu/{id}
     * Tampilkan satu produk tahu.
     */
    public function show($id)
    {
        $produk = ProdukTahu::with('kategori')->find($id);

        if (!$produk) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tahu tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $produk,
        ], 200);
    }

    /**
     * PUT/PATCH /api/produk-tahu/{id}
     * Update produk tahu.
     */
    public function update(Request $request, $id)
    {
        $produk = ProdukTahu::find($id);

        if (!$produk) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tahu tidak ditemukan',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'kategori_id'  => 'sometimes|required|exists:kategori,id',
            'nama_produk'  => 'sometimes|required|string|min:3|max:150',
            'harga'        => 'sometimes|required|numeric|min:100|max:100000',
            'stok'         => 'sometimes|required|integer|min:0|max:1000',
            'satuan'       => 'nullable|string|max:20',
            'deskripsi'    => 'nullable|string|max:1000',
            'status'       => 'sometimes|required|in:aktif,nonaktif',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $data = $request->only([
            'kategori_id', 'nama_produk', 'harga',
            'stok', 'satuan', 'deskripsi', 'status',
        ]);

        if ($request->filled('nama_produk')) {
            $data['slug'] = \Illuminate\Support\Str::slug($request->nama_produk) . '-' . time();
        }

        $produk->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Produk tahu berhasil diupdate',
            'data'    => $produk->fresh('kategori'),
        ], 200);
    }

    /**
     * DELETE /api/produk-tahu/{id}
     * Hapus produk tahu.
     */
    public function destroy($id)
    {
        $produk = ProdukTahu::find($id);

        if (!$produk) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tahu tidak ditemukan',
            ], 404);
        }

        $produk->delete();

        return response()->json([
            'success' => true,
            'message' => 'Produk tahu berhasil dihapus',
        ], 200);
    }
}