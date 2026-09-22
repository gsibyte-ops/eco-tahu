<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Limbah;
use App\Models\ProdukTahu;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session('cart', []);
        $subtotal = collect($cart)->sum(fn ($i) => $i['harga'] * $i['qty']);

        return view('user.cart.index', compact('cart', 'subtotal'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'type' => 'required|in:produk,limbah',
            'id' => 'required|integer',
            'qty' => 'required|integer|min:1',
        ]);

        $item = $request->type === 'produk'
            ? ProdukTahu::where('status', 'aktif')->find($request->id)
            : Limbah::where('status', 'aktif')->find($request->id);

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan',
            ], 404);
        }

        if ($item->stok < $request->qty) {
            return response()->json([
                'success' => false,
                'message' => 'Stok tidak mencukupi. Sisa: ' . $item->stok,
            ], 422);
        }

        $cart = session('cart', []);
        $key = $request->type . '_' . $item->id;

        $nama = $request->type === 'produk' ? $item->nama_produk : $item->nama_limbah;

        if (isset($cart[$key])) {
            $newQty = $cart[$key]['qty'] + $request->qty;
            if ($newQty > $item->stok) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok tidak mencukupi. Sisa: ' . $item->stok,
                ], 422);
            }
            $cart[$key]['qty'] = $newQty;
        } else {
            $cart[$key] = [
                'type' => $request->type,
                'id' => $item->id,
                'nama' => $nama,
                'harga' => (int) $item->harga,
                'qty' => $request->qty,
                'gambar' => $item->gambar,
                'satuan' => $request->type === 'limbah' ? ($item->satuan ?? 'kg') : null,
                'stok_max' => $item->stok,
            ];
        }

        session(['cart' => $cart]);

        return response()->json([
            'success' => true,
            'message' => $nama . ' ditambahkan ke keranjang!',
            'cart_count' => count($cart),
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'key' => 'required|string',
            'qty' => 'required|integer|min:1',
        ]);

        $cart = session('cart', []);

        if (!isset($cart[$request->key])) {
            return response()->json(['success' => false, 'message' => 'Item tidak ditemukan'], 404);
        }

        if ($request->qty > $cart[$request->key]['stok_max']) {
            return response()->json([
                'success' => false,
                'message' => 'Stok tidak mencukupi. Maksimal: ' . $cart[$request->key]['stok_max'],
            ], 422);
        }

        $cart[$request->key]['qty'] = $request->qty;
        session(['cart' => $cart]);

        return response()->json([
            'success' => true,
            'message' => 'Keranjang diupdate',
            'subtotal' => collect($cart)->sum(fn ($i) => $i['harga'] * $i['qty']),
            'item_subtotal' => $cart[$request->key]['harga'] * $request->qty,
        ]);
    }

    public function remove(Request $request)
    {
        $request->validate(['key' => 'required|string']);

        $cart = session('cart', []);
        unset($cart[$request->key]);
        session(['cart' => $cart]);

        return response()->json([
            'success' => true,
            'message' => 'Item dihapus dari keranjang',
            'cart_count' => count($cart),
        ]);
    }

    public function clear()
    {
        session()->forget('cart');
        return redirect()->route('user.cart.index')
            ->with('success', 'Keranjang dikosongkan');
    }
}