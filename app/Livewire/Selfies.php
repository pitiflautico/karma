<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\MoodEntry;
use Illuminate\Support\Facades\Storage;

class Selfies extends Component
{
    public function mount()
    {
        // Can add initialization here if needed
    }

    public function deleteSelfie($id)
    {
        $moodEntry = MoodEntry::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$moodEntry) {
            session()->flash('error', 'Selfie not found.');
            return;
        }

        // Delete photo from storage
        if ($moodEntry->selfie_photo_path) {
            Storage::disk('public')->delete($moodEntry->selfie_photo_path);
        }

        // Clear photo fields
        $moodEntry->update([
            'selfie_photo_path' => null,
            'selfie_heatmap_path' => null,
            'selfie_taken_at' => null,
        ]);

        session()->flash('success', 'Selfie deleted successfully.');
    }

    public function render()
    {
        $selfies = MoodEntry::where('user_id', auth()->id())
            ->whereNotNull('selfie_photo_path')
            ->orderBy('selfie_taken_at', 'desc')
            ->paginate(24);

        // Group by date for display
        $selfiesByDate = $selfies->groupBy(function($item) {
            return $item->selfie_taken_at->format('Y-m-d');
        });

        return view('livewire.selfies', [
            'selfies' => $selfies,
            'selfiesByDate' => $selfiesByDate,
        ])->layout('layouts.app');
    }
}
