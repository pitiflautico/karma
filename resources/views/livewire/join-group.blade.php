<div class="min-h-screen bg-[#F7F3EF]">
    <!-- Header -->
    <div class="bg-white">
        <div class="px-6 py-4 flex items-center justify-between" style="padding-top: max(1rem, env(safe-area-inset-top, 0px) + 1rem);">
            <x-back-button />
            <h1 class="text-base font-semibold text-[#292524]">Join a Group</h1>
            <div class="w-7"></div>
        </div>
    </div>

    <!-- Content -->
    <div class="px-6 py-8">
        <!-- Illustration/Icon -->
        <div class="flex justify-center mb-8">
            <div class="w-24 h-24 bg-[#8B5CF6]/10 rounded-full flex items-center justify-center">
                <svg class="w-12 h-12 text-[#8B5CF6]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
        </div>

        <!-- Instructions -->
        <div class="text-center mb-8">
            <h2 class="text-xl font-bold text-[#292524] mb-2">Enter Invite Code</h2>
            <p class="text-[#57534e]">Enter the 8-character code shared by the group admin</p>
        </div>

        <!-- Form -->
        <form wire:submit.prevent="joinGroup" class="space-y-6">
            <!-- Invite Code Input -->
            <div>
                <label for="invite_code" class="block text-sm font-medium text-[#292524] mb-2">
                    Invite Code
                </label>
                <input
                    type="text"
                    id="invite_code"
                    wire:model="inviteCode"
                    maxlength="8"
                    placeholder="ABC12XYZ"
                    class="w-full px-4 py-3.5 bg-white border border-gray-200 rounded-xl text-center text-xl font-bold tracking-wider uppercase text-[#292524] placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-[#8B5CF6] focus:border-transparent transition-all"
                    style="letter-spacing: 0.3em;"
                >
                @error('inviteCode')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <button
                type="submit"
                wire:loading.attr="disabled"
                class="w-full bg-[#8B5CF6] hover:bg-[#7C3AED] text-white font-semibold py-4 rounded-xl transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                <span wire:loading.remove>Join Group</span>
                <span wire:loading class="flex items-center justify-center gap-2">
                    <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Joining...
                </span>
            </button>
        </form>

        <!-- Help Text -->
        <div class="mt-8 p-4 bg-blue-50 rounded-xl">
            <div class="flex gap-3">
                <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="text-sm font-medium text-blue-900">How to get an invite code?</p>
                    <p class="text-sm text-blue-700 mt-1">Ask a group admin to share their group's unique 8-character invite code with you.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if (session()->has('message'))
        <div class="fixed top-20 left-4 right-4 z-50">
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl shadow-lg">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="font-medium">{{ session('message') }}</span>
                </div>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="fixed top-20 left-4 right-4 z-50">
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl shadow-lg">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
            </div>
        </div>
    @endif
</div>
