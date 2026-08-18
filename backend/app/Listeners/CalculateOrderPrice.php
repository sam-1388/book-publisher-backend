<?php

namespace App\Listeners;

use App\Events\ItemsPriced;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CalculateOrderPrice
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
    public function handle(ItemsPriced $event): void
    {
        $finalPrice = $event->order->orderItems()->sum('total_price_in_cents');
        $event->order->update([
            'final_price_in_cents'=>$finalPrice
        ]);
    }
}
