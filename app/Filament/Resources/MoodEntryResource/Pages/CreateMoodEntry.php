<?php

namespace App\Filament\Resources\MoodEntryResource\Pages;

use App\Filament\Resources\MoodEntryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMoodEntry extends CreateRecord
{
    protected static string $resource = MoodEntryResource::class;
}
