<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Services\CurrencyService;

class CurrencyController extends Controller
{
    public function switch(string $code, CurrencyService $currencyService)
    {
        $currency = Currency::where('code', $code)->active()->firstOrFail();
        $currencyService->setFrontendDisplayCurrency($currency->code);

        return back();
    }
}
