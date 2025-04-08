<?php

namespace App\Filament\Business\Resources\ContractResource\Pages;

use App\Filament\Business\Resources\ContractResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;

class ListContracts extends ListRecords
{
    protected static string $resource = ContractResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    protected function getHeaderWidgets(): array
    {
        return ContractResource::getWidgets();
    }
    public function getTabs(): array
    {
        $query = $this->getTableQuery();
        $allCount = $query->count();
        $pendingCount = $query->clone()->where('status_id', 1)->count();
        $initialPayment = $query->clone()->where('status_id', 2)->count();
        $activePayment = $query->clone()->where('status_id', 3)->count();
        $completedCount = $query->clone()->where('status_id', 4)->count();
        $cancelledCount = $query->clone()->where('status_id', 5)->count();
        return [
            null => Tab::make('Все')->badge($allCount),
            'Активен' => Tab::make()
                ->label('Активен')
                ->badge($activePayment)
                ->query(fn ($query) => $query->where('status_id', 3)),
            'Ожидает подтверждение' => Tab::make()
                ->label('Ожидает подтверждение')
                ->badge($pendingCount)
                ->query(fn ($query) => $query->where('status_id', 1)),
            'Ожидает первоначальный взнос' => Tab::make()
                ->label('Ожидает первоначальный взнос')
                ->badge($initialPayment)
                ->query(fn ($query) => $query->where('status_id', 2)),
            'Успешно завершено' => Tab::make()
                ->label('Успешно завершено')
                ->badge($completedCount)
                ->query(fn ($query) => $query->where('status_id', 4)),
            'Отменён' => Tab::make()
                ->label('Отменён')
                ->badge($cancelledCount)
                ->query(fn ($query) => $query->where('status_id', 5)),
        ];
    }
}
