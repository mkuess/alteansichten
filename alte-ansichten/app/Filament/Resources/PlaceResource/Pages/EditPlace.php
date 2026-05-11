<?php

namespace App\Filament\Resources\PlaceResource\Pages;

use App\Filament\Resources\PlaceResource;
use App\Services\QrCodeService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditPlace extends EditRecord
{
    protected static string $resource = PlaceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createQrCode')
                ->label('QR-Code erstellen')
                ->icon('heroicon-o-qr-code')
                ->color('info')
                ->action(function () {
                    $result = app(QrCodeService::class)->createForPlace($this->record);

                    if ($result === false) {
                        Notification::make()
                            ->title('QR-Code bereits vorhanden')
                            ->warning()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('QR-Code erfolgreich erstellt')
                            ->success()
                            ->send();
                    }
                }),

            DeleteAction::make(),
        ];
    }
}
