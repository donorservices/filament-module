<?php

namespace DonorServices\FilamentModule\Resources\ModuleResource\Pages;

use DonorServices\FilamentModule\Actions\ScanModulesAction;
use DonorServices\FilamentModule\Resources\ModuleResource;
use Filament\Resources\Pages\ManageRecords;

class ManageModules extends ManageRecords
{
    protected static string $resource = ModuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ScanModulesAction::make(),
//            Actions\CreateAction::make(),
        ];
    }
}
