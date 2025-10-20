<?php

namespace App\Livewire;

use App\Models\Group;
use Livewire\Component;

class CreateGroup extends Component
{
    public $name = '';
    public $description = '';
    public $color = '#8B5CF6'; // Default purple color
    public $privacyLevel = 'events_only'; // Default: only events

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string|max:1000',
        'color' => 'nullable|string|max:7',
        'privacyLevel' => 'required|in:events_only,aggregated_stats,full_support',
    ];

    protected $messages = [
        'name.required' => 'El nombre del grupo es obligatorio',
        'name.max' => 'El nombre no puede tener más de 255 caracteres',
        'description.max' => 'La descripción no puede tener más de 1000 caracteres',
        'privacyLevel.required' => 'Debes seleccionar un nivel de privacidad',
    ];

    public function createGroup()
    {
        // Validate
        $this->validate();

        // Create group with slug and privacy level
        $group = Group::create([
            'name' => $this->name,
            'slug' => \Illuminate\Support\Str::slug($this->name),
            'description' => $this->description,
            'color' => $this->color,
            'privacy_level' => $this->privacyLevel,
            'created_by' => auth()->id(),
            'is_active' => true,
        ]);

        // Add creator as admin member
        $group->users()->attach(auth()->id(), [
            'role' => 'admin',
            'joined_at' => now(),
        ]);

        // Flash success message
        session()->flash('success', 'Grupo creado exitosamente');

        // Redirect to group dashboard
        return redirect()->route('groups.dashboard', ['groupId' => $group->id]);
    }

    public function render()
    {
        return view('livewire.create-group')->layout('layouts.app-mobile');
    }
}
