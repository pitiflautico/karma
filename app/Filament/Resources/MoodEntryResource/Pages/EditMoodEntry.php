<?php

namespace App\Filament\Resources\MoodEntryResource\Pages;

use App\Filament\Resources\MoodEntryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMoodEntry extends EditRecord
{
    protected static string $resource = MoodEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
