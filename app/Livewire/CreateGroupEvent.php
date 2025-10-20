<?php

namespace App\Livewire;

use App\Models\Group;
use App\Models\GroupEvent;
use Carbon\Carbon;
use Livewire\Component;

class CreateGroupEvent extends Component
{
    public $groupId;
    public $group;
    public $title = '';
    public $description = '';
    public $eventDate = '';
    public $eventTime = '';

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string|max:1000',
        'eventDate' => 'required|date|after_or_equal:today',
        'eventTime' => 'required',
    ];

    protected $messages = [
        'title.required' => 'El título es obligatorio',
        'eventDate.required' => 'La fecha es obligatoria',
        'eventDate.after_or_equal' => 'La fecha debe ser hoy o posterior',
        'eventTime.required' => 'La hora es obligatoria',
    ];

    public function mount($groupId)
    {
        $this->groupId = $groupId;

        // Load group and verify user is a member
        $this->group = Group::findOrFail($groupId);

        // Verify user is a member of this group
        if (!$this->group->users->contains(auth()->id())) {
            abort(403, 'No eres miembro de este grupo');
        }

        // Set default date and time
        $this->eventDate = today()->format('Y-m-d');
        $this->eventTime = now()->format('H:i');
    }

    public function createEvent()
    {
        // Validate
        $this->validate();

        // Combine date and time
        $eventDateTime = Carbon::parse($this->eventDate . ' ' . $this->eventTime);

        // Create event
        GroupEvent::create([
            'group_id' => $this->groupId,
            'calendar_event_id' => null,
            'title' => $this->title,
            'description' => $this->description,
            'event_date' => $eventDateTime,
            'created_by' => auth()->id(),
            'is_custom' => true,
        ]);

        // Flash success message
        session()->flash('success', 'Evento creado exitosamente');

        // Redirect to events list
        return redirect()->route('groups.events', ['groupId' => $this->groupId]);
    }

    public function render()
    {
        return view('livewire.create-group-event')->layout('layouts.app-mobile');
    }
}
