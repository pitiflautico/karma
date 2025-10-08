<?php

namespace App\Filament\Resources\MoodEntryResource\Pages;

use App\Filament\Resources\MoodEntryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMoodEntries extends ListRecords
{
    protected static string $resource = MoodEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
