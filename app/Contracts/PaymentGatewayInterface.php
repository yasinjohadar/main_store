<?php

namespace App\Contracts;

use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentMethod;

interface PaymentGatewayInterface
{
    public function driver(): string;

    /** @return array{redirect_url?: string, status?: string} */
    public function initiate(PaymentMethod $method, Order $order, Payment $payment): array;

    /** @return array{success: bool, status?: string} */
    public function handleWebhook(array $payload): array;

    /** @return array{success: bool, refund_id?: string} */
    public function refund(Payment $payment, float $amount): array;
}
