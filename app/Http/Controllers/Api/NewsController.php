<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function getNews(Request $request)
    {
        $iso = $request->input('iso', 'ID');

        $articles = [
            [
                'title' => "Stabilitas Rantai Pasok Logistik di Wilayah {$iso} Terpantau Stabil",
                'description' => "Pemerintah dan otoritas pelabuhan meningkatkan efisiensi pengiriman barang untuk menghindari hambatan jalur perdagangan internasional.",
                'url' => '#',
                'source' => 'Global Supply Chain Wire',
                'published_at' => '2 jam lalu',
                'sentiment' => 'Positive'
            ],
            [
                'title' => "Fluktuasi Cuaca Memengaruhi Jadwal Bongkar Muat Pelabuhan Utama",
                'description' => "Pengelola logistik diimbau untuk memantau pembaruan cuaca secara berkala guna mengantisipasi keterlambatan pengiriman maritim.",
                'url' => '#',
                'source' => 'Maritime Intelligence',
                'published_at' => '5 jam lalu',
                'sentiment' => 'Neutral'
            ],
            [
                'title' => "Kenaikan Biaya Logistik Global Picu Penyesuaian Tarif Pengiriman",
                'description' => "Sejumlah operator kargo menyesuaikan tarif seiring dinamika bahan bakar dan tingkat inflasi regional.",
                'url' => '#',
                'source' => 'Logistics Economic Review',
                'published_at' => '1 hari lalu',
                'sentiment' => 'Negative'
            ]
        ];

        return response()->json([
            'status' => 'success',
            'country' => $iso,
            'articles' => $articles
        ]);
    }
}