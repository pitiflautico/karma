<?php

namespace App\Livewire;

use App\Models\SharedAccess;
use App\Models\SharingInvite;
use App\Models\User;
use App\Notifications\SharingInviteNotification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SharingSettings extends Component
{
    public $recipientEmail = '';
    public $permissionLevel = 'basico'; // basico, intermedio, avanzado

    // For editing permissions
    public $editingAccessId = null;
    public $editCanViewMoods = true;
    public $editCanViewNotes = false;
    public $editCanViewSelfies = false;

    protected $rules = [
        'recipientEmail' => 'required|email|different:currentUserEmail',
    ];

    protected $messages = [
        'recipientEmail.different' => 'You cannot share data with yourself.',
    ];

    public function getCurrentUserEmailProperty()
    {
        return Auth::user()->email;
    }

    public function sendInvite()
    {
        $this->validate();

        $user = Auth::user();

        // Check if user is trying to share with themselves
        if ($this->recipientEmail === $user->email) {
            session()->flash('error', 'You cannot share data with yourself.');
            return;
        }

        // Check if there's already an active sharing relationship
        $recipientUser = User::where('email', $this->recipientEmail)->first();
        if ($recipientUser) {
            $existingAccess = SharedAccess::where('owner_id', $user->id)
                ->where('shared_with_user_id', $recipientUser->id)
                ->first();

            if ($existingAccess) {
                session()->flash('error', 'You are already sharing data with this user.');
                return;
            }
        }

        // Check if there's already a pending invitation
        $existingInvite = SharingInvite::where('sender_id', $user->id)
            ->where('recipient_email', $this->recipientEmail)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->first();

        if ($existingInvite) {
            session()->flash('error', 'You already have a pending invitation for this email.');
            return;
        }

        // Map permission levels to specific permissions
        $permissions = $this->getPermissionsForLevel($this->permissionLevel);

        // Create new invitation
        $invite = SharingInvite::create([
            'sender_id' => $user->id,
            'recipient_email' => $this->recipientEmail,
            'token' => SharingInvite::generateToken(),
            'status' => 'pending',
            'can_view_moods' => $permissions['can_view_moods'],
            'can_view_notes' => $permissions['can_view_notes'],
            'can_view_selfies' => $permissions['can_view_selfies'],
            'expires_at' => now()->addDays(7),
        ]);

        // Send notification email to recipient
        try {
            \Notification::route('mail', $this->recipientEmail)
                ->notify(new SharingInviteNotification($invite));

            session()->flash('success', 'Invitation sent successfully! The recipient will receive an email to accept your invitation.');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to send invitation email. Please try again.');
            \Log::error('Failed to send sharing invitation', [
                'invite_id' => $invite->id,
                'error' => $e->getMessage(),
            ]);
        }

        // Reset form
        $this->reset(['recipientEmail', 'permissionLevel']);
        $this->permissionLevel = 'basico';
    }

    private function getPermissionsForLevel($level)
    {
        return match($level) {
            'basico' => [
                'can_view_moods' => true,
                'can_view_notes' => false,
                'can_view_selfies' => false,
            ],
            'intermedio' => [
                'can_view_moods' => true,
                'can_view_notes' => true,
                'can_view_selfies' => false,
            ],
            'avanzado' => [
                'can_view_moods' => true,
                'can_view_notes' => false,
                'can_view_selfies' => true,
            ],
            default => [
                'can_view_moods' => true,
                'can_view_notes' => false,
                'can_view_selfies' => false,
            ],
        };
    }

    public function editPermissions($accessId)
    {
        $access = SharedAccess::where('id', $accessId)
            ->where('owner_id', Auth::id())
            ->first();

        if ($access) {
            $this->editingAccessId = $accessId;
            $this->editCanViewMoods = $access->can_view_moods;
            $this->editCanViewNotes = $access->can_view_notes;
            $this->editCanViewSelfies = $access->can_view_selfies;
        }
    }

    public function updatePermissions()
    {
        $access = SharedAccess::where('id', $this->editingAccessId)
            ->where('owner_id', Auth::id())
            ->first();

        if ($access) {
            $access->update([
                'can_view_moods' => $this->editCanViewMoods,
                'can_view_notes' => $this->editCanViewNotes,
                'can_view_selfies' => $this->editCanViewSelfies,
            ]);

            session()->flash('success', 'Permissions updated successfully!');
            $this->editingAccessId = null;
        }
    }

    public function cancelEdit()
    {
        $this->editingAccessId = null;
    }

    public function revokeAccess($accessId)
    {
        $access = SharedAccess::where('id', $accessId)
            ->where('owner_id', Auth::id())
            ->first();

        if ($access) {
            $sharedWithUser = $access->sharedWithUser;
            $access->delete();

            session()->flash('success', "Access revoked for {$sharedWithUser->name}.");
        }
    }

    public function cancelInvite($inviteId)
    {
        $invite = SharingInvite::where('id', $inviteId)
            ->where('sender_id', Auth::id())
            ->first();

        if ($invite) {
            $invite->delete();
            session()->flash('success', 'Invitation cancelled.');
        }
    }

    /**
     * Detect if the request is from a mobile device or native app
     */
    private function isMobileDevice()
    {
        // Check if there's a session variable indicating mobile/native app
        if (session()->has('is_mobile_app') || session()->has('native_app_login')) {
            return true;
        }

        // Check for mobile query parameter (can be set by native app on first load)
        if (request()->has('mobile') && request()->input('mobile') == '1') {
            session()->put('is_mobile_app', true);
            return true;
        }

        // Check user agent for mobile devices
        $userAgent = request()->header('User-Agent');
        if ($userAgent) {
            $mobileKeywords = ['Mobile', 'Android', 'iPhone', 'iPad', 'iPod', 'BlackBerry', 'Windows Phone'];
            foreach ($mobileKeywords as $keyword) {
                if (stripos($userAgent, $keyword) !== false) {
                    return true;
                }
            }
        }

        return false;
    }

    public function render()
    {
        $user = Auth::user();

        // Get all active sharing relationships
        $sharedAccesses = SharedAccess::where('owner_id', $user->id)
            ->with('sharedWithUser')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get pending invitations
        $pendingInvites = SharingInvite::where('sender_id', $user->id)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->orderBy('created_at', 'desc')
            ->get();

        // Detect if mobile device and render appropriate layout
        if ($this->isMobileDevice()) {
            return view('livewire.sharing-settings', [
                'sharedAccesses' => $sharedAccesses,
                'pendingInvites' => $pendingInvites,
            ])->layout('layouts.app-mobile');
        }

        return view('livewire.sharing-settings', [
            'sharedAccesses' => $sharedAccesses,
            'pendingInvites' => $pendingInvites,
        ])->layout('layouts.app');
    }
}
