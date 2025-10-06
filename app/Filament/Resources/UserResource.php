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
use Illuminate\Support\Facades\Auth;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Administration';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('User Information')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Profile')
                            ->icon('heroicon-m-user')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true),
                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                                    ->live(onBlur: true),
                            ])->columns(2),

                        Forms\Components\Tabs\Tab::make('Roles')
                            ->icon('heroicon-m-shield-check')
                            ->schema([
                                Forms\Components\Select::make('roles')
                                    ->relationship('roles', 'name', function ($query) {
                                        return $query->where('guard_name', 'web');
                                    })
                                    ->multiple()
                                    ->searchable()
                                    ->preload()
                                    ->placeholder('Select roles')
                                    ->columnSpanFull(),
                            ]),

                        Forms\Components\Tabs\Tab::make('Security')
                            ->icon('heroicon-m-key')
                            ->schema([
                                Forms\Components\TextInput::make('password')
                                    ->password()
                                    ->required(fn(string $context): bool => $context === 'create')
                                    ->minLength(8)
                                    ->confirmed()
                                    ->live(onBlur: true)
                                    ->dehydrateStateUsing(fn($state) => filled($state) ? bcrypt($state) : null)
                                    ->dehydrated(fn($state) => filled($state)),
                                Forms\Components\TextInput::make('password_confirmation')
                                    ->password()
                                    ->required(fn(string $context): bool => $context === 'create')
                                    ->minLength(8)
                                    ->live(onBlur: true)
                                    ->dehydrated(false),
                            ])->columns(2),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('User')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->color('primary')
                    ->formatStateUsing(function ($record) {
                        return $record->name;
                    })
                    ->description(function ($record) {
                        return $record->initials();
                    }),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-m-at-symbol'),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Roles')
                    ->badge()
                    ->color('success')
                    ->separator(', '),
                Tables\Columns\TextColumn::make('organization.name')
                    ->label('Organization')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray')
                    ->placeholder('No Organization'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Registered')
                    ->dateTime()
                    ->sortable()
                    ->since(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('roles')
                    ->relationship('roles', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make()->slideOver(),
                    Tables\Actions\DeleteAction::make(),
                ])
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->size('sm')
                    ->color('gray'),
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

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Automatically assign the current user's organization
        $currentUser = Auth::user();
        $data['organization_id'] = $currentUser ? $currentUser->organization_id : null;

        // Set email as verified so user can login immediately
        $data['email_verified_at'] = now();

        return $data;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'view' => Pages\ViewUser::route('/{record}'),
        ];
    }
}
