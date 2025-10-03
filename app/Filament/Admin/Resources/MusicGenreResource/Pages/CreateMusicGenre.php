<?php

namespace App\Filament\Admin\Resources\MusicGenreResource\Pages;

use App\Filament\Admin\Resources\MusicGenreResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMusicGenre extends CreateRecord
{
    protected static string $resource = MusicGenreResource::class;
}
