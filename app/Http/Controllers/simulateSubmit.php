<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB; // ⬅️ tambahkan ini buat insert manual
use Illuminate\Support\Str; // buat order_id random

class simulateSubmit extends Controller
{
    public function sendForm(Request $req)
    {
        $htmlHelper = new \App\Services\Htmlhelper();

        // Ambil URL target (endpoint asli simulator Midtrans)
        $url = $req->input('_orig_action');
        if (!$url) {
            return response("URL asal tidak ditemukan.", 400);
        }

        // 🔹 Ambil data penting dari request buat disimpan
        $orderId = Str::uuid()->toString();
        $nama = $req->input('name') ?? 'unknown';
        $nim = $req->input('nim') ?? '-';
        $harga = $req->input('harga') ?? 0;

        // 🔹 Simpan data ke tabel bayar (tanpa ganggu flow lama)
        try {
            DB::table('bayar')->insert([
                'order_id'      => $orderId,
                'nama'          => $nama,
                'nim'           => $nim,
                'harga'         => $harga,
                'status'        => 'pending',
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        } catch (\Throwable $e) {
            // kalau gagal insert, tetap lanjut proses Midtrans
            logger()->error("Gagal menyimpan ke tabel bayar: ".$e->getMessage());
        }

        // Hapus field lokal yang tidak perlu dikirim ke Midtrans
        $payload = $req->except(['_token', '_orig_action']);

        // Kirim ke Midtrans pakai POST
        $res = Http::asForm()->post($url, $payload);

        if ($res->failed())
            return response(["status" => "can't send the url"], $res->status());

        // Buat Document object model
        $dom = $htmlHelper->createDom($res);
        if (!$dom)
            return response(["status" => "not a HTML document"], 500);

        // Copy semua elemen form
        $forms = $dom->getElementsByTagName("form");
        if (!$forms)
            return response(["status" => "no form element"], 500);

        $form = $forms->item(0); // pilih indeks 0

        // URL endpoint absolut untuk form
        $absoluteUrl_Endpoint = $htmlHelper->toAbsoluteUrl(
            $form->getAttribute('action') ?: config("midtransAPI.simulator"),
            config("midtransAPI.simulator")
        );

        // Ganti endpoint ke absolut endpoint
        $form->setAttribute('action', $absoluteUrl_Endpoint);
        $htmlHelper->cleanForm($form);

        // Jadikan DOM ke array asosiatif, kirim dengan method post
        $res = Http::asForm()->post($absoluteUrl_Endpoint, $htmlHelper->formToArray($form));

        if ($res->failed())
            return response(["status" => "can't post to payment simulator"], $res->status());

        // ✅ Berhasil kirim, tunggu webhook, kirim response seperti semula
        return response(["status" => "checking payment status", "token" => csrf_token()], 200);
    }
}
