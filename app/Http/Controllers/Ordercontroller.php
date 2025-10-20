<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;
use App\Models\Order;

class OrderController extends Controller
{
    public function generate(Request $req)
    {
        // Harga per detik
        $hargaPerDetik = 10;

        // Hitung total harga
        $grossAmount = ($req->duration ?? 0) * $hargaPerDetik;

        // Tambahkan harga ke request sebelum dikirim ke Qrgenerator
        $req->merge(['harga' => $grossAmount]);

        // Buat transaksi via Qrgenerator
        $data = app(\App\Http\Controllers\Qrgenerator::class)
                ->createQrispayment($req)
                ->getData(true);

        // Simpan ke database
        Order::create([
            'order_id'         => $data['order_id'] ?? uniqid('ORD-'),
            'name'             => $req->name,
            'nim'              => $req->NIM,
            'duration_seconds' => $req->duration,
            'gross_amount'     => $grossAmount,
            'status'           => $data['transaction_status'] ?? 'pending',
        ]);

        // Simpan status ke cache untuk monitoring (opsional)
        if (isset($data['expiry_time'])) {
            $expiry = Carbon::parse($data['expiry_time']);
            $ttl = Carbon::now()->diffInMinutes($expiry);
            Cache::put($data['order_id'], $data['transaction_status'], $ttl);
        }

        // Kembalikan view hasil pembayaran
        return view('displaytransaction', [
            'type'     => (isset($data['status_code']) && (int)$data['status_code'] < 400)
                ? $data['payment_type']
                : 'err',
            'message'  => $data['status_message'] ?? $data['error'] ?? 'Terjadi kesalahan',
            'mataUang' => $data['currency'] ?? 'IDR',
            'src'      => $data['actions'][0]['url'] ?? null,
            'Rp'       => $grossAmount,
            'OrderId'  => $data['order_id'] ?? ""
        ]);
    }
}
