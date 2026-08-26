<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Jobs\SendOrderEmailJob;
use App\Services\SiteSettingsService;

class SendAdminNewOrderEmail
{
    public function __construct(
        protected SiteSettingsService $settings,
    ) {}

    public function handle(OrderPlaced $event): void
    {
        $order = $event->order->load(['items', 'status']);
        $adminEmail = $this->settings->get(
            SiteSettingsService::KEY_SITE_CONTACT_EMAIL,
            config('mail.from.address')
        );

        if (! $adminEmail) {
            return;
        }

        SendOrderEmailJob::dispatch('new_order_admin', $adminEmail, [
            'order' => $order,
            'order_number' => $order->order_number,
            'total' => number_format((float) $order->total, 2),
        ]);
    }
}
