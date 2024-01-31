<?php

namespace DonorServices\FilamentModule\Actions;

use DonorServices\FilamentModule\Models\Module;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class ScanModulesAction
{
    public static function make()
    {
        return Action::make('scan_modules')
            ->action(function () {
                try {
                    Module::dispatchRescanJob();
                    Notification::make()
                        ->title('Scan of modules started')
                        ->success()
                        ->send();
                } catch (\Exception $e) {
                    Notification::make()
                        ->title('Failed')
                        ->body($e)
                        ->danger()
                        ->send();
                }
            });
    }
}
