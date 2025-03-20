<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Services\RazorpayService;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\RazorpayPaymentRequest;
use Carbon\Carbon;

class PaymentController extends Controller
{
    protected $razorpayService;

    public function __construct(RazorpayService $razorpayService)
    {
        $this->razorpayService = $razorpayService;
    }

    /**
     * Show all orders for the authenticated user
     */
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())->with('product')->latest()->paginate(6);
        return Inertia::render('Frontend/Order/Index', ['orders' => $orders]);
    }

    /**
     * Show the payment page with Razorpay
     */
    public function create($productId)
    {
        $product = Product::findOrFail($productId);
        $amount = $product->price;
        $userId = Auth::id();
        $order = $this->razorpayService->createOrder($amount);
        $newOrder = Order::create([
            'order_id' => $order['id'],
            'user_id' => $userId, 
            'product_id' => $productId,
            'amount' => $amount,
            'currency' => 'INR',
            'status' => 'pending',
        ]);
        return redirect()->route('payment.show', ['orderId' => $newOrder->order_id]);
    }

    public function show($orderId)
    {
        $order = Order::where('order_id', $orderId)->with('user')->firstOrFail();        
        $user = $order->user;         
        return Inertia::render('Frontend/Product/Payment', [
            'orderId' => $order->order_id,
            'amount' => (float) $order->amount,
            'razorpayKey' => env('RAZORPAY_KEY'),
            'user' => [
                'name' => $user->name ?? 'Guest',
                'email' => $user->email ?? 'guest@example.com',
            ],
        ]);
    }

    /**
     * Handle Payment Verification
     */
    public function verify(RazorpayPaymentRequest $request)
    {
        $verificationResult = $this->razorpayService->verifyPayment($request);
        if (isset($verificationResult['error'])) {
            return redirect()->route('products.index')->with('error', $verificationResult['error']);
        }
        return redirect()->route('products.index')->with('success', 'Payment successful! Your order has been confirmed.');
    }

    /**
     * Handle Cancel order
     */
    public function cancelOrder($orderId)
    {
        $order = Order::findOrFail($orderId);
        if ($order->status === 'paid') {
            return $this->handlePaidOrder($order);
        }
        if ($order->status === 'pending') {
            return $this->handlePendingOrder($order);
        }
        return redirect()->route('orders.index')->with('error', 'Invalid order status for cancellation.');
    }
    
    /**
     * Handle Paid Order Cancel
     */
    private function handlePaidOrder($order)
    {
        $orderCreatedAt = Carbon::parse($order->created_at);
        $now = Carbon::now();
        if ($now->diffInHours($orderCreatedAt) <= 24 && $order->product && $order->product->trashed()) {
            $order->product->restore();
        }
        $order->update(['status' => 'cancelled']);
        return redirect()->route('orders.index')->with('success', 'Order has been cancelled successfully.');
    }
    
    
    /**
     * Handle Pending Order Cancel
     */
    private function handlePendingOrder($order)
    {
        $orderCreatedAt = Carbon::parse($order->created_at);
        $now = Carbon::now();
        if ($now->diffInHours($orderCreatedAt) > 24) {
            $order->update(['status' => 'cancelled']);
        } else{
            $order->update(['status' => 'cancelled']);
            return redirect()->route('orders.index')->with('success', 'Pending order has been cancelled successfully.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $order = Order::findOrFail($id);
        $order->delete();
        return redirect()->route('orders.index')->with('success', 'Order deleted successfully.');
    }
}

