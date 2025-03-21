@component('mail::message')
# {{ __('Your Order Has Been Shipped!') }}

{{ __('Dear :customer_name,', ['customer_name' => $order->user->name]) }}

{{ __('We are happy to inform you that your order #:order_id has been shipped.', ['order_id' => $order->order_id]) }}

### **Order Details:**
- **Product Name:** {{ $order->product->name ?? 'N/A' }}
- **Brand Name:** {{ $order->product->brand->name ?? 'N/A' }}
- **Amount:** {{ number_format($order->amount, 2) }} {{ $order->currency ?? 'USD' }}

{{ __('If you did not place this order, please contact our support team immediately.') }}

Thanks,  
{{ config('app.name') }}
@endcomponent
