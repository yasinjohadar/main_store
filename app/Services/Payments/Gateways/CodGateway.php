<?php

namespace App\Services\Payments\Gateways;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentMethod;

class CodGateway implements PaymentGatewayInterface
{
    public function driver(): string
    {
        return 'cod';
    }

    public function initiate(PaymentMethod $method, Order $order, Payment $payment): array
    {
        $payment->update(['status' => 'pending']);

        return ['status' => 'pending'];
    }

    public function handleWebhook(array $payload): array
    {
        return ['success' => false];
    }

    public function refund(Payment $payment, float $amount): array
    {
        return ['success' => true];
    }
}
