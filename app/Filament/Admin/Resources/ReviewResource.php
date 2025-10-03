<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ReviewResource\Pages;
use App\Filament\Admin\Resources\ReviewResource\RelationManagers;
use App\Models\Review;
use App\Models\Venue;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Support\Facades\Auth;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static ?string $navigationIcon = null;

    protected static ?string $navigationLabel = 'Reviews';

    protected static ?string $modelLabel = 'Review';

    protected static ?string $pluralModelLabel = 'Reviews';

    protected static ?string $navigationGroup = 'Venue Management';

    protected static ?int $navigationSort = 4;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // REVIEW ESSENTIALS (Most Important for Admin)
                Section::make('Review Essentials')
                    ->description('Core review information and moderator control')
                    ->schema([
                        Select::make('venue_id')
                            ->label('Venue Reviewed')
                            ->relationship('venue', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->prefixIcon('heroicon-o-building-office')
                            ->columnSpanFull()
                            ->helperText('Which venue is this review for?'),

                        Textarea::make('review_text')
                            ->label('Review Content')
                            ->placeholder('What was the customer experience like?...')
                            ->rows(4)
                            ->required()
                            ->columnSpanFull()
                            ->helperText('The actual review text from the customer'),

                        Grid::make(2)
                            ->schema([
                                Select::make('rating')
                                    ->label('Customer Rating')
                                    ->options([
                                        5 => '5 Stars (Excellent)',
                                        4 => '4 Stars (Very Good)',
                                        3 => '3 Stars (Good)',
                                        2 => '2 Stars (Poor)',
                                        1 => '1 Star (Terrible)',
                                    ])
                                    ->required()
                                    ->default(5)
                                    ->prefixIcon('heroicon-o-star'),

                                TextInput::make('reviewer_name')
                                    ->label('Reviewer Name')
                                    ->maxLength(255)
                                    ->prefixIcon('heroicon-o-user')
                                    ->placeholder('Anonymous if left blank'),
                            ]),

                        Grid::make(1)
                            ->schema([
                                Toggle::make('is_approved')
                                    ->label('Approved for Public')
                                    ->helperText('Approve to make visible to customers')
                                    ->default(true),
                            ]),

                        TextInput::make('reviewer_email')
                            ->label('Reviewer Email')
                            ->email()
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-envelope')
                            ->placeholder('Email address if available')
                            ->helperText('Contact for follow-up if needed'),
                    ])
                    ->icon('heroicon-o-star')
                    ->collapsed(false),

                // REVIEW IMAGES (Visual Evidence)
                Section::make('Review Images')
                    ->description('Photos submitted with this review')
                    ->schema([
                        FileUpload::make('images')
                            ->label('Review Images')
                            ->image()
                            ->multiple()
                            ->directory('reviews')
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '16:9',  // Wide format for venue photos
                                '4:3',   // Standard review photos
                                '1:1',   // Square social media ready
                            ])
                            ->columnSpanFull()
                            ->helperText('Images customers uploaded with their review'),
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
                TextColumn::make('venue.name')
                    ->label('Venue')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('rating')
                    ->label('Rating')
                    ->formatStateUsing(function ($state) {
                        return ($state . '/5');
                    })
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        5 => 'success',
                        4 => 'warning',
                        3 => 'warning',
                        2 => 'danger',
                        1 => 'danger',
                        default => 'gray',
                    })
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('review_text')
                    ->label('Review')
                    ->limit(50)
                    ->searchable(),

                TextColumn::make('reviewer_name')
                    ->label('Reviewer')
                    ->searchable()
                    ->sortable(),

                IconColumn::make('is_approved')
                    ->label('Approved')
                    ->boolean()
                    ->sortable(),


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

                SelectFilter::make('rating')
                    ->options([
                        5 => '5 Stars',
                        4 => '4 Stars',
                        3 => '3 Stars',
                        2 => '2 Stars',
                        1 => '1 Star',
                    ]),

                TernaryFilter::make('is_approved')
                    ->label('Approved'),

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
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            // We'll create relation managers for venues to show their reviews
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReviews::route('/'),
            'create' => Pages\CreateReview::route('/create'),
            'edit' => Pages\EditReview::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }

    public static function canCreate(): bool
    {
        return Auth::user()->hasRole('admin');
    }
}
