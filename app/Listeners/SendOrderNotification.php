<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Mail\OrderShippedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;


class SendOrderNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderPlaced $event): void
    {
        $userEmail = $event->order->user->email ?? null;

        // Mail::to($userEmail)->send(new OrderShippedMail($event->order));
        Mail::to($userEmail)->queue(new OrderShippedMail($event->order)); // Use queue()


    }
}
