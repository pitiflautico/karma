<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MoodEntryResource\Pages;
use App\Models\MoodEntry;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MoodEntryResource extends Resource
{
    protected static ?string $model = MoodEntry::class;

    protected static ?string $navigationIcon = 'heroicon-o-heart';

    protected static ?string $navigationGroup = 'Mood Tracking';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Mood Information')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('calendar_event_id')
                            ->relationship('calendarEvent', 'title')
                            ->searchable()
                            ->preload()
                            ->label('Related Event'),
                        Forms\Components\Select::make('group_id')
                            ->relationship('group', 'name')
                            ->searchable()
                            ->preload()
                            ->label('Group'),
                        Forms\Components\Slider::make('mood_score')
                            ->label('Mood Score')
                            ->required()
                            ->minValue(1)
                            ->maxValue(10)
                            ->step(1)
                            ->marks([
                                1 => 'Very Low',
                                5 => 'Neutral',
                                10 => 'Excellent',
                            ]),
                        Forms\Components\Textarea::make('note')
                            ->label('Mood Description')
                            ->helperText('Describe what influenced this mood. Useful for generating professional reports.')
                            ->placeholder('Example: Had a productive meeting, felt accomplished, or feeling stressed about deadlines...')
                            ->maxLength(1000)
                            ->rows(4)
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('is_manual')
                            ->label('Manual Entry')
                            ->helperText('Not triggered by a calendar event')
                            ->default(false),
                    ])->columns(2),

                Forms\Components\Section::make('Facial Analysis')
                    ->schema([
                        Forms\Components\Select::make('face_expression')
                            ->label('Expression')
                            ->options([
                                'happy' => 'Happy',
                                'slight_smile' => 'Slight Smile',
                                'neutral' => 'Neutral',
                                'sad' => 'Sad',
                                'tired' => 'Tired',
                            ])
                            ->disabled()
                            ->dehydrated(false),
                        Forms\Components\TextInput::make('face_expression_confidence')
                            ->label('Expression Confidence')
                            ->suffix('%')
                            ->disabled()
                            ->dehydrated(false)
                            ->formatStateUsing(fn ($state) => $state ? round($state * 100, 1) : null),
                        Forms\Components\Select::make('face_energy_level')
                            ->label('Energy Level')
                            ->options([
                                'high' => 'High',
                                'medium' => 'Medium',
                                'low' => 'Low',
                            ])
                            ->disabled()
                            ->dehydrated(false),
                        Forms\Components\TextInput::make('face_eyes_openness')
                            ->label('Eyes Openness')
                            ->suffix('%')
                            ->disabled()
                            ->dehydrated(false)
                            ->formatStateUsing(fn ($state) => $state ? round($state * 100, 1) : null),
                        Forms\Components\Select::make('face_social_context')
                            ->label('Social Context')
                            ->options([
                                'alone' => 'Alone',
                                'with_one' => 'With One Person',
                                'group' => 'Group',
                            ])
                            ->disabled()
                            ->dehydrated(false),
                        Forms\Components\TextInput::make('face_total_faces')
                            ->label('Total Faces Detected')
                            ->disabled()
                            ->dehydrated(false),
                        Forms\Components\TextInput::make('bpm')
                            ->label('BPM')
                            ->suffix('bpm')
                            ->disabled()
                            ->dehydrated(false),
                        Forms\Components\TextInput::make('environment_brightness')
                            ->label('Environment Brightness')
                            ->disabled()
                            ->dehydrated(false),
                    ])->columns(4)->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('mood_score')
                    ->label('Mood')
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state <= 3 => 'danger',
                        $state <= 6 => 'warning',
                        $state <= 8 => 'success',
                        default => 'info',
                    })
                    ->formatStateUsing(fn (int $state): string => $state . '/10')
                    ->sortable(),
                Tables\Columns\TextColumn::make('calendarEvent.title')
                    ->label('Event')
                    ->limit(30)
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('group.name')
                    ->label('Group')
                    ->badge()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_manual')
                    ->label('Manual')
                    ->boolean()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('face_expression')
                    ->label('Expression')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'happy', 'slight_smile' => 'success',
                        'neutral' => 'info',
                        'sad' => 'warning',
                        'tired' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => ucwords(str_replace('_', ' ', $state ?? '')))
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('face_energy_level')
                    ->label('Energy')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'high' => 'success',
                        'medium' => 'info',
                        'low' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => ucfirst($state ?? ''))
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('face_social_context')
                    ->label('Social')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'alone' => '👤 Alone',
                        'with_one' => '👥 With One',
                        'group' => '👥👥 Group',
                        default => '',
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->since(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('group')
                    ->relationship('group', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\Filter::make('mood_score')
                    ->form([
                        Forms\Components\Select::make('mood_category')
                            ->options([
                                'low' => 'Low (1-3)',
                                'medium' => 'Medium (4-6)',
                                'good' => 'Good (7-8)',
                                'excellent' => 'Excellent (9-10)',
                            ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['mood_category'],
                            fn (Builder $query, $category): Builder => match ($category) {
                                'low' => $query->whereBetween('mood_score', [1, 3]),
                                'medium' => $query->whereBetween('mood_score', [4, 6]),
                                'good' => $query->whereBetween('mood_score', [7, 8]),
                                'excellent' => $query->whereBetween('mood_score', [9, 10]),
                            }
                        );
                    }),
                Tables\Filters\TernaryFilter::make('is_manual')
                    ->label('Manual Entries'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListMoodEntries::route('/'),
            'create' => Pages\CreateMoodEntry::route('/create'),
            'view' => Pages\ViewMoodEntry::route('/{record}'),
            'edit' => Pages\EditMoodEntry::route('/{record}/edit'),
        ];
    }
}
