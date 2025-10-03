<?php

namespace App\Filament\Resources\VenueResource\Pages;

use App\Filament\Resources\VenueResource;
use Filament\Resources\Pages\CreateRecord;

class CreateVenue extends CreateRecord
{
    protected static string $resource = VenueResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // If user is venue moderator, automatically assign their user_id
        if (auth()->user()->isVenueModerator()) {
            $data['user_id'] = auth()->id();
        }

        return $data;
    }
}

