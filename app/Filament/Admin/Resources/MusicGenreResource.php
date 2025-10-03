<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\MusicGenreResource\Pages;
use App\Filament\Admin\Resources\MusicGenreResource\RelationManagers;
use App\Models\MusicGenre;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;

class MusicGenreResource extends Resource
{
    protected static ?string $model = MusicGenre::class;

    protected static ?string $navigationIcon = null;

    protected static ?string $navigationLabel = 'Music Genres';

    protected static ?string $modelLabel = 'Music Genre';

    protected static ?string $pluralModelLabel = 'Music Genres';

    protected static ?string $navigationGroup = 'Entertainment Management';

    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Genre Name')
                    ->required()
                    ->maxLength(255)
                    ->prefixIcon('heroicon-o-musical-note')
                    ->helperText('e.g., Electronic, Hip-Hop, Rock, Commercial'),

                Select::make('category')
                    ->label('Category')
                    ->options([
                        'Electronic' => 'Electronic',
                        'Commercial' => 'Commercial',
                        'Hip-Hop' => 'Hip-Hop',
                        'Rock' => 'Rock',
                        'Other' => 'Other',
                    ])
                    ->required()
                    ->prefixIcon('heroicon-o-tag'),

                Textarea::make('description')
                    ->label('Description')
                    ->rows(3)
                    ->helperText('Brief description of this music genre'),

                Toggle::make('is_popular')
                    ->label('Popular')
                    ->helperText('Mark as popular for easier venue selection')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->searchOnBlur()
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('category')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Electronic' => 'warning',
                        'Commercial' => 'success',
                        'Hip-Hop' => 'info',
                        'Rock' => 'danger',
                        'Other' => 'gray',
                        default => 'gray',
                    }),

                TextColumn::make('description')
                    ->limit(50)
                    ->searchable(),

                IconColumn::make('is_popular')
                    ->boolean(),

                TextColumn::make('venues_count')
                    ->label('Venues')
                    ->counts('venues')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->options([
                        'Electronic' => 'Electronic',
                        'Commercial' => 'Commercial',
                        'Hip-Hop' => 'Hip-Hop',
                        'Rock' => 'Rock',
                        'Other' => 'Other',
                    ]),

                TernaryFilter::make('is_popular')
                    ->label('Popular'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->successNotificationTitle(''),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->icon('heroicon-o-trash')
                        ->label('Delete Selected')


                ]),
            ])
            ->defaultSort('category', 'asc')
            ->defaultPaginationPageOption(25);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMusicGenres::route('/'),
            'create' => Pages\CreateMusicGenre::route('/create'),
            'edit' => Pages\EditMusicGenre::route('/{record}/edit'),
        ];
    }
}
