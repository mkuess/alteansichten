<?php

namespace App\Filament\Resources\PlaceResource\Pages;

use App\Filament\Resources\PlaceResource;
use App\Models\Municipality;
use App\Services\GeocodingService;
use App\Services\QrCodeService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;

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

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (empty($this->record->slug)) {
            $data['slug'] = Str::slug($data['title'] ?? '');
        }

        if (empty($data['latitude']) && empty($data['longitude'])) {
            $coords = app(GeocodingService::class)->resolveCoordinates(
                $this->buildAddressParts($data)
            );

            if ($coords) {
                $data['latitude']  = $coords['latitude'];
                $data['longitude'] = $coords['longitude'];
            }
        }

        return $data;
    }

    private function buildAddressParts(array $data): array
    {
        $city = null;
        if (! empty($data['municipality_id'])) {
            $municipality = Municipality::find($data['municipality_id']);
            $city = $municipality?->name;
        }

        return [
            'street'       => $data['street'] ?? null,
            'house_number' => $data['house_number'] ?? null,
            'postal_code'  => $data['postal_code'] ?? null,
            'city'         => $city ?? ($data['address_text'] ?? null),
            'country'      => null,
        ];
    }
}
