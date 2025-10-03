<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VenueResource\Pages;
use App\Models\Venue;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\ViewAction;

class VenueResource extends Resource
{
    protected static ?string $model = Venue::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $navigationLabel = 'Venues';

    protected static ?string $modelLabel = 'Venue';

    protected static ?string $pluralModelLabel = 'Venues';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Basic Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Select::make('user_id')
                                    ->label('Venue Moderator')
                                    ->relationship('user', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->visible(fn () => auth()->user()->isAdmin()),
                            ]),
                        Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Section::make('Location Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('address')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('city')
                                    ->required()
                                    ->maxLength(255),
                            ]),
                        Grid::make(3)
                            ->schema([
                                TextInput::make('state')
                                    ->maxLength(255),
                                TextInput::make('postal_code')
                                    ->maxLength(20),
                                TextInput::make('country')
                                    ->default('US')
                                    ->maxLength(255),
                            ]),
                        Grid::make(2)
                            ->schema([
                                TextInput::make('latitude')
                                    ->numeric()
                                    ->step(0.00000001),
                                TextInput::make('longitude')
                                    ->numeric()
                                    ->step(0.00000001),
                            ]),
                    ]),

                Section::make('Contact Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('phone')
                                    ->tel()
                                    ->maxLength(20),
                                TextInput::make('email')
                                    ->email()
                                    ->maxLength(255),
                            ]),
                        TextInput::make('website')
                            ->url()
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ]),

                Section::make('Venue Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('capacity')
                                    ->numeric()
                                    ->minValue(1),
                                Select::make('price_range')
                                    ->options([
                                        '$' => '$ (Budget)',
                                        '$$' => '$$ (Moderate)',
                                        '$$$' => '$$$ (Expensive)',
                                        '$$$$' => '$$$$ (Very Expensive)',
                                    ]),
                            ]),
                        KeyValue::make('opening_hours')
                            ->label('Opening Hours')
                            ->keyLabel('Day')
                            ->valueLabel('Hours')
                            ->addActionLabel('Add Day'),
                        KeyValue::make('amenities')
                            ->label('Amenities')
                            ->keyLabel('Amenity')
                            ->valueLabel('Description')
                            ->addActionLabel('Add Amenity'),
                    ]),

                Section::make('Media')
                    ->schema([
                        FileUpload::make('venue_images')
                            ->label('Main Image')
                            ->image()
                            ->directory('venues')
                            ->visibility('public')
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '16:9',
                                '4:3',
                                '1:1',
                            ]),
                        FileUpload::make('venue_gallery')
                            ->label('Gallery Images')
                            ->image()
                            ->directory('venues/gallery')
                            ->visibility('public')
                            ->multiple()
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '16:9',
                                '4:3',
                                '1:1',
                            ]),
                    ]),

                Section::make('Settings')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Toggle::make('is_active')
                                    ->label('Active')
                                    ->default(true),
                                Toggle::make('featured')
                                    ->label('Featured Venue')
                                    ->default(false),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('venue_images')
                    ->label('Image')
                    ->circular()
                    ->size(50),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('city')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Moderator')
                    ->searchable()
                    ->sortable()
                    ->visible(fn () => auth()->user()->isAdmin()),
                TextColumn::make('capacity')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('price_range')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        '$' => 'success',
                        '$$' => 'warning',
                        '$$$' => 'danger',
                        '$$$$' => 'gray',
                    }),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                IconColumn::make('featured')
                    ->label('Featured')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('city')
                    ->options(fn () => Venue::distinct()->pluck('city', 'city')->toArray())
                    ->searchable(),
                SelectFilter::make('price_range')
                    ->options([
                        '$' => '$ (Budget)',
                        '$$' => '$$ (Moderate)',
                        '$$$' => '$$$ (Expensive)',
                        '$$$$' => '$$$$ (Very Expensive)',
                    ]),
                TernaryFilter::make('is_active')
                    ->label('Active'),
                TernaryFilter::make('featured')
                    ->label('Featured'),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListVenues::route('/'),
            'create' => Pages\CreateVenue::route('/create'),
            'view' => Pages\ViewVenue::route('/{record}'),
            'edit' => Pages\EditVenue::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // If user is venue moderator, only show their venue
        if (auth()->user()->isVenueModerator()) {
            $query->where('user_id', auth()->id());
        }

        return $query;
    }
}

