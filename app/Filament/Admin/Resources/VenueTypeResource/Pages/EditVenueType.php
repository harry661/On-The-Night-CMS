<?php

namespace App\Filament\Admin\Resources\VenueTypeResource\Pages;

use App\Filament\Admin\Resources\VenueTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVenueType extends EditRecord
{
    protected static string $resource = VenueTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
