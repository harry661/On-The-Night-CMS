<?php

namespace App\Filament\Admin\Resources\DrinkTypeResource\Pages;

use App\Filament\Admin\Resources\DrinkTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDrinkType extends EditRecord
{
    protected static string $resource = DrinkTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
