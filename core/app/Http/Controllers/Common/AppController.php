<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Services\CurrencyConversionService;
use Exception;
use Illuminate\Http\JsonResponse;

class AppController extends Controller
{
    /**
     * Get the currency rate.
     *
     * @throws Exception
     */
    public function getCurrencyRate(string $fromCurrency, string $toCurrency, CurrencyConversionService $currencyConversionService): JsonResponse
    {
        return response()->json(['rate' => $currencyConversionService->convertCurrency(1.0, $fromCurrency, $toCurrency)]);
    }
}
