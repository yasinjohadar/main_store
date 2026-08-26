<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class GdprService
{
    /**
     * Build a full export of everything the store holds about a customer.
     *
     * @return array<string, mixed>
     */
    public function exportUserData(User $user): array
    {
        $user->load([
            'addresses',
            'orders.items',
            'orders.addresses',
            'reviews',
            'wishlistProducts',
            'loyaltyPointTransactions',
        ]);

        return [
            'profile' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'loyalty_points_balance' => $user->loyalty_points_balance,
                'created_at' => optional($user->created_at)->toIso8601String(),
            ],
            'addresses' => $user->addresses->map(fn ($address) => $address->only([
                'type', 'name', 'phone', 'country', 'city', 'state',
                'postal_code', 'address_line_1', 'address_line_2', 'is_default',
            ]))->all(),
            'orders' => $user->orders->map(fn ($order) => [
                'order_number' => $order->order_number,
                'total' => (float) $order->total,
                'currency' => $order->currency,
                'created_at' => optional($order->created_at)->toIso8601String(),
                'items' => $order->items->map(fn ($item) => [
                    'product_name' => $item->product_name,
                    'quantity' => $item->quantity,
                    'unit_price' => (float) $item->unit_price,
                ])->all(),
            ])->all(),
            'reviews' => $user->reviews->map(fn ($review) => [
                'product_id' => $review->product_id,
                'rating' => $review->rating,
                'comment' => $review->comment ?? null,
                'created_at' => optional($review->created_at)->toIso8601String(),
            ])->all(),
            'wishlist' => $user->wishlistProducts->pluck('name')->all(),
            'loyalty_point_transactions' => $user->loyaltyPointTransactions->map(fn ($t) => [
                'amount' => $t->amount,
                'type' => $t->type,
                'description' => $t->description,
                'created_at' => optional($t->created_at)->toIso8601String(),
            ])->all(),
        ];
    }

    /**
     * Anonymize a user's personal data in place. Orders/payments are kept
     * (accounting/legal retention) but stripped of the ability to identify
     * the customer going forward; delivery addresses are removed outright.
     */
    public function anonymizeUser(User $user): void
    {
        $user->addresses()->delete();

        $user->update([
            'name' => 'مستخدم محذوف',
            'email' => 'deleted-' . $user->id . '-' . Str::random(8) . '@deleted.local',
            'username' => null,
            'phone' => null,
            'google_id' => null,
            'facebook_id' => null,
            'photo' => null,
            'is_active' => false,
            'password' => Hash::make(Str::random(40)),
            'stripe_customer_id' => null,
            'remember_token' => null,
        ]);
    }
}
