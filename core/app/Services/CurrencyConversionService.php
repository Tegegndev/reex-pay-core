<?php

namespace App\Services;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CurrencyConversionService
{
    protected Client $coinMarketCapClient;

    protected Client $exchangeRateClient;

    protected array $fiatConversionData = [];

    protected array $cryptoConversionData = [];

    public function __construct()
    {

        $this->coinMarketCapClient = new Client([
            'base_uri' => 'https://pro-api.coinmarketcap.com/',
        ]);

        $this->exchangeRateClient = new Client([
            'base_uri' => 'https://api.currencylayer.com/',
        ]);

        $this->fiatConversionData   = $this->prepareAutoUpdateData('currencylayer');
        $this->cryptoConversionData = $this->prepareAutoUpdateData('coinmarketcap');

    }

    /**
     * Convert currency based on the type of conversion.
     *
     * @param  float      $amount The amount to be converted
     * @param  string     $from   The currency to convert from
     * @param  string     $to     The currency to convert to
     * @return float|null The converted amount
     *
     * @throws Exception
     */
    public function convertCurrency(float $amount, string $from, string $to): ?float
    {

        $cryptoCurrencies   = collect(getJsonData('currencies')['crypto'])->pluck('code');
        $isCryptoConversion = $cryptoCurrencies->contains($from) || $cryptoCurrencies->contains($to);

        return $isCryptoConversion ? $this->convertCryptoWithCache($amount, $from, $to) : $this->convertFiatWithCache($amount, $from, $to);
    }

    /**
     * Prepare auto-update data based on plugin credentials.
     */
    protected function prepareAutoUpdateData(string $plugin): array
    {
        $credentials = pluginCredentials($plugin);

        return array_merge(
            $credentials['fields'],
            [
                'api_key' => $credentials['api_key'],
                'status'  => $credentials['status'],
            ]
        );
    }

    /**
     * Convert cryptocurrency using CoinMarketCap API with caching.
     *
     * @param  float      $amount The amount to be converted
     * @param  string     $from   The cryptocurrency symbol to convert from
     * @param  string     $to     The currency symbol to convert to
     * @return float|null The converted amount or null on failure
     */
    protected function convertCryptoWithCache(float $amount, string $from, string $to): ?float
    {
        $type     = 'crypto';
        $cacheKey = $this->getCacheKey($amount, $from, $to, $type);

        return $this->cryptoConversionData['status'] === 1 ? $this->cacheConversionResult($cacheKey, $amount, $from, $to, $type) : $amount;
    }

    protected function getCacheKey(float $amount, string $from, string $to, string $type): string
    {
        return "{$type}_conversion_{$amount}_{$from}_{$to}";
    }

    /**
     * Cache the conversion result.
     *
     * @param string $type 'crypto' or 'fiat'
     */
    protected function cacheConversionResult(string $cacheKey, float $amount, string $from, string $to, string $type): ?float
    {

        $data     = $type === 'crypto' ? $this->cryptoConversionData : $this->fiatConversionData;
        $callback = $type === 'crypto' ? fn () => $this->fetchCryptoConversion($amount, $from, $to) : fn () => $this->fetchFiatConversion($amount, $from, $to);

        return ((int) $data['auto_update_status'] === 1) ? Cache::remember($cacheKey, now()->addMinutes((int) $data['auto_update_time']), $callback) : Cache::rememberForever($cacheKey, $callback);
    }

    /**
     * Fetch the cryptocurrency conversion from the CoinMarketCap API.
     *
     * @throws GuzzleException
     */
    protected function fetchCryptoConversion(float $amount, string $from, string $to): ?float
    {

        try {
            $response = $this->coinMarketCapClient->get('v2/tools/price-conversion', [
                'query' => [
                    'amount'  => $amount,
                    'symbol'  => $from,
                    'convert' => $to,
                ],
                'headers' => [
                    'Accept'            => 'application/json',
                    'X-CMC_PRO_API_KEY' => $this->cryptoConversionData['api_key'],
                ],
            ]);

            $data = json_decode($response->getBody(), true);

            return $data['data'][0]['quote'][$to]['price'] ?? null;
        } catch (Exception $e) {
            report($e);

            return null;
        }
    }

    /**
     * Fetch the fiat currency conversion from the ExchangeRate API.
     *
     * @throws GuzzleException
     */
    protected function fetchFiatConversion(float $amount, string $from, string $to)
    {

        try {
            $response = $this->exchangeRateClient->get('convert', [
                'query' => [
                    'access_key' => $this->fiatConversionData['api_key'],
                    'from'       => $from,
                    'to'         => $to,
                    'amount'     => $amount,
                ],
            ]);
            $data = json_decode($response->getBody(), true);

        } catch (Exception $e) {
            $data = json_decode($e->getResponse()->getBody()->getContents(), true);
            session()->flash('exchange_rate_error', $data['error']['info'] ?? 'ExchangeRate conversion failed.');

            return null;
        }

        return $data['success'] ? $data['result'] : null;
    }

    /**
     * Convert fiat currency using ExchangeRate API with caching.
     *
     * @param  float      $amount The amount to be converted
     * @param  string     $from   The fiat currency code to convert from
     * @param  string     $to     The currency code to convert to
     * @return float|null The converted amount or null on failure
     *
     * @throws Exception
     */
    protected function convertFiatWithCache(float $amount, string $from, string $to): ?float
    {
        $type     = 'fiat';
        $cacheKey = $this->getCacheKey($amount, $from, $to, $type);

        return $this->fiatConversionData['status'] === 1 ? $this->cacheConversionResult($cacheKey, $amount, $from, $to, $type) : $this->convertFiatTemporarily($from, $to, $amount);
    }

    /**
     * @throws Exception
     */
    private function convertFiatTemporarily(string $from, string $to, float $amount): ?float
    {
        $response = Http::get('https://api.frankfurter.app/latest', [
            'base'    => $from,
            'symbols' => $to,
        ]);

        if (! $response->successful()) {
            return round($amount, 2);
        }

        $data = $response->json();

        if (! isset($data['rates'][$to])) {
            throw new Exception('Invalid currency code.');
        }

        return round($amount * $data['rates'][$to], 2);
    }
}
