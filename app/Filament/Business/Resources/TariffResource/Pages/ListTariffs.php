<?php

namespace App\Filament\Business\Resources\TariffResource\Pages;

use App\Filament\Business\Resources\TariffResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTariffs extends ListRecords
{
    protected static string $resource = TariffResource::class;

    // protected function getHeaderActions(): array
    // {
    //     return [
    //         Actions\CreateAction::make(),
    //     ];
    // }
}
