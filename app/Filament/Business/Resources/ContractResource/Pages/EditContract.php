<?php

namespace App\Filament\Business\Resources\ContractResource\Pages;

use App\Filament\Business\Resources\ContractResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditContract extends EditRecord
{
    protected static string $resource = ContractResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make()->label('Shartnomani bekor qilish'),
            Action::make('delete')
                ->label('Shartnomani bekor qilish')
                ->action(fn($record) => $record->update(['status_id' => '4'])) // Statusni "cancelled" qilib o‘zgartirish
                ->color('danger')  // Tugma rangi
                ->visible(fn ($record) =>  $record->status->key == 'active')
                ->requiresConfirmation(), // Tasdiqlash oynasi
            Action::make('activation')
                ->label('Shartnomani faollashtirish')
                ->action(fn($record) => $record->update(['status_id' => '2'])) // Statusni "cancelled" qilib o‘zgartirish
                ->color('info')  // Tugma rangi
                ->visible(fn ($record) => ($record->status->key == 'pending'))
                ->requiresConfirmation() // Tasdiqlash oynasi
        ];
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    
}
