<?php

namespace App\Filament\Business\Resources\ContractResource\Pages;

use App\Filament\Business\Resources\ContractResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditContract extends EditRecord
{
    protected static string $resource = ContractResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make()->label('Shartnomani bekor qilish'),
            Action::make('delete')
                ->label('Shartnomani bekor qilish')
                ->action(fn($record) => $record->update(['status_id' => '5'])) // Statusni "cancelled" qilib o‘zgartirish
                ->color('danger')  // Tugma rangi
                ->visible(fn ($record) =>  $record->status->key == 'active')
                ->requiresConfirmation(), // Tasdiqlash oynasi
            Action::make('activation')
                ->label('Shartnomani faollashtirish')
                // ->action(fn($record) => $record->update(['status_id' => '3'])) // Statusni "cancelled" qilib o‘zgartirish
                ->color('primary')  // Tugma rangi
                ->visible(fn ($record) => ($record->status->key == 'pending' || $record->status->key == 'initial_payment'))
                ->requiresConfirmation() // Tasdiqlash oynasi
                ->action(function ($record) {
                    if ($record->status_id != 3) {
                        $record->update(['status_id' => 3]);
            
                        Notification::make()
                            ->title('Shartnoma muvaffaqiyatli o‘zgartirildi')
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Shartnoma allaqachon faollashtirilgan')
                            ->warning()
                            ->send();
                    }
                }),
        ];
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    
}
