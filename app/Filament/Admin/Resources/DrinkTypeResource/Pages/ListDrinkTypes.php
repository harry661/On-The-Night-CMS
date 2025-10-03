<?php

namespace App\Filament\Admin\Resources\DrinkTypeResource\Pages;

use App\Filament\Admin\Resources\DrinkTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDrinkTypes extends ListRecords
{
    protected static string $resource = DrinkTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
