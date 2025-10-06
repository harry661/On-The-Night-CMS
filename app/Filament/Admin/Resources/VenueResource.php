<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\VenueResource\Pages;
use App\Filament\Admin\Resources\VenueResource\RelationManagers;
use App\Models\Venue;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\CheckboxList;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Support\Facades\Auth;

class VenueResource extends Resource
{
    protected static ?string $model = Venue::class;

    protected static ?string $navigationIcon = null;

    protected static ?string $navigationLabel = 'Venues';

    protected static ?string $modelLabel = 'Venue';

    protected static ?string $pluralModelLabel = 'Venues';

    protected static ?string $navigationGroup = 'Venue Management';

    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // ESSENTIAL VENUE INFO (Most Important for Moderators)
                Section::make('Venue Essentials')
                    ->description('Core information that customers see first')
                    ->schema([
                        TextInput::make('name')
                            ->label('Venue Name')
                            ->required()
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-building-office')
                            ->columnSpanFull(),
                        
                        Textarea::make('description')
                            ->label('Venue Description')
                            ->placeholder('Tell customers what makes your venue special...')
                            ->rows(4)
                            ->columnSpanFull()
                            ->helperText('Describe your venue\'s atmosphere, music, crowd, and unique offerings'),
                        
                        Grid::make(2)
                            ->schema([
                                Select::make('venue_type_id')
                                    ->label('Venue Type')
                                    ->relationship('venueType', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->prefixIcon('heroicon-o-tag')
                                    ->helperText('Choose the primary category for this venue'),
                                
                                Select::make('status')
                                    ->label('Marketing Status')
                                    ->options([
                                        'none' => 'No Special Status',
                                        'featured' => 'Featured Venue',
                                        'sponsored' => 'Sponsored Promotion',
                                        'new' => 'New Venue',
                                    ])
                                    ->default('none')
                                    ->prefixIcon('heroicon-o-star'),
                            ]),
                        
                        Toggle::make('is_active')
                                ->label('Venue is Active')
                                ->helperText('Toggle off to hide from public')
                                ->default(true)
                                ->columnSpanFull(),
                    ])
                    ->icon('heroicon-o-star')
                    ->collapsed(false),
                
                // LOCATION & CONTACTS (High Priority)
                Section::make('Location & Contact')
                    ->description('Where customers can find and reach you')
                    ->schema([
                        TextInput::make('address')
                            ->label('Street Address')
                            ->required()
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-map-pin')
                            ->columnSpanFull()
                            ->helperText('Full street address for map integration'),
                        
                        Select::make('location_id')
                            ->label('Location')
                            ->relationship('location', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->helperText('City name (e.g., Liverpool, Manchester)'),

                                TextInput::make('city')
                                    ->required(),

                                TextInput::make('state')
                                    ->required(),

                                TextInput::make('country')
                                    ->required()
                                    ->default('UK'),
                            ])
                            ->prefixIcon('heroicon-o-map-pin')
                            ->columnSpanFull()
                            ->helperText('Select city location or create new'),
                        
                        Grid::make(2)
                            ->schema([
                                TextInput::make('phone')
                                    ->label('Phone Number')
                                    ->tel()
                                    ->maxLength(20)
                                    ->prefix('+44')
                                    ->prefixIcon('heroicon-o-phone')
                                    ->helperText('Main contact number'),
                                
                                TextInput::make('email')
                                    ->label('Email Address')
                                    ->email()
                                    ->maxLength(255)
                                    ->prefixIcon('heroicon-o-envelope')
                                    ->helperText('Business email for inquiries'),
                            ]),
                        
                        TextInput::make('website')
                            ->label('Website')
                            ->url()
                            ->maxLength(255)
                            ->prefix('https://')
                            ->prefixIcon('heroicon-o-globe-alt')
                            ->helperText('Your venue\'s website or social media'),
                    ])
                    ->icon('heroicon-o-map-pin')
                    ->collapsed(false),
                
                // OPERATING HOURS (High Priority for Customers)
                Section::make('Operating Hours')
                    ->description('When your venue is open - customers plan around this!')
                    ->schema([
                        KeyValue::make('opening_hours')
                            ->keyLabel('Day of Week')
                            ->valueLabel('Opening Hours')
                            ->addActionLabel('Add Day')
                            ->addable(fn ($get) => count($get('opening_hours') ?? []) < 7)
                            ->deletable(true)
                            ->columnSpanFull()
                            ->helperText('Set your opening hours for each day. Use formats like "9:00 PM - 2:00 AM" or "Closed"'),
                    ])
                    ->icon('heroicon-o-clock')
                    ->collapsed(false),
                
                // MUSIC & DRINKS
                Section::make('Music & Drinks')
                    ->description('Select music genres and drink types available at this venue')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                // Music Section - Left Column
                                CheckboxList::make('musicGenres')
                                    ->label('Music Genres')
                                    ->relationship('musicGenres', 'name')
                                    ->searchable()
                                    ->columns(1)
                                    ->helperText('Select music genres played'),
                                
                                // Drinks Section - Right Column  
                                CheckboxList::make('drinkTypes')
                                    ->label('Drink Types')
                                    ->relationship('drinkTypes', 'name')
                                    ->searchable()
                                    ->columns(1)
                                    ->helperText('Select drinks available'),
                            ]),
                    ])
                    ->icon('heroicon-o-musical-note')
                    ->collapsible()
                    ->collapsed(false),
                
                // VENUE IMAGES (Marketing Essential)
                Section::make('Venue Images')
                    ->description('High-quality photo that showcases your venue\'s vibe')
                    ->schema([
                        FileUpload::make('venue_images')
                            ->label('Venue Image')
                            ->image()
                            ->directory('venues')
                            ->visibility('public')
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '16:9',  // Wide shots of venue space
                                '4:3',   // Standard venue views  
                                '1:1',   // Square social media ready
                            ])
                            ->columnSpanFull()
                            ->helperText('Upload the main photo that represents your venue'),
                    ])
                    ->icon('heroicon-o-camera')
                    ->collapsed(true),
                
                // AMENITIES & FEATURES (Important Differentiators)
                Section::make('Amenities & Features')
                    ->description('What makes your venue stand out from the competition')
                    ->schema([
                        KeyValue::make('amenities')
                            ->keyLabel('Feature/Service')
                            ->valueLabel('Description')
                            ->addActionLabel('Add Feature')
                            ->columnSpanFull()
                            ->helperText('List what your venue offers: VIP areas, drink specials, music genres, parking, dress codes, etc.'),
                    ])
                    ->icon('heroicon-o-sparkles')
                    ->collapsed(true),
                
                // ADMIN SETTINGS (Lowest Priority)
                Section::make('Admin Settings')
                    ->description('Management settings (usually only changed by admin)')
                    ->schema([
                        Select::make('user_id')
                            ->label('Venue Manager')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->prefixIcon('heroicon-o-user')
                            ->helperText('Assign who manages this venue'),
                    ])
                    ->icon('heroicon-o-cog-6-tooth')
                    ->collapsed(true),
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
                
                TextColumn::make('venueType.name')
                    ->label('Type')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Bar' => 'purple',
                        'Food' => 'orange', 
                        'Stay' => 'green',
                        'Leisure' => 'red',
                        default => 'gray',
                    }),
                
                TextColumn::make('location.name')
                    ->label('Location')
                    ->searchable()
                    ->sortable(),

                    TextColumn::make('location.city')
                        ->label('City')
                        ->searchable()
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),
                
                IconColumn::make('is_active')
                    ->boolean(),
                
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'featured' => 'danger',
                        'sponsored' => 'warning',
                        'new' => 'success',
                        'none' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'featured' => 'Featured',
                        'sponsored' => 'Sponsored',
                        'new' => 'New',
                        'none' => 'None',
                        default => 'None',
                    })
                    ->sortable(),
                
                TextColumn::make('user.name')
                    ->label('Owner')
                    ->searchable()
                    ->sortable()
                    ->url(fn ($record) => $record->user ? route('filament.admin.resources.users.edit', ['record' => $record->user]) : null)
                    ->color('primary')
                    ->tooltip('Click to edit user'),
                
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
                ->filters([
                    SelectFilter::make('location')
                        ->relationship('location', 'name')
                        ->searchable()
                        ->preload(),

                    SelectFilter::make('city')
                        ->options(function () {
                            return Venue::with('location')->get()
                                ->pluck('location.city', 'location.city')
                                ->filter()
                                ->toArray();
                        })
                        ->searchable(),
                
                TernaryFilter::make('is_active')
                    ->label('Active'),
                
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'none' => 'None',
                        'featured' => 'Featured',
                        'sponsored' => 'Sponsored',
                        'new' => 'New',
                    ]),
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
                        ->label('Delete Selected'),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ReviewsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVenues::route('/'),
            'create' => Pages\CreateVenue::route('/create'),
            'edit' => Pages\EditVenue::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // If user is venue moderator, only show their venue
        if (Auth::user()->hasRole('venue_moderator')) {
            $query->where('user_id', Auth::id());
        }

        return $query;
    }

    public static function canCreate(): bool
    {
        return Auth::user()->hasRole('admin') || Auth::user()->hasRole('venue_moderator');
    }

    public static function canEdit($record): bool
    {
        if (Auth::user()->hasRole('admin')) {
            return true;
        }

        if (Auth::user()->hasRole('venue_moderator')) {
            return $record->user_id === Auth::id();
        }

        return false;
    }

    public static function canDelete($record): bool
    {
        if (Auth::user()->hasRole('admin')) {
            return true;
        }

        if (Auth::user()->hasRole('venue_moderator')) {
            return $record->user_id === Auth::id();
        }

        return false;
    }

    public static function canDeleteAny(): bool
    {
        return Auth::user()->hasRole('admin');
    }
}
