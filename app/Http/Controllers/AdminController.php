<?php

namespace App\Http\Controllers;

use App\Models\Order; // Import model Order
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Menampilkan formulir login admin.
     */
    public function showLoginForm()
    {
        // Jika sudah login, arahkan ke dashboard order
        if (Auth::check()) {
            return redirect()->route('admin.orders');
        }
        return view('admin_login');
    }

    /**
     * Menangani proses login admin menggunakan tabel 'users'.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            // Menggunakan 'email' dan 'password' untuk Auth::attempt() standar
            'email' => 'required|email', 
            'password' => 'required|string',
        ]);
        
        // Opsional: Pastikan email yang login adalah admin@wifi.id untuk keamanan sederhana
        if ($credentials['email'] !== 'admin@wifi.id') {
            return redirect()->back()->withErrors(['email' => 'Email atau password salah.'])->withInput($request->except('password'));
        }

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            // Sukses login
            return redirect()->route('admin.orders');
        }

        // Gagal login
        return redirect()->back()->withErrors(['password' => 'Email atau password salah.'])->withInput($request->except('password'));
    }

    /**
     * Menampilkan daftar semua order.
     */
    public function showOrders()
    {
        // Middleware 'auth' akan memastikan user sudah login.
        // Cek tambahan: memastikan user yang login adalah admin.
        if (Auth::user()->email !== 'admin@wifi.id') {
            // Jika user login, tapi bukan admin, log out dan kembalikan ke login
            Auth::logout();
            return redirect()->route('admin.login');
        }

        // Admin bisa melihat order tanpa bisa melakukan apapun (hanya view)
        $orders = Order::orderByDesc('created_at')->get();

        return view('admin_orders', compact('orders'));
    }

    /**
     * Menangani proses logout admin dan kembali ke /admin (halaman login).
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Tombol logout kembali ke /admin (login admin)
        return redirect()->route('admin.login'); 
    }
}
