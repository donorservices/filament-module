<?php

namespace DonorServices\FilamentModule\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \DonorServices\FilamentModule\FilamentModule
 */
class FilamentModule extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \DonorServices\FilamentModule\FilamentModule::class;
    }
}
