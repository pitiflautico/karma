<?php

namespace App\Livewire;

use App\Models\SharedAccess;
use App\Models\SharingInvite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AcceptInvite extends Component
{
    public $token;
    public $invite;
    public $error = null;
    public $success = null;

    public function mount($token)
    {
        $this->token = $token;

        // Find the invitation
        $this->invite = SharingInvite::where('token', $token)->first();

        // Validate invitation
        if (!$this->invite) {
            $this->error = 'Invalid invitation link.';
            return;
        }

        if ($this->invite->status !== 'pending') {
            $this->error = 'This invitation has already been ' . $this->invite->status . '.';
            return;
        }

        if ($this->invite->expires_at->isPast()) {
            $this->invite->markAsExpired();
            $this->error = 'This invitation has expired.';
            return;
        }
    }

    public function acceptInvitation()
    {
        if (!Auth::check()) {
            return redirect()->route('auth.google')->with('intended', route('sharing.accept-invite', ['token' => $this->token]));
        }

        $user = Auth::user();

        // Check if user is trying to accept their own invitation
        if ($this->invite->sender_id === $user->id) {
            $this->error = 'You cannot accept your own invitation.';
            return;
        }

        // Check if email matches (optional - you might want to allow any logged in user)
        if ($this->invite->recipient_email !== $user->email) {
            $this->error = 'This invitation was sent to ' . $this->invite->recipient_email . '. Please log in with that account.';
            return;
        }

        // Check if sharing relationship already exists
        $existingAccess = SharedAccess::where('owner_id', $this->invite->sender_id)
            ->where('shared_with_user_id', $user->id)
            ->first();

        if ($existingAccess) {
            $this->invite->markAsAccepted();
            $this->error = 'You already have access to ' . $this->invite->sender->name . '\'s data.';
            return;
        }

        // Create sharing relationship
        SharedAccess::create([
            'owner_id' => $this->invite->sender_id,
            'shared_with_user_id' => $user->id,
            'can_view_moods' => $this->invite->can_view_moods,
            'can_view_notes' => $this->invite->can_view_notes,
            'can_view_selfies' => $this->invite->can_view_selfies,
        ]);

        // Mark invitation as accepted
        $this->invite->markAsAccepted();

        $this->success = 'Invitation accepted! You can now view ' . $this->invite->sender->name . '\'s mood data.';
    }

    public function rejectInvitation()
    {
        if (!Auth::check()) {
            return redirect()->route('auth.google')->with('intended', route('sharing.accept-invite', ['token' => $this->token]));
        }

        $user = Auth::user();

        // Check if email matches
        if ($this->invite->recipient_email !== $user->email) {
            $this->error = 'This invitation was sent to ' . $this->invite->recipient_email . '. Please log in with that account.';
            return;
        }

        // Mark invitation as rejected
        $this->invite->markAsRejected();

        $this->success = 'Invitation declined.';
    }

    public function render()
    {
        return view('livewire.accept-invite')->layout('layouts.app');
    }
}
