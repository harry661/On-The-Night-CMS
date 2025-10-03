<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\DrinkTypeResource\Pages;
use App\Filament\Admin\Resources\DrinkTypeResource\RelationManagers;
use App\Models\DrinkType;
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

class DrinkTypeResource extends Resource
{
    protected static ?string $model = DrinkType::class;

    protected static ?string $navigationIcon = null;

    protected static ?string $navigationLabel = 'Drink Types';

    protected static ?string $modelLabel = 'Drink Type';

    protected static ?string $pluralModelLabel = 'Drink Types';

    protected static ?string $navigationGroup = 'Entertainment Management';

    protected static ?int $navigationSort = 2;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Drink Name')
                    ->required()
                    ->maxLength(255)
                    ->prefixIcon('heroicon-o-beaker')
                    ->helperText('e.g., Lager, Whisky, Martini'),

                Select::make('category')
                    ->label('Category')
                    ->options([
                        'Beer' => 'Beer',
                        'Spirits' => 'Spirits',
                        'Cocktails' => 'Cocktails',
                        'Wine' => 'Wine',
                        'Non-Alcoholic' => 'Non-Alcoholic',
                    ])
                    ->required()
                    ->prefixIcon('heroicon-o-tag'),

                Textarea::make('description')
                    ->label('Description')
                    ->rows(3)
                    ->helperText('Brief description of this drink type'),

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
                        'Beer' => 'success',
                        'Spirits' => 'warning',
                        'Cocktails' => 'info',
                        'Wine' => 'danger',
                        'Non-Alcoholic' => 'gray',
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
                        'Beer' => 'Beer',
                        'Spirits' => 'Spirits',
                        'Cocktails' => 'Cocktails',
                        'Wine' => 'Wine',
                        'Non-Alcoholic' => 'Non-Alcoholic',
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
            'index' => Pages\ListDrinkTypes::route('/'),
            'create' => Pages\CreateDrinkType::route('/create'),
            'edit' => Pages\EditDrinkType::route('/{record}/edit'),
        ];
    }
}
