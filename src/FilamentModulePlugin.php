<?php

namespace DonorServices\FilamentModule;

use DonorServices\FilamentModule\Models\Module;
use DonorServices\FilamentModule\Resources\ModuleResource;
use Filament\Contracts\Plugin;
use Filament\Panel;

class FilamentModulePlugin implements Plugin
{
    public function getId(): string
    {
        return 'filament-module';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                ModuleResource::class,
                ...Module::activeResources()
            ])
            ->pages([
                ...Module::activePages()
            ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }
}
