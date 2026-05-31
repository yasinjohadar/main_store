<?php

namespace App\Services\Payments;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Services\Payments\Gateways\BankTransferGateway;
use App\Services\Payments\Gateways\CodGateway;
use App\Services\Payments\Gateways\PayPalGateway;
use App\Services\Payments\Gateways\StripeGateway;

class PaymentGatewayManager
{
    /** @var array<string, PaymentGatewayInterface> */
    protected array $gateways = [];

    public function __construct()
    {
        $this->register(new CodGateway);
        $this->register(new BankTransferGateway);
        $this->register(new PayPalGateway);
        $this->register(app(StripeGateway::class));
    }

    public function register(PaymentGatewayInterface $gateway): void
    {
        $this->gateways[$gateway->driver()] = $gateway;
    }

    public function resolve(PaymentMethod $method): PaymentGatewayInterface
    {
        $driver = $method->driver ?? 'cod';

        if (in_array($driver, ['stripe', 'card'], true)) {
            return $this->gateways['stripe'] ?? $this->gateways['cod'];
        }

        if (! isset($this->gateways[$driver])) {
            return $this->gateways['cod'];
        }

        return $this->gateways[$driver];
    }

    public function initiate(PaymentMethod $method, Order $order, Payment $payment): array
    {
        return $this->resolve($method)->initiate($method, $order, $payment);
    }
}
