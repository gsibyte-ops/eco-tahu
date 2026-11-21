<?php

namespace App\Support;

use App\Models\CartItem;
use App\Models\Limbah;
use App\Models\ProdukTahu;
use App\Models\User;

class CartStore
{
    public static function normalizeType(string $raw): string
    {
        $val = strtolower(trim($raw));
        if ($val === 'limbah') {
            return 'limbah';
        }
        return 'produk';
    }

    public static function parseKey(string $key): ?array
    {
        if (preg_match('/^(produk|tahu|limbah)_(\d+)$/', strtolower(trim($key)), $m)) {
            return [
                'type' => self::normalizeType($m[1]),
                'id'   => (int) $m[2],
            ];
        }
        return null;
    }

    public static function getCartForUser(?User $user): array
    {
        if (!$user) {
            return [];
        }

        $rows = CartItem::where('user_id', $user->id)->orderBy('id')->get();
        if ($rows->isEmpty()) {
            return [];
        }

        $cart = [];

        foreach ($rows as $row) {
            $type = self::normalizeType((string) $row->item_type);
            $model = $type === 'produk'
                ? ProdukTahu::where('status', 'aktif')->find($row->item_id)
                : Limbah::where('status', 'aktif')->find($row->item_id);

            if (!$model || (int) $model->stok <= 0) {
                $row->delete();
                continue;
            }

            $qty = max(1, (int) $row->qty);
            if ($qty > (int) $model->stok) {
                $qty = (int) $model->stok;
                $row->qty = $qty;
                $row->save();
            }

            $nama = $type === 'produk' ? $model->nama_produk : $model->nama_limbah;
            $gambarUrl = '';
            if (!empty($model->gambar)) {
                $rawGambar = trim((string) $model->gambar);
                if (str_starts_with($rawGambar, 'http://') || str_starts_with($rawGambar, 'https://')) {
                    $gambarUrl = $rawGambar;
                } else {
                    $clean = ltrim(str_replace('storage/', '', $rawGambar), '/');
                    $gambarUrl = url('/files/' . $clean);
                }
            }

            $key = $type . '_' . $model->id;
            $cart[$key] = [
                'type'        => $type,
                'mobile_type' => $type === 'limbah' ? 'limbah' : 'tahu',
                'id'          => (int) $model->id,
                'kategori_id' => (int) ($model->kategori_id ?? 0),
                'nama'        => $nama,
                'slug'        => (string) ($model->slug ?? ''),
                'deskripsi'   => (string) ($model->deskripsi ?? ''),
                'harga'       => (int) $model->harga,
                'qty'         => $qty,
                'gambar'      => $model->gambar,
                'gambar_url'  => $gambarUrl,
                'satuan'      => $type === 'limbah' ? ($model->satuan ?? 'kg') : ($model->satuan ?? 'pcs'),
                'stok_max'    => (int) $model->stok,
            ];
        }

        return $cart;
    }

    public static function countForUser(?User $user): int
    {
        if (!$user) {
            return 0;
        }
        return (int) CartItem::where('user_id', $user->id)->count();
    }

    public static function addItem(User $user, string $rawType, int $itemId, int $qty = 1): array
    {
        $type = self::normalizeType($rawType);
        $model = $type === 'produk'
            ? ProdukTahu::where('status', 'aktif')->find($itemId)
            : Limbah::where('status', 'aktif')->find($itemId);

        if (!$model) {
            return [
                'status'  => 404,
                'success' => false,
                'message' => 'Produk tidak ditemukan',
            ];
        }

        $existing = CartItem::where('user_id', $user->id)
            ->where('item_type', $type)
            ->where('item_id', $itemId)
            ->first();

        $newQty = ($existing ? (int) $existing->qty : 0) + max(1, $qty);
        if ($newQty > (int) $model->stok) {
            return [
                'status'  => 422,
                'success' => false,
                'message' => 'Stok tidak mencukupi. Sisa: ' . $model->stok,
            ];
        }

        if ($existing) {
            $existing->qty = $newQty;
            $existing->save();
        } else {
            CartItem::create([
                'user_id'   => $user->id,
                'item_type' => $type,
                'item_id'   => $itemId,
                'qty'       => $newQty,
            ]);
        }

        $nama = $type === 'produk' ? $model->nama_produk : $model->nama_limbah;

        return [
            'status'     => 200,
            'success'    => true,
            'message'    => $nama . ' ditambahkan ke keranjang!',
            'cart_count' => self::countForUser($user),
        ];
    }

    public static function updateQty(User $user, string $rawType, int $itemId, int $qty): array
    {
        $type = self::normalizeType($rawType);
        $existing = CartItem::where('user_id', $user->id)
            ->where('item_type', $type)
            ->where('item_id', $itemId)
            ->first();

        if (!$existing) {
            return [
                'status'  => 404,
                'success' => false,
                'message' => 'Item tidak ditemukan',
            ];
        }

        $model = $type === 'produk'
            ? ProdukTahu::where('status', 'aktif')->find($itemId)
            : Limbah::where('status', 'aktif')->find($itemId);

        if (!$model || (int) $model->stok <= 0) {
            $existing->delete();
            return [
                'status'  => 404,
                'success' => false,
                'message' => 'Produk sudah tidak tersedia',
            ];
        }

        if ($qty > (int) $model->stok) {
            return [
                'status'  => 422,
                'success' => false,
                'message' => 'Stok tidak mencukupi. Maksimal: ' . $model->stok,
            ];
        }

        $existing->qty = max(1, $qty);
        $existing->save();

        $cart = self::getCartForUser($user);

        return [
            'status'        => 200,
            'success'       => true,
            'message'       => 'Keranjang diupdate',
            'subtotal'      => collect($cart)->sum(fn ($i) => $i['harga'] * $i['qty']),
            'item_subtotal' => (int) $model->harga * $existing->qty,
            'cart_count'    => count($cart),
        ];
    }

    public static function removeItem(User $user, string $rawType, int $itemId): array
    {
        $type = self::normalizeType($rawType);
        CartItem::where('user_id', $user->id)
            ->where('item_type', $type)
            ->where('item_id', $itemId)
            ->delete();

        return [
            'status'     => 200,
            'success'    => true,
            'message'    => 'Item dihapus dari keranjang',
            'cart_count' => self::countForUser($user),
        ];
    }

    public static function clearUser(?User $user): void
    {
        if (!$user) {
            return;
        }
        CartItem::where('user_id', $user->id)->delete();
    }
}
