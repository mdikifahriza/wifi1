<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;
use App\Models\Order;

class HotspotController extends Controller
{
    /**
     * Menampilkan halaman countdown
     */
    public function show(Request $request)
    {
        $lastOrder = Order::orderBy('created_at', 'desc')->first();

        if (!$lastOrder || !isset($lastOrder->duration_seconds)) {
            return redirect('/')->withErrors(['order' => 'Tidak ada order ditemukan']);
        }

        $duration = (int) $lastOrder->duration_seconds;
        return view('countdown', ['duration' => $duration]);
    }

    /**
     * Menjalankan hotspot ON dan menjadwalkan OFF otomatis menggunakan Task Scheduler
     */
    public function start(Request $request)
    {
        try {
            // Ambil order terakhir
            $lastOrder = Order::orderBy('created_at', 'desc')->first();
            if (!$lastOrder || !isset($lastOrder->duration_seconds)) {
                return response()->json(['error' => 'Order tidak ditemukan'], 404);
            }

            $duration = (int) $lastOrder->duration_seconds;
            $username = env('WINDOWS_USERNAME', 'THINKPAD'); // Ganti sesuai user Windows kamu

            $onBat  = env('HOTSPOT_ON_BAT', base_path('scripts/hotspot_on.bat'));
            $offBat = env('HOTSPOT_OFF_BAT', base_path('scripts/hotspot_off.bat'));

            if (!file_exists($onBat)) {
                Log::error('Script hotspot_on.bat tidak ditemukan', ['path' => $onBat]);
                return response()->json(['error' => 'Script hotspot_on.bat tidak ditemukan'], 500);
            }

            if (!file_exists($offBat)) {
                Log::error('Script hotspot_off.bat tidak ditemukan', ['path' => $offBat]);
                return response()->json(['error' => 'Script hotspot_off.bat tidak ditemukan'], 500);
            }

            // Buat nama unik untuk task
            $taskNameOn  = 'HotspotOn_' . time();
            $taskNameOff = 'HotspotOff_' . time();

            // Jadwal eksekusi
            $startTime = now()->addSeconds(5)->format('H:i:s');
            $offTime   = now()->addSeconds($duration)->format('H:i:s');

            // Command untuk membuat task ON
            $createTaskOn = sprintf(
                'schtasks /create /tn "%s" /tr "\"%s\"" /sc once /st %s /f /ru "%s"',
                $taskNameOn,
                $onBat,
                $startTime,
                $username
            );

            // Command untuk membuat task OFF
            $createTaskOff = sprintf(
                'schtasks /create /tn "%s" /tr "\"%s\"" /sc once /st %s /f /ru "%s"',
                $taskNameOff,
                $offBat,
                $offTime,
                $username
            );

            // Jalankan keduanya
            exec($createTaskOn, $outputOn, $returnOn);
            exec("schtasks /run /tn \"$taskNameOn\"", $runOut, $runReturn);

            exec($createTaskOff, $outputOff, $returnOff);

            // Logging
            Log::info('Hotspot tasks created', [
                'on_task' => $taskNameOn,
                'off_task' => $taskNameOff,
                'on_time' => $startTime,
                'off_time' => $offTime,
                'duration' => $duration,
                'create_on_exit_code' => $returnOn,
                'create_off_exit_code' => $returnOff,
            ]);

            if ($returnOn !== 0) {
                Log::error('Gagal membuat task ON', ['output' => $outputOn]);
                return response()->json([
                    'error' => 'Gagal membuat task ON',
                    'details' => implode("\n", $outputOn)
                ], 500);
            }

            if ($returnOff !== 0) {
                Log::warning('Gagal membuat task OFF (akan fallback timer)', ['output' => $outputOff]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Hotspot dijadwalkan menyala dan akan mati otomatis',
                'task_on' => $taskNameOn,
                'task_off' => $taskNameOff,
                'duration_seconds' => $duration
            ]);
        } catch (\Throwable $e) {
            Log::error('Exception saat menjalankan hotspot', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'Gagal menjalankan script',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mematikan hotspot secara manual (opsional)
     */
    public function stop(Request $request)
    {
        try {
            $offBat = env('HOTSPOT_OFF_BAT', base_path('scripts/hotspot_off.bat'));

            if (!file_exists($offBat)) {
                Log::error('Script OFF tidak ditemukan', ['path' => $offBat]);
                return response()->json(['error' => 'Script tidak ditemukan'], 404);
            }

            $username = env('WINDOWS_USERNAME', 'Diki');
            $taskName = 'HotspotManualOff_' . time();
            $now = now()->addSeconds(3)->format('H:i:s');

            // Buat dan jalankan task untuk OFF
            $createTaskOff = sprintf(
                'schtasks /create /tn "%s" /tr "\"%s\"" /sc once /st %s /f /ru "%s"',
                $taskName,
                $offBat,
                $now,
                $username
            );

            exec($createTaskOff, $output, $returnVar);
            exec("schtasks /run /tn \"$taskName\"");

            Log::info('Manual hotspot OFF task dibuat', [
                'task_name' => $taskName,
                'time' => $now,
                'exit_code' => $returnVar
            ]);

            if ($returnVar !== 0) {
                return response()->json([
                    'error' => 'Gagal membuat task OFF',
                    'details' => implode("\n", $output)
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Hotspot akan dimatikan dalam beberapa detik',
                'task_name' => $taskName
            ]);
        } catch (\Throwable $e) {
            Log::error('Exception saat mematikan hotspot', ['message' => $e->getMessage()]);
            return response()->json([
                'error' => 'Gagal menjalankan script OFF',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
