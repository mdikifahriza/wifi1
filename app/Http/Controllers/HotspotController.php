<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\Order;

class HotspotController extends Controller
{
    /**
     * IP ESP32 (gunakan IP statis agar tidak berubah)
     */
    private $espIp = '192.168.43.16'; // Ganti dengan IP ESP32 kamu

    /**
     * Menampilkan halaman countdown sesuai order terakhir
     */
    public function show()
    {
        $lastOrder = Order::orderBy('created_at', 'desc')->first();

        if (!$lastOrder || !isset($lastOrder->duration_seconds)) {
            return redirect('/')->withErrors(['order' => 'Tidak ada order ditemukan']);
        }

        $duration = (int) $lastOrder->duration_seconds;

        return view('countdown', ['duration' => $duration]);
    }

    /**
     * Menyalakan hotspot dan relay (ON)
     */
    public function start(Request $request)
    {
        $lastOrder = Order::orderBy('created_at', 'desc')->first();
        if (!$lastOrder || !isset($lastOrder->duration_seconds)) {
            return response()->json(['error' => 'Order tidak ditemukan'], 404);
        }

        $duration = (int) $lastOrder->duration_seconds;

        // Simpan status relay ON di cache (opsional)
        Cache::put('relay_status', 'ON', now()->addSeconds($duration));

        // Kirim perintah ke ESP32
        try {
            $response = Http::timeout(3)->get("http://{$this->espIp}/relay", ['status' => 'ON']);
            Log::info("Kirim perintah ON ke ESP32: " . $response->body());
        } catch (\Exception $e) {
            Log::error("Gagal mengirim perintah ke ESP32: " . $e->getMessage());
        }

        Log::info("Hotspot & relay ON selama {$duration} detik");

        return response()->json([
            'success' => true,
            'status' => 'ON',
            'duration_seconds' => $duration
        ]);
    }

    /**
     * Mematikan hotspot & relay (OFF)
     */
    public function stop(Request $request)
    {
        Cache::put('relay_status', 'OFF', now()->addHours(24));

        try {
            $response = Http::timeout(3)->get("http://{$this->espIp}/relay", ['status' => 'OFF']);
            Log::info("Kirim perintah OFF ke ESP32: " . $response->body());
        } catch (\Exception $e) {
            Log::error("Gagal mengirim perintah ke ESP32: " . $e->getMessage());
        }

        Log::info("Hotspot & relay dimatikan secara manual");

        return response()->json([
            'success' => true,
            'status' => 'OFF'
        ]);
    }

    /**
     * Untuk pengecekan status dari web (tidak dipakai oleh ESP32)
     */
    public function relayStatus()
    {
        $status = Cache::get('relay_status', 'OFF');
        return response($status, 200);
    }
}
