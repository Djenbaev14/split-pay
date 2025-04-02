<?php

namespace App\Filament\Business\Resources\TariffResource\Pages;

use App\Filament\Business\Resources\TariffResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTariff extends EditRecord
{
    protected static string $resource = TariffResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
