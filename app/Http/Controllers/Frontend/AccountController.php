<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\OrderDownload;

class AccountController extends Controller
{
    public function index()
    {
        return $this->renderTab('dashboard');
    }

    public function orders()
    {
        return $this->renderTab('orders');
    }

    public function downloads()
    {
        return $this->renderTab('downloads');
    }

    public function addresses()
    {
        return $this->renderTab('addresses');
    }

    public function details()
    {
        return $this->renderTab('details');
    }

    public function showOrder($orderId)
    {
        $user = auth()->user();

        $order = $user->orders()
            ->with(['status', 'items.product', 'addresses', 'payments', 'shippingMethod'])
            ->findOrFail($orderId);

        $accountStats = [
            'orders' => $user->orders()->count(),
            'downloads' => OrderDownload::query()
                ->whereHas('order', fn ($q) => $q->where('user_id', $user->id))
                ->count(),
            'wishlist' => $user->wishlists()->count(),
            'addresses' => $user->addresses()->count(),
        ];

        return view('frontend.pages.account.order', compact('order', 'accountStats'));
    }

    protected function renderTab(string $tab)
    {
        $user = auth()->user();

        $orders = $user->orders()
            ->with(['status', 'items'])
            ->latest()
            ->paginate(10);

        $downloads = OrderDownload::query()
            ->whereHas('order', fn ($q) => $q->where('user_id', $user->id))
            ->with(['order', 'item', 'file'])
            ->latest()
            ->get();

        $billingAddress = $user->addresses()->where('type', 'billing')->orderByDesc('is_default')->first();
        $shippingAddress = $user->addresses()->where('type', 'shipping')->orderByDesc('is_default')->first();

        $latestOrder = $user->orders()->with('addresses')->latest()->first();

        if (! $billingAddress && $latestOrder?->billing_address) {
            $billingAddress = $latestOrder->billing_address;
        }

        if (! $shippingAddress && $latestOrder?->shipping_address) {
            $shippingAddress = $latestOrder->shipping_address;
        }

        $accountStats = [
            'orders' => $user->orders()->count(),
            'downloads' => OrderDownload::query()
                ->whereHas('order', fn ($q) => $q->where('user_id', $user->id))
                ->count(),
            'wishlist' => $user->wishlists()->count(),
            'addresses' => $user->addresses()->count(),
        ];

        $dashboardStats = $tab === 'dashboard' ? $accountStats : null;

        return view('frontend.pages.account.index', compact(
            'tab',
            'orders',
            'downloads',
            'billingAddress',
            'shippingAddress',
            'latestOrder',
            'dashboardStats',
            'accountStats'
        ))->with('activeTab', $tab);
    }
}
