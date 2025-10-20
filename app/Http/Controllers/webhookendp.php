<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class Webhookendp extends Controller
{
    public function editData(Request $request)
    {
        $notification = $request->all();

        if (empty($notification)) {
            return response()->json(['status' => 'Data not found'], 400);
        }

        if (!isset($notification['signature_key'], $notification['order_id'], $notification['status_code'], $notification['gross_amount'])) {
            return response()->json(['status' => 'Invalid data format'], 400);
        }

        $serverKey = env('MIDTRANS_SERVER_KEY');
        $signatureKey = hash('sha512',
            $notification['order_id'] .
            $notification['status_code'] .
            $notification['gross_amount'] .
            $serverKey
        );

        if ($notification['signature_key'] !== $signatureKey) {
            return response()->json(['status' => 'Invalid signature'], 403);
        }

        $orderId = $notification['order_id'];
        $transactionStatus = $notification['transaction_status'];

        // update database
        $order = Order::where('order_id', $orderId)->first();
        if ($order) {
            $order->status = $transactionStatus;
            $order->save();
        }

        // update cache
        Cache::put($orderId, $transactionStatus);

        Log::info('Midtrans update received', [
            'order_id' => $orderId,
            'status' => $transactionStatus
        ]);

        return response()->json(['status' => 'ok'], 200);
    }

    public function getData(Request $req)
    {
        $req->validate(['key' => 'required|string|max:255']);
        $value = Cache::get($req->key, 'canceled');
        return response(['state' => $value], 200);
    }
}
