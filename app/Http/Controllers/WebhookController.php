<?php

namespace App\Http\Controllers;

use App\Services\Payments\Gateways\PayPalGateway;
use App\Services\Payments\Gateways\StripeGateway;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class WebhookController extends Controller
{
    public function stripe(Request $request, StripeGateway $gateway): Response
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');

        $result = $gateway->handleSignedWebhook($payload, $signature);

        if (! ($result['success'] ?? false)) {
            $status = str_contains($result['message'] ?? '', 'signature') ? 400 : 422;

            return response($result['message'] ?? 'Webhook error', $status);
        }

        return response('OK', 200);
    }

    public function paypal(Request $request, PayPalGateway $gateway)
    {
        $result = $gateway->handleWebhook($request->all());

        return response()->json($result);
    }
}
