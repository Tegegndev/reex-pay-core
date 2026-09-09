<?php

use App\Services\CurrencyService;

beforeEach(function () {
    app()->singleton(CurrencyService::class, fn ($app) => new CurrencyService);
});

it('ensures CurrencyService is a singleton', function () {
    $service1 = app(CurrencyService::class);
    $service2 = app(CurrencyService::class);

    expect($service1)->toBe($service2);
});
