<?php

namespace App\Http\Controllers;

use App\Models\Edukasi;
use App\Models\Limbah;
use App\Models\ProdukTahu;
use App\Models\Resep;
use Illuminate\Http\Request;

class SearchSuggestController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $scope = (string) $request->query('scope', 'all');
        if (!in_array($scope, ['all', 'produk', 'limbah', 'resep', 'edukasi'], true)) {
            $scope = 'all';
        }

        if ($q === '') {
            return response()->json(['items' => []]);
        }

        $q = mb_substr($q, 0, 80);
        $limit = $scope === 'all' ? 4 : 7;
        $items = [];

        if ($scope === 'all' || $scope === 'produk') {
            $produk = ProdukTahu::where('status', 'aktif')
                ->where('nama_produk', 'like', "%{$q}%")
                ->orderByDesc('id')
                ->limit($limit)
                ->get(['id', 'nama_produk', 'slug', 'harga', 'satuan', 'stok', 'gambar']);

            foreach ($produk as $p) {
                $stokText = ($p->stok ?? 0) > 0
                    ? "Stok {$p->stok} " . ($p->satuan ?? 'pcs')
                    : 'Stok Habis';
                $items[] = [
                    'type'  => 'produk',
                    'badge' => 'Produk Tahu',
                    'name'  => $p->nama_produk,
                    'meta'  => 'Rp ' . number_format((float) $p->harga, 0, ',', '.') . ' / ' . ($p->satuan ?? 'pcs') . ' · ' . $stokText,
                    'image' => $p->gambar ? asset('storage/' . ltrim($p->gambar, '/')) : null,
                    'url'   => route('user.produk.show', $p->slug),
                ];
            }
        }

        if ($scope === 'all' || $scope === 'limbah') {
            $limbah = Limbah::where('status', 'aktif')
                ->where('nama_limbah', 'like', "%{$q}%")
                ->orderByDesc('id')
                ->limit($limit)
                ->get(['id', 'nama_limbah', 'slug', 'harga', 'satuan', 'stok', 'gambar']);

            foreach ($limbah as $l) {
                $stokText = ($l->stok ?? 0) > 0
                    ? "Stok {$l->stok} " . ($l->satuan ?? 'kg')
                    : 'Stok Habis';
                $items[] = [
                    'type'  => 'limbah',
                    'badge' => 'Ampas Tahu',
                    'name'  => $l->nama_limbah,
                    'meta'  => 'Rp ' . number_format((float) $l->harga, 0, ',', '.') . ' / ' . ($l->satuan ?? 'kg') . ' · ' . $stokText,
                    'image' => $l->gambar ? asset('storage/' . ltrim($l->gambar, '/')) : null,
                    'url'   => route('user.limbah.show', $l->slug),
                ];
            }
        }

        if ($scope === 'all' || $scope === 'resep') {
            $resep = Resep::visibleToUser()
                ->where('judul', 'like', "%{$q}%")
                ->orderByDesc('published_at')
                ->orderByDesc('id')
                ->limit($limit)
                ->get(['id', 'judul', 'slug', 'durasi_menit', 'tingkat_kesulitan', 'thumbnail', 'youtube_url']);

            foreach ($resep as $r) {
                $durasi = $r->durasi_menit ? "{$r->durasi_menit} mnt" : 'Resep';
                $level = $r->tingkat_kesulitan ? ucfirst($r->tingkat_kesulitan) : 'Mudah';
                $items[] = [
                    'type'  => 'resep',
                    'badge' => 'Resep',
                    'name'  => $r->judul,
                    'meta'  => "{$durasi} · {$level}",
                    'image' => $r->display_thumbnail_url,
                    'url'   => route('user.resep.show', $r->slug),
                ];
            }
        }

        if ($scope === 'all' || $scope === 'edukasi') {
            $edukasi = Edukasi::visibleToUser()
                ->where('judul', 'like', "%{$q}%")
                ->orderByDesc('published_at')
                ->orderByDesc('id')
                ->limit($limit)
                ->get(['id', 'judul', 'slug', 'media_type', 'thumbnail', 'youtube_url']);

            foreach ($edukasi as $e) {
                $mediaLabel = match ($e->media_type ?? 'image') {
                    'youtube' => 'Video YouTube',
                    'video'   => 'Video Edukasi',
                    default   => 'Artikel Edukasi',
                };
                $items[] = [
                    'type'  => 'edukasi',
                    'badge' => 'Edukasi',
                    'name'  => $e->judul,
                    'meta'  => $mediaLabel,
                    'image' => $e->display_thumbnail_url,
                    'url'   => route('user.edukasi.show', $e->slug),
                ];
            }
        }

        if ($scope === 'all' && count($items) > 10) {
            $items = array_slice($items, 0, 10);
        }

        return response()->json(['items' => array_values($items)]);
    }
}
