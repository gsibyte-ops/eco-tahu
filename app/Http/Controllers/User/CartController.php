<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Support\CartStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $cart = CartStore::getCartForUser(Auth::user());
        $subtotal = collect($cart)->sum(fn ($i) => $i['harga'] * $i['qty']);

        return view('user.cart.index', compact('cart', 'subtotal'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'type' => 'required|in:produk,tahu,limbah',
            'id'   => 'required|integer|min:1',
            'qty'  => 'required|integer|min:1',
        ]);

        $res = CartStore::addItem(
            Auth::user(),
            $request->type,
            (int) $request->id,
            (int) $request->qty
        );

        return response()->json($res, $res['status']);
    }

    public function update(Request $request)
    {
        $request->validate([
            'key' => 'required|string',
            'qty' => 'required|integer|min:1',
        ]);

        $parsed = CartStore::parseKey($request->key);
        if (!$parsed) {
            return response()->json(['success' => false, 'message' => 'Item tidak ditemukan'], 404);
        }

        $res = CartStore::updateQty(
            Auth::user(),
            $parsed['type'],
            $parsed['id'],
            (int) $request->qty
        );

        return response()->json($res, $res['status']);
    }

    public function remove(Request $request)
    {
        $request->validate(['key' => 'required|string']);

        $parsed = CartStore::parseKey($request->key);
        if (!$parsed) {
            return response()->json(['success' => false, 'message' => 'Item tidak ditemukan'], 404);
        }

        $res = CartStore::removeItem(Auth::user(), $parsed['type'], $parsed['id']);

        return response()->json($res, $res['status']);
    }

    public function clear()
    {
        CartStore::clearUser(Auth::user());
        session()->forget('cart');

        return redirect()->route('user.cart.index')
            ->with('success', 'Keranjang dikosongkan');
    }
}
