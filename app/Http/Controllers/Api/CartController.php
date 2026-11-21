<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\CartStore;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $user->loadMissing('role');

        if ($user->isAdmin() || (int) $user->role_id === 1) {
            return response()->json([
                'success'    => true,
                'items'      => [],
                'cart_count' => 0,
            ]);
        }

        $items = array_values(CartStore::getCartForUser($user));

        return response()->json([
            'success'    => true,
            'items'      => $items,
            'cart_count' => count($items),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_type' => 'required|in:tahu,produk,limbah',
            'item_id'   => 'required|integer|min:1',
            'qty'       => 'nullable|integer|min:1',
        ]);

        $res = CartStore::addItem(
            $request->user(),
            $validated['item_type'],
            (int) $validated['item_id'],
            (int) ($validated['qty'] ?? 1)
        );

        return response()->json($res, $res['status']);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'item_type' => 'required|in:tahu,produk,limbah',
            'item_id'   => 'required|integer|min:1',
            'qty'       => 'required|integer|min:1',
        ]);

        $res = CartStore::updateQty(
            $request->user(),
            $validated['item_type'],
            (int) $validated['item_id'],
            (int) $validated['qty']
        );

        return response()->json($res, $res['status']);
    }

    public function removeItem(Request $request)
    {
        $validated = $request->validate([
            'item_type' => 'required|in:tahu,produk,limbah',
            'item_id'   => 'required|integer|min:1',
        ]);

        $res = CartStore::removeItem(
            $request->user(),
            $validated['item_type'],
            (int) $validated['item_id']
        );

        return response()->json($res, $res['status']);
    }

    public function clear(Request $request)
    {
        CartStore::clearUser($request->user());

        return response()->json([
            'success'    => true,
            'message'    => 'Keranjang dikosongkan',
            'cart_count' => 0,
        ]);
    }
}
