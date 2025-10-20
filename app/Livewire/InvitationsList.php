<?php

namespace App\Livewire;

use App\Models\SharedAccess;
use App\Models\SharingInvite;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class InvitationsList extends Component
{
    public $showDeleteConfirm = false;
    public $itemToDelete = null;
    public $deleteType = null; // 'access' or 'invite'

    #[On('confirm-delete-access')]
    public function confirmDeleteAccess($id)
    {
        $this->itemToDelete = $id;
        $this->deleteType = 'access';
        $this->showDeleteConfirm = true;
    }

    #[On('confirm-delete-invite')]
    public function confirmDeleteInvite($id)
    {
        $this->itemToDelete = $id;
        $this->deleteType = 'invite';
        $this->showDeleteConfirm = true;
    }

    public function cancelDelete()
    {
        $this->showDeleteConfirm = false;
        $this->itemToDelete = null;
        $this->deleteType = null;
    }

    public function confirmDelete()
    {
        if ($this->deleteType === 'access') {
            $this->deleteAccess();
        } elseif ($this->deleteType === 'invite') {
            $this->deleteInvite();
        }

        $this->showDeleteConfirm = false;
        $this->itemToDelete = null;
        $this->deleteType = null;
    }

    private function deleteAccess()
    {
        $access = SharedAccess::where('id', $this->itemToDelete)
            ->where(function($query) {
                $query->where('owner_id', Auth::id())
                      ->orWhere('shared_with_user_id', Auth::id());
            })
            ->first();

        if ($access) {
            $access->delete();
            session()->flash('success', 'Acceso eliminado correctamente');
        }
    }

    private function deleteInvite()
    {
        $invite = SharingInvite::where('id', $this->itemToDelete)
            ->where('inviter_id', Auth::id())
            ->first();

        if ($invite) {
            $invite->delete();
            session()->flash('success', 'Invitación eliminada correctamente');
        }
    }

    private function isMobileDevice()
    {
        if (session()->has('is_mobile_app') || session()->has('native_app_login')) {
            return true;
        }

        if (request()->has('mobile') && request()->input('mobile') == '1') {
            session()->put('is_mobile_app', true);
            return true;
        }

        $userAgent = request()->header('User-Agent');
        $mobileKeywords = ['Mobile', 'Android', 'iPhone', 'iPad', 'iPod', 'BlackBerry', 'Windows Phone'];

        foreach ($mobileKeywords as $keyword) {
            if (stripos($userAgent, $keyword) !== false) {
                return true;
            }
        }

        return false;
    }

    public function render()
    {
        $user = Auth::user();

        // Get people who have shared with me (accepted invitations)
        $sharedWithMe = SharedAccess::where('shared_with_user_id', $user->id)
            ->with('owner')
            ->get();

        // Get pending invitations I've received
        $pendingInvites = SharingInvite::where('invitee_email', $user->email)
            ->where('status', 'pending')
            ->with('inviter')
            ->get();

        // Get people I've shared with (my shared accesses)
        $mySharedAccesses = SharedAccess::where('owner_id', $user->id)
            ->with('sharedWith')
            ->get();

        // Get invitations I've sent
        $myInvites = SharingInvite::where('inviter_id', $user->id)
            ->get();

        if ($this->isMobileDevice()) {
            return view('livewire.invitations-list', [
                'sharedWithMe' => $sharedWithMe,
                'pendingInvites' => $pendingInvites,
                'mySharedAccesses' => $mySharedAccesses,
                'myInvites' => $myInvites,
            ])->layout('layouts.app-mobile');
        }

        return view('livewire.invitations-list', [
            'sharedWithMe' => $sharedWithMe,
            'pendingInvites' => $pendingInvites,
            'mySharedAccesses' => $mySharedAccesses,
            'myInvites' => $myInvites,
        ])->layout('layouts.app');
    }
}
