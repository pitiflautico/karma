<div class="min-h-screen bg-gray-100 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full">
        @if($error)
            <!-- Error State -->
            <div class="bg-white rounded-lg shadow-lg p-8 text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                    <svg class="h-10 w-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Invitation Error</h2>
                <p class="text-gray-600 mb-6">{{ $error }}</p>
                <a href="{{ route('dashboard') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700">
                    Go to Dashboard
                </a>
            </div>
        @elseif($success)
            <!-- Success State -->
            <div class="bg-white rounded-lg shadow-lg p-8 text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                    <svg class="h-10 w-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Success!</h2>
                <p class="text-gray-600 mb-6">{{ $success }}</p>
                <div class="space-y-3">
                    <a href="{{ route('shared.with.me') }}" class="block w-full px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700">
                        View Shared Data
                    </a>
                    <a href="{{ route('dashboard') }}" class="block w-full px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Go to Dashboard
                    </a>
                </div>
            </div>
        @elseif($invite)
            <!-- Invitation Details -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="bg-purple-600 px-8 py-6">
                    <h2 class="text-2xl font-bold text-white text-center">Sharing Invitation</h2>
                </div>

                <div class="px-8 py-6">
                    <div class="text-center mb-6">
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-purple-100 mb-4">
                            <span class="text-purple-600 font-bold text-2xl">{{ substr($invite->sender->name, 0, 1) }}</span>
                        </div>
                        <p class="text-lg text-gray-900">
                            <span class="font-semibold">{{ $invite->sender->name }}</span> wants to share their emotional wellbeing data with you
                        </p>
                        <p class="text-sm text-gray-500 mt-1">{{ $invite->sender->email }}</p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4 mb-6">
                        <h3 class="text-sm font-medium text-gray-700 mb-3">You will be able to view:</h3>
                        <div class="space-y-2">
                            <div class="flex items-center">
                                @if($invite->can_view_moods)
                                    <svg class="h-5 w-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span class="text-sm text-gray-700">Mood scores</span>
                                @else
                                    <svg class="h-5 w-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    <span class="text-sm text-gray-400">Mood scores (not granted)</span>
                                @endif
                            </div>
                            <div class="flex items-center">
                                @if($invite->can_view_notes)
                                    <svg class="h-5 w-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span class="text-sm text-gray-700">Notes</span>
                                @else
                                    <svg class="h-5 w-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    <span class="text-sm text-gray-400">Notes (not granted)</span>
                                @endif
                            </div>
                            <div class="flex items-center">
                                @if($invite->can_view_selfies)
                                    <svg class="h-5 w-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span class="text-sm text-gray-700">Emotional selfies</span>
                                @else
                                    <svg class="h-5 w-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    <span class="text-sm text-gray-400">Emotional selfies (not granted)</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                        <div class="flex items-start">
                            <svg class="h-5 w-5 text-blue-600 mt-0.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-blue-900">Important</p>
                                <p class="text-sm text-blue-700 mt-1">
                                    By accepting, you will have access to {{ $invite->sender->name }}'s mood data.
                                    They can revoke access at any time.
                                </p>
                            </div>
                        </div>
                    </div>

                    <p class="text-xs text-gray-500 text-center mb-6">
                        This invitation expires {{ $invite->expires_at->diffForHumans() }}
                    </p>

                    @auth
                        @if(Auth::user()->email === $invite->recipient_email)
                            <div class="space-y-3">
                                <button
                                    wire:click="acceptInvitation"
                                    class="w-full px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                                >
                                    Accept Invitation
                                </button>
                                <button
                                    wire:click="rejectInvitation"
                                    wire:confirm="Are you sure you want to decline this invitation?"
                                    class="w-full px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500"
                                >
                                    Decline
                                </button>
                            </div>
                        @else
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                                <p class="text-sm text-yellow-800">
                                    This invitation was sent to <strong>{{ $invite->recipient_email }}</strong>.
                                    Please log out and log in with that account to accept this invitation.
                                </p>
                            </div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Log out and switch accounts
                                </button>
                            </form>
                        @endif
                    @else
                        <div class="text-center">
                            <p class="text-sm text-gray-600 mb-4">You need to be logged in to accept this invitation</p>
                            <a href="{{ route('auth.google') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700">
                                <svg class="h-5 w-5 mr-2" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                                </svg>
                                Login with Google
                            </a>
                        </div>
                    @endauth
                </div>
            </div>
        @endif
    </div>
</div>
