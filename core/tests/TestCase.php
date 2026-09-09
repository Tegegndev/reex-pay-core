<?php

namespace Tests;

use App\Services\CurrencyService;
use App\Services\PaymentService;
use App\Services\TransactionService;
use App\Services\WalletService;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /** @test */
    public function it_ensures_currency_service_is_a_singleton(): void
    {
        $instance1 = app(CurrencyService::class);
        $instance2 = app(CurrencyService::class);

        $this->assertSame($instance1, $instance2);
    }

    /** @test */
    public function it_ensures_wallet_service_is_a_singleton(): void
    {
        $instance1 = app(WalletService::class);
        $instance2 = app(WalletService::class);

        $this->assertSame($instance1, $instance2);
    }

    /** @test */
    public function it_ensures_transaction_service_is_a_singleton(): void
    {
        $instance1 = app(TransactionService::class);
        $instance2 = app(TransactionService::class);

        $this->assertSame($instance1, $instance2);
    }

    /** @test */
    public function it_ensures_payment_service_is_a_singleton(): void
    {
        $instance1 = app(PaymentService::class);
        $instance2 = app(PaymentService::class);

        $this->assertSame($instance1, $instance2);
    }

    /** @test */
    public function it_ensures_different_services_are_distinct(): void
    {
        $currencyService = app(CurrencyService::class);
        $walletService   = app(WalletService::class);

        $this->assertNotSame($currencyService, $walletService);
    }
}
