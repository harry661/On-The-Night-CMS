<?php

namespace App\Filament\Admin\Resources\VenueTypeResource\Pages;

use App\Filament\Admin\Resources\VenueTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVenueTypes extends ListRecords
{
    protected static string $resource = VenueTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
