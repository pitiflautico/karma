<?php

namespace App\Filament\Resources\MoodEntryResource\Pages;

use App\Filament\Resources\MoodEntryResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMoodEntry extends ViewRecord
{
    protected static string $resource = MoodEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
