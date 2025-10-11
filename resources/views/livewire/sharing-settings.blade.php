<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h2 class="text-3xl font-bold text-gray-900">Sharing Settings</h2>
            <p class="mt-2 text-gray-600">Manage who can access your emotional wellbeing data</p>
        </div>

        <!-- Success/Error Messages -->
        @if (session()->has('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <!-- Send New Invitation -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Invite Someone to View Your Data</h3>
            <p class="text-sm text-gray-600 mb-4">Share your mood data with trusted contacts. They will receive an email invitation.</p>

            <form wire:submit.prevent="sendInvite">
                <div class="mb-4">
                    <label for="recipientEmail" class="block text-sm font-medium text-gray-700 mb-2">
                        Email Address
                    </label>
                    <input
                        type="email"
                        id="recipientEmail"
                        wire:model="recipientEmail"
                        placeholder="friend@example.com"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm"
                    >
                    @error('recipientEmail')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-4">
                    <p class="text-sm font-medium text-gray-700 mb-2">Default Permissions (can be changed later)</p>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input
                                type="checkbox"
                                wire:model="canViewMoods"
                                class="rounded border-gray-300 text-purple-600 focus:ring-purple-500"
                            >
                            <span class="ml-2 text-sm text-gray-700">Can view mood scores</span>
                        </label>
                        <label class="flex items-center">
                            <input
                                type="checkbox"
                                wire:model="canViewNotes"
                                class="rounded border-gray-300 text-purple-600 focus:ring-purple-500"
                            >
                            <span class="ml-2 text-sm text-gray-700">Can view notes</span>
                        </label>
                        <label class="flex items-center">
                            <input
                                type="checkbox"
                                wire:model="canViewSelfies"
                                class="rounded border-gray-300 text-purple-600 focus:ring-purple-500"
                            >
                            <span class="ml-2 text-sm text-gray-700">Can view emotional selfies</span>
                        </label>
                    </div>
                </div>

                <button
                    type="submit"
                    class="inline-flex justify-center px-6 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500"
                >
                    Send Invitation
                </button>
            </form>
        </div>

        <!-- Pending Invitations -->
        @if($pendingInvites->count() > 0)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Pending Invitations</h3>
                <div class="space-y-3">
                    @foreach($pendingInvites as $invite)
                        <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">{{ $invite->recipient_email }}</p>
                                    <p class="text-xs text-gray-500">Sent {{ $invite->created_at->diffForHumans() }} • Expires {{ $invite->expires_at->diffForHumans() }}</p>
                                </div>
                                <button
                                    wire:click="cancelInvite({{ $invite->id }})"
                                    wire:confirm="Are you sure you want to cancel this invitation?"
                                    class="px-3 py-1 text-sm font-medium text-red-700 bg-red-50 hover:bg-red-100 rounded-md transition"
                                >
                                    Cancel
                                </button>
                            </div>
                            <div class="flex flex-wrap gap-2 mt-2">
                                <p class="text-xs text-gray-600 mr-2">Invited to view:</p>
                                @if($invite->can_view_moods)
                                    <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded">Mood Scores</span>
                                @endif
                                @if($invite->can_view_notes)
                                    <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded">Notes</span>
                                @endif
                                @if($invite->can_view_selfies)
                                    <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded">Selfies</span>
                                @endif
                                @if(!$invite->can_view_moods && !$invite->can_view_notes && !$invite->can_view_selfies)
                                    <span class="text-xs text-gray-500 italic">No permissions</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Active Sharing -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">People Who Can View Your Data</h3>

            @if($sharedAccesses->count() > 0)
                <div class="space-y-4">
                    @foreach($sharedAccesses as $access)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                                        <span class="text-purple-600 font-semibold text-lg">{{ substr($access->sharedWithUser->name, 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $access->sharedWithUser->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $access->sharedWithUser->email }}</p>
                                    </div>
                                </div>
                                <div class="flex space-x-2">
                                    @if($editingAccessId === $access->id)
                                        <button
                                            wire:click="updatePermissions"
                                            class="px-3 py-1 text-sm font-medium text-green-700 bg-green-50 hover:bg-green-100 rounded-md transition"
                                        >
                                            Save
                                        </button>
                                        <button
                                            wire:click="cancelEdit"
                                            class="px-3 py-1 text-sm font-medium text-gray-700 bg-gray-50 hover:bg-gray-100 rounded-md transition"
                                        >
                                            Cancel
                                        </button>
                                    @else
                                        <button
                                            wire:click="editPermissions('{{ $access->id }}')"
                                            class="px-3 py-1 text-sm font-medium text-purple-700 bg-purple-50 hover:bg-purple-100 rounded-md transition"
                                        >
                                            Edit
                                        </button>
                                        <button
                                            wire:click="revokeAccess('{{ $access->id }}')"
                                            wire:confirm="Are you sure you want to revoke access for {{ $access->sharedWithUser->name }}?"
                                            class="px-3 py-1 text-sm font-medium text-red-700 bg-red-50 hover:bg-red-100 rounded-md transition"
                                        >
                                            Revoke
                                        </button>
                                    @endif
                                </div>
                            </div>

                            <!-- Permissions -->
                            <div class="space-y-2 pl-13">
                                @if($editingAccessId === $access->id)
                                    <label class="flex items-center">
                                        <input
                                            type="checkbox"
                                            wire:model="editCanViewMoods"
                                            class="rounded border-gray-300 text-purple-600 focus:ring-purple-500"
                                        >
                                        <span class="ml-2 text-sm text-gray-700">Can view mood scores</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="checkbox"
                                            wire:model="editCanViewNotes"
                                            class="rounded border-gray-300 text-purple-600 focus:ring-purple-500"
                                        >
                                        <span class="ml-2 text-sm text-gray-700">Can view notes</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="checkbox"
                                            wire:model="editCanViewSelfies"
                                            class="rounded border-gray-300 text-purple-600 focus:ring-purple-500"
                                        >
                                        <span class="ml-2 text-sm text-gray-700">Can view emotional selfies</span>
                                    </label>
                                @else
                                    <div class="flex flex-wrap gap-2">
                                        @if($access->can_view_moods)
                                            <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded">Mood Scores</span>
                                        @endif
                                        @if($access->can_view_notes)
                                            <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded">Notes</span>
                                        @endif
                                        @if($access->can_view_selfies)
                                            <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded">Selfies</span>
                                        @endif
                                        @if(!$access->can_view_moods && !$access->can_view_notes && !$access->can_view_selfies)
                                            <span class="text-xs text-gray-500 italic">No permissions granted</span>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <p class="text-xs text-gray-500 mt-3">Shared since {{ $access->created_at->format('M d, Y') }}</p>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <p class="text-gray-600">You haven't shared your data with anyone yet.</p>
                    <p class="text-sm text-gray-500 mt-2">Send an invitation above to get started.</p>
                </div>
            @endif
        </div>

        <!-- Privacy Notice -->
        <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <div class="flex items-start">
                <svg class="h-5 w-5 text-blue-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <p class="text-sm font-medium text-blue-900">Privacy First</p>
                    <p class="text-sm text-blue-700 mt-1">
                        Your data is private by default. Only people you explicitly invite will have access, and you can revoke access at any time.
                        They will only see the data you've chosen to share with them.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
