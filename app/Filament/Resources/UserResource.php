<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'User Management';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->required(fn (string $context): bool => $context === 'create')
                            ->dehydrated(fn ($state) => filled($state))
                            ->minLength(8)
                            ->maxLength(255),
                        Forms\Components\Select::make('roles')
                            ->relationship('roles', 'name')
                            ->preload()
                            ->searchable()
                            ->label('Role'),
                    ])->columns(2),

                Forms\Components\Section::make('Google Calendar Integration')
                    ->schema([
                        Forms\Components\TextInput::make('google_id')
                            ->label('Google ID')
                            ->disabled()
                            ->dehydrated(false),
                        Forms\Components\Toggle::make('calendar_sync_enabled')
                            ->label('Calendar Sync Enabled')
                            ->default(false),
                        Forms\Components\TimePicker::make('quiet_hours_start')
                            ->label('Quiet Hours Start')
                            ->seconds(false),
                        Forms\Components\TimePicker::make('quiet_hours_end')
                            ->label('Quiet Hours End')
                            ->seconds(false),
                    ])->columns(2)->collapsed(),

                Forms\Components\Section::make('Emotional Selfie Settings')
                    ->schema([
                        Forms\Components\Select::make('selfie_mode')
                            ->label('Selfie Prompt Mode')
                            ->options([
                                'random' => 'Random',
                                'scheduled' => 'Scheduled',
                            ])
                            ->default('random')
                            ->required()
                            ->reactive(),
                        Forms\Components\TimePicker::make('selfie_scheduled_time')
                            ->label('Scheduled Time')
                            ->seconds(false)
                            ->visible(fn ($get) => $get('selfie_mode') === 'scheduled'),
                    ])->columns(2)->collapsed(),

                Forms\Components\Section::make('Preferences')
                    ->schema([
                        Forms\Components\Toggle::make('adaptive_ui_enabled')
                            ->label('Adaptive UI Colors')
                            ->default(true)
                            ->helperText('UI colors adapt to current emotional state'),
                    ])->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Role')
                    ->badge()
                    ->colors([
                        'success' => 'admin',
                        'gray' => 'user',
                    ]),
                Tables\Columns\IconColumn::make('email_verified_at')
                    ->label('Verified')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\IconColumn::make('calendar_sync_enabled')
                    ->label('Calendar')
                    ->boolean()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('moodEntries_count')
                    ->counts('moodEntries')
                    ->label('Mood Entries')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('groups_count')
                    ->counts('groups')
                    ->label('Groups')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('last_login_at')
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->relationship('roles', 'name')
                    ->label('Role'),
                Tables\Filters\TernaryFilter::make('email_verified_at')
                    ->label('Verified')
                    ->nullable(),
                Tables\Filters\TernaryFilter::make('calendar_sync_enabled')
                    ->label('Calendar Sync'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
