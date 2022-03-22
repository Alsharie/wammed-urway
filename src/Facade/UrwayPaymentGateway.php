<?php

namespace Wameed\UrwayPaymentGateway\Facade;

use Illuminate\Support\Facades\Facade;
use Wameed\UrwayPaymentGateway\Urway;

class UrwayPaymentGateway extends Facade
{
    /**
     * Get the binding in the IoC container
     *
     */
    protected static function getFacadeAccessor()
    {
        return Urway::class;
    }
}