<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\DealResource\Pages;
use App\Filament\Admin\Resources\DealResource\RelationManagers;
use App\Models\Deal;
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
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Support\Facades\Auth;

class DealResource extends Resource
{
    protected static ?string $model = Deal::class;

    protected static ?string $navigationIcon = null;

    protected static ?string $navigationLabel = 'Deals';

    protected static ?string $modelLabel = 'Deal';

    protected static ?string $pluralModelLabel = 'Deals';

    protected static ?string $navigationGroup = 'Venue Management';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // DEAL ESSENTIALS (Most Important for Moderators)
                Section::make('Deal Essentials')
                    ->description('Core deal information customers see first')
                    ->schema([
                        TextInput::make('title')
                            ->label('Deal Title')
                            ->required()
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-tag')
                            ->columnSpanFull()
                            ->helperText('Eye-catching title that grabs attention'),

                        TextInput::make('deal_type')
                            ->label('Deal Type')
                            ->placeholder('e.g., 2-4-1, 50% off, Free entry')
                            ->required()
                            ->maxLength(100)
                            ->prefixIcon('heroicon-o-tag')
                            ->helperText('What kind of deal is this?'),

                        Textarea::make('description')
                            ->label('Deal Description')
                            ->placeholder('Describe what customers get with this deal...')
                            ->rows(3)
                            ->columnSpanFull()
                            ->helperText('Explain terms, conditions, and what customers receive'),

                        Select::make('venue_id')
                            ->label('Venue')
                            ->relationship('venue', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->prefixIcon('heroicon-o-building-office'),

                        Toggle::make('is_active')
                            ->label('Deal is Active')
                            ->helperText('Toggle off to hide from public')
                            ->default(true)
                            ->columnSpanFull(),
                    ])
                    ->icon('heroicon-o-tag')
                    ->collapsed(false),

                // VALIDITY PERIOD (High Priority for Customers)
                Section::make('Validity Period')
                    ->description('When customers can claim this deal')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                DateTimePicker::make('start_date')
                                    ->label('Deal Starts')
                                    ->required()
                                    ->default(now())
                                    ->prefixIcon('heroicon-o-calendar')
                                    ->helperText('When can customers start using this deal?'),

                                DateTimePicker::make('end_date')
                                    ->label('Deal Expires')
                                    ->required()
                                    ->default(now()->addDays(7))
                                    ->prefixIcon('heroicon-o-calendar-days')
                                    ->helperText('Last date/time customers can claim this deal'),
                            ]),
                    ])
                    ->icon('heroicon-o-clock')
                    ->collapsed(false),

                // DEAL IMAGE (Marketing Essential)
                Section::make('Deal Image')
                    ->description('Visual that showcases your offer')
                    ->schema([
                        FileUpload::make('images')
                            ->label('Deal Image')
                            ->image()
                            ->directory('deals')
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '16:9',  // Wide format for banners
                                '4:3',   // Standard promotion format
                                '1:1',   // Square social media ready
                            ])
                            ->columnSpanFull()
                            ->helperText('Upload an eye-catching image that represents your deal'),
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

                TextColumn::make('deal_type')
                    ->badge()
                    ->color('success')
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

                IconColumn::make('is_active')
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

                SelectFilter::make('deal_type')
                    ->options(function () {
                        return Deal::distinct()->pluck('deal_type', 'deal_type')->filter()->toArray();
                    }),

                TernaryFilter::make('is_active')
                    ->label('Active'),
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
            'index' => Pages\ListDeals::route('/'),
            'create' => Pages\CreateDeal::route('/create'),
            'edit' => Pages\EditDeal::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // If user is venue moderator, only show deals for their venue
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
