<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\EventResource\Pages;
use App\Filament\Admin\Resources\EventResource\RelationManagers;
use App\Models\Event;
use App\Models\Venue;
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
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Support\Facades\Auth;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static ?string $navigationIcon = null;

    protected static ?string $navigationLabel = 'Events';

    protected static ?string $modelLabel = 'Event';

    protected static ?string $pluralModelLabel = 'Events';

    protected static ?string $navigationGroup = 'Venue Management';

    protected static ?int $navigationSort = 3;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // EVENT ESSENTIALS (Most Important for Moderators)
                Section::make('Event Essentials')
                    ->description('Core event information customers see first')
                    ->schema([
                        TextInput::make('title')
                            ->label('Event Title')
                            ->required()
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-calendar')
                            ->columnSpanFull()
                            ->helperText('Eye-catching event title that draws crowds'),

                        Select::make('event_type')
                            ->label('Event Type')
                            ->options([
                                'Live Music' => 'Live Music',
                                'DJ Set' => 'DJ Set',
                                'Comedy Night' => 'Comedy Night',
                                'Theme Night' => 'Theme Night',
                                'Special Evening' => 'Special Evening',
                                'Weekend Dance Party' => 'Weekend Dance Party',
                                'Birthday Party' => 'Birthday Party',
                                'Wedding' => 'Wedding',
                                'Other' => 'Other',
                            ])
                            ->required()
                            ->searchable()
                            ->prefixIcon('heroicon-o-tag')
                            ->helperText('What kind of entertainment is this?'),

                        Textarea::make('description')
                            ->label('Event Description')
                            ->placeholder('Describe what makes this event special...')
                            ->rows(3)
                            ->columnSpanFull()
                            ->helperText('Tell customers what to expect - entertainment, atmosphere, crowd'),

                        Select::make('venue_id')
                            ->label('Venue')
                            ->relationship('venue', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->prefixIcon('heroicon-o-building-office'),

                        Toggle::make('is_active')
                            ->label('Event is Active')
                            ->helperText('Toggle off to hide from public')
                            ->default(true)
                            ->columnSpanFull(),
                    ])
                    ->icon('heroicon-o-calendar')
                    ->collapsed(false),

                // EVENT SCHEDULE (High Priority for Customers)
                Section::make('Event Schedule')
                    ->description('When customers can attend this event')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                DateTimePicker::make('start_date')
                                    ->label('Event Starts')
                                    ->required()
                                    ->default(now()->addDays(7))
                                    ->prefixIcon('heroicon-o-calendar')
                                    ->helperText('When does the event begin?'),

                                DateTimePicker::make('end_date')
                                    ->label('Event Ends')
                                    ->required()
                                    ->default(now()->addDays(7)->addHours(4))
                                    ->prefixIcon('heroicon-o-clock')
                                    ->helperText('When does the event finish?'),
                            ]),
                    ])
                    ->icon('heroicon-o-clock')
                    ->collapsed(false),

                // PRICING & AVAILABILITY
                Section::make('Pricing & Availability')
                    ->description('Ticket costs and availability')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('ticket_price')
                                    ->label('Ticket Price')
                                    ->numeric()
                                    ->prefix('£')
                                    ->step(0.01)
                                    ->placeholder('0.00 for free events')
                                    ->prefixIcon('heroicon-o-banknotes')
                                    ->helperText('Cost per ticket (leave as 0 for free events)'),

                                Toggle::make('sold_out')
                                    ->label('Sold Out')
                                    ->helperText('Are all tickets sold?')
                                    ->default(false),
                            ]),
                    ])
                    ->icon('heroicon-o-banknotes')
                    ->collapsed(false),

                // EVENT IMAGE (Marketing Essential)
                Section::make('Event Image')
                    ->description('Promotional visuals for this event')
                    ->schema([
                        FileUpload::make('images')
                            ->label('Event Image')
                            ->image()
                            ->directory('events')
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '16:9',  // Wide format for event banners
                                '4:3',   // Standard event promotion
                                '1:1',   // Square social media ready
                            ])
                            ->columnSpanFull()
                            ->helperText('Upload promotional images that capture the event vibe'),
                    ])
                    ->icon('heroicon-o-camera')
                    ->collapsed(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->searchOnBlur()
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('event_type')
                    ->badge()
                    ->color('info')
                    ->searchable(),

                TextColumn::make('venue.name')
                    ->label('Venue')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('start_date')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('end_date')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('ticket_price')
                    ->label('Price')
                    ->money('GBP')
                    ->sortable(),

                IconColumn::make('is_active')
                    ->boolean(),

                IconColumn::make('sold_out')
                    ->boolean(),

                TextColumn::make('user.name')
                    ->label('Created By')
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
                SelectFilter::make('venue')
                    ->relationship('venue', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('event_type')
                    ->options([
                        'Live Music' => 'Live Music',
                        'DJ Night' => 'DJ Night',
                        'Comedy Show' => 'Comedy Show',
                        'Private Party' => 'Private Party',
                        'Corporate Event' => 'Corporate Event',
                        'Wedding' => 'Wedding',
                        'Birthday Party' => 'Birthday Party',
                        'Other' => 'Other',
                    ]),

                TernaryFilter::make('is_active')
                    ->label('Active'),

                TernaryFilter::make('sold_out')
                    ->label('Sold Out'),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // If user is venue moderator, only show events for their venue
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
