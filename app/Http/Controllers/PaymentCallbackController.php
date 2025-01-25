<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Filament\Resources\IncomeResource;

class MidtransCallbackController extends Controller
{
    public function handle(Request $request)
    {
        try {
            $notification = new \Midtrans\Notification();
            
            $transaction = $notification->transaction_status;
            $type = $notification->payment_type;
            $orderId = $notification->order_id;
            $fraud = $notification->fraud_status;

            $paymentId = str_replace('PAY-', '', $orderId);
            $payment = Payment::findOrFail($paymentId);

            DB::beginTransaction();

            if ($transaction == 'capture') {
                if ($type == 'credit_card') {
                    if($fraud == 'challenge') {
                        $payment->payment_status = 'pending';
                    } else {
                        $payment->payment_status = 'paid';
                    }
                }
            } else if ($transaction == 'settlement') {
                $payment->payment_status = 'paid';
            } else if ($transaction == 'pending') {
                $payment->payment_status = 'pending';
            } else if ($transaction == 'deny') {
                $payment->payment_status = 'failed';
            } else if ($transaction == 'expire') {
                $payment->payment_status = 'expired';
            } else if ($transaction == 'cancel') {
                $payment->payment_status = 'cancelled';
            }

            $payment->save();

            if ($payment->payment_status === 'paid') {
                $payment->bill->update(['status' => 'paid']);
                IncomeResource::createFromPayment($payment);
            }

            DB::commit();

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Payment callback processing failed:', [
                'error' => $e->getMessage(),
                'request' => $request->all(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}