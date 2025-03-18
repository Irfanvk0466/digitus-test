<?php

namespace App\Services;

use App\Models\Order;
use Razorpay\Api\Api;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\RazorpayPaymentRequest;

class RazorpayService
{
    protected $razorpay;

    public function __construct()
    {
        $this->razorpay = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
    }

    /**
     * Create a new Razorpay order
     */
    public function createOrder($amount, $currency = "INR")
    {
        try {
            return $this->razorpay->order->create([
                'amount' => $amount * 100,
                'currency' => $currency,
                'payment_capture' => 1
            ]);
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     *  Verify Razorpay Payment Signature
     */
    public function verifyPayment(RazorpayPaymentRequest $request)
    {
        try {
            $order = $this->getOrder($request->razorpay_order_id);
            if (!$this->isPaymentValid($request->razorpay_order_id, $request->razorpay_payment_id, $request->razorpay_signature)) {
                return ['error' => 'Payment verification failed.'];
            }
            $this->updateOrderDetails($order, $request);
            $this->deleteProductIfExists($order);
            return ['success' => true, 'message' => 'Payment verified successfully.'];
        } catch (Exception $e) {
            Log::error("Payment verification failed: " . $e->getMessage());
            return ['error' => 'Payment verification error.'];
        }
    }

    /**
     * Retrieve order by Razorpay Order ID
     */
    private function getOrder($razorpayOrderId)
    {
        return Order::where('order_id', $razorpayOrderId)->first();
    }

    /**
     * Validate Razorpay payment signature
     */
    private function isPaymentValid($razorpayOrderId, $razorpayPaymentId, $razorpaySignature)
    {
        $generatedSignature = hash_hmac('sha256', "{$razorpayOrderId}|{$razorpayPaymentId}", env('RAZORPAY_SECRET'));
        return $generatedSignature === $razorpaySignature;
    }

    /**
     * Update order details after successful payment verification
     */
    private function updateOrderDetails($order, $request)
    {
        $order->update([
            'payment_id' => $request->razorpay_payment_id,
            'status' => 'paid',
            'payment_method' => $request->payment_method,
            'bank_name' => $request->bank_name,
        ]);
    }

    /**
     * Delete product if it exists
     */
    private function deleteProductIfExists($order)
    {
        if ($order->product) {
            $order->product->delete();
        }
    }
}
