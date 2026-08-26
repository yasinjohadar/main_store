<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\ShoppingCart;
use App\Services\EmailSender;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AbandonedCartRecoveryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(EmailSender $emailSender): void
    {
        if (! config('store.abandoned_cart_recovery_enabled', true)) {
            return;
        }

        $hours = (int) config('store.abandoned_cart_hours', 3);
        if ($hours <= 0) {
            return;
        }

        $carts = ShoppingCart::query()
            ->whereNotNull('user_id')
            ->whereNull('reminder_sent_at')
            ->where('updated_at', '<=', now()->subHours($hours))
            ->whereHas('items')
            ->with(['user', 'items.product'])
            ->limit(200)
            ->get();

        foreach ($carts as $cart) {
            if (! $cart->user?->email) {
                continue;
            }

            $hasOrderedSince = Order::query()
                ->where('user_id', $cart->user_id)
                ->where('created_at', '>=', $cart->updated_at)
                ->exists();

            if ($hasOrderedSince) {
                $cart->update(['reminder_sent_at' => now()]);
                continue;
            }

            $itemsList = $cart->items
                ->map(fn ($item) => ($item->product->name ?? '—') . ' × ' . $item->quantity)
                ->implode('<br>');

            $emailSender->send('abandoned_cart', $cart->user, [
                'customer_name' => $cart->user->name,
                'cart_items_list' => $itemsList,
                'cart_url' => route('frontend.cart.index'),
            ]);

            $cart->update(['reminder_sent_at' => now()]);
        }
    }
}
