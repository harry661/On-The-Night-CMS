<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\VenueTypeResource\Pages;
use App\Filament\Admin\Resources\VenueTypeResource\RelationManagers;
use App\Models\VenueType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class VenueTypeResource extends Resource
{
    protected static ?string $model = VenueType::class;

    protected static ?string $navigationIcon = null;

    protected static ?string $navigationLabel = 'Venue Types';

    protected static ?string $navigationGroup = 'Venue Management';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Venue Type Details')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                                        if ($operation !== 'create') {
                                            return;
                                        }
                                        $set('slug', Str::slug($state));
                                    }),
                                
                                Forms\Components\TextInput::make('slug')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(VenueType::class, 'slug', ignoreRecord: true)
                                    ->helperText('URL-friendly version of the name'),
                            ]),
                        
                        Forms\Components\Textarea::make('description')
                            ->rows(3)
                            ->helperText('Brief description of this venue type'),
                        
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('icon')
                                    ->maxLength(255)
                                    ->helperText('Heroicon name (e.g., "home", "building-office")'),
                                
                                Forms\Components\TextInput::make('color')
                                    ->maxLength(255)
                                    ->helperText('Hex color code (e.g., "#FF5733")'),
                                
                                Forms\Components\TextInput::make('sort_order')
                                    ->numeric()
                                    ->default(0)
                                    ->helperText('Order for display (lower numbers first)'),
                            ]),
                        
                        Forms\Components\Toggle::make('is_active')
                            ->default(true)
                            ->helperText('Whether this venue type is active and available for selection'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('slug')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                
                Tables\Columns\TextColumn::make('description')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    }),
                
                Tables\Columns\TextColumn::make('venues_count')
                    ->counts('venues')
                    ->label('Venues')
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('sort_order')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status')
                    ->boolean()
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only')
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order');
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
            'index' => Pages\ListVenueTypes::route('/'),
            'create' => Pages\CreateVenueType::route('/create'),
            'edit' => Pages\EditVenueType::route('/{record}/edit'),
        ];
    }
}
