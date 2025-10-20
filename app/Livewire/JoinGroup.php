<?php

namespace App\Livewire;

use App\Models\Group;
use Livewire\Component;

class JoinGroup extends Component
{
    public $inviteCode = '';
    public $errorMessage = '';
    public $successMessage = '';

    protected $rules = [
        'inviteCode' => 'required|string|size:8',
    ];

    protected $messages = [
        'inviteCode.required' => 'Por favor ingresa un código de invitación',
        'inviteCode.size' => 'El código debe tener exactamente 8 caracteres',
    ];

    public function joinGroup()
    {
        $this->errorMessage = '';
        $this->successMessage = '';

        // Validate
        $this->validate();

        // Find group by invite code (case insensitive)
        $group = Group::where('invite_code', strtoupper($this->inviteCode))->first();

        if (!$group) {
            $this->errorMessage = 'Código de invitación inválido';
            return;
        }

        if (!$group->is_active) {
            $this->errorMessage = 'Este grupo ya no está activo';
            return;
        }

        // Check if user is already a member
        if ($group->users->contains(auth()->id())) {
            $this->errorMessage = 'Ya eres miembro de este grupo';
            return;
        }

        // Add user to group
        $group->users()->attach(auth()->id(), [
            'role' => 'member',
            'joined_at' => now(),
        ]);

        $this->successMessage = '¡Te has unido al grupo exitosamente!';

        // Redirect to group dashboard after 1.5 seconds
        $this->dispatch('group-joined', groupId: $group->id);
    }

    public function render()
    {
        return view('livewire.join-group')->layout('layouts.app-mobile');
    }
}
