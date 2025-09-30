<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as BaseEventServiceProvider;

class EventServiceProvider extends BaseEventServiceProvider
{
    protected $listen = [
        \PujaNaik\UserDiscount\Events\DiscountAssigned::class => [
            \App\Listeners\LogDiscountEvent::class,
        ],
        \PujaNaik\UserDiscount\Events\DiscountRevoked::class => [
            \App\Listeners\LogDiscountEvent::class,
        ],
        \PujaNaik\UserDiscount\Events\DiscountApplied::class => [
            \App\Listeners\LogDiscountEvent::class,
        ],
    ];

    public function boot(): void
    {
        parent::boot();
    }
}
