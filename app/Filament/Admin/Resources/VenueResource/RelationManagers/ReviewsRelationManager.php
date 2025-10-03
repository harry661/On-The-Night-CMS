<?php

namespace App\Filament\Admin\Resources\VenueResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TernaryFilter;

class ReviewsRelationManager extends RelationManager
{
    protected static string $relationship = 'reviews';

    protected static ?string $title = 'Reviews';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('rating')
                    ->label('Rating')
                    ->options([
                        1 => '1 Star',
                        2 => '2 Stars',
                        3 => '3 Stars',
                        4 => '4 Stars',
                        5 => '5 Stars',
                    ])
                    ->required()
                    ->default(5),

                TextInput::make('reviewer_name')
                    ->label('Reviewer Name')
                    ->maxLength(255),

                TextInput::make('reviewer_email')
                    ->label('Reviewer Email')
                    ->email()
                    ->maxLength(255),

                Textarea::make('review_text')
                    ->label('Review Text')
                    ->rows(4)
                    ->required()
                    ->columnSpanFull(),

                Toggle::make('is_approved')
                    ->label('Approved')
                    ->helperText('Approve this review to make it public'),

                FileUpload::make('images')
                    ->label('Review Images')
                    ->image()
                    ->multiple()
                    ->directory('reviews')
                    ->imageEditor()
                    ->imageEditorAspectRatios([
                        '16:9',
                        '4:3',
                        '1:1',
                    ])
                    ->columnSpanFull()
                    ->helperText('Images uploaded with this review'),

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('rating')
            ->columns([
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
                    ->alignCenter(),

                TextColumn::make('review_text')
                    ->label('Review')
                    ->limit(60)
                    ->searchable(),

                TextColumn::make('reviewer_name')
                    ->label('Reviewer')
                    ->searchable()
                    ->placeholder('Anonymous'),

                IconColumn::make('is_approved')
                    ->label('Approved')
                    ->boolean(),


                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_approved')
                    ->label('Approved'),
                
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add Review'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Delete Reviews'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('No Reviews Yet')
            ->emptyStateDescription('Reviews from app users will appear here once they start using this venue.')
            ->emptyStateIcon('heroicon-o-star');
    }
}
