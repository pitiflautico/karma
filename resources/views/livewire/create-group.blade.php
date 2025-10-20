<div class="min-h-screen bg-[#F7F3EF]">
    <!-- Header -->
    <x-interior-header title="Create Group" />

    <!-- Content -->
    <div class="px-6 py-6">
        <!-- Form -->
        <form wire:submit.prevent="createGroup" class="space-y-5">
            <!-- Group Name -->
            <div>
                <label for="name" class="block text-sm font-semibold text-[#292524] mb-2">
                    Group Name <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    id="name"
                    wire:model="name"
                    placeholder="e.g., Family, Work Team, Friends"
                    class="w-full px-4 py-3.5 bg-white border border-[#e7e5e4] rounded-2xl text-[#292524] placeholder-[#a8a29e] focus:outline-none focus:ring-2 focus:ring-[#8B5CF6] focus:border-transparent transition-all"
                >
                @error('name')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Group Description -->
            <div>
                <label for="description" class="block text-sm font-semibold text-[#292524] mb-2">
                    Description <span class="text-[#78716c] text-sm font-normal">(optional)</span>
                </label>
                <textarea
                    id="description"
                    wire:model="description"
                    rows="3"
                    placeholder="Add more details about the group..."
                    class="w-full px-4 py-3.5 bg-white border border-[#e7e5e4] rounded-2xl text-[#292524] placeholder-[#a8a29e] focus:outline-none focus:ring-2 focus:ring-[#8B5CF6] focus:border-transparent transition-all resize-none"
                ></textarea>
                @error('description')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Group Color -->
            <div>
                <label for="color" class="block text-sm font-semibold text-[#292524] mb-2">
                    Group Color <span class="text-[#78716c] text-sm font-normal">(optional)</span>
                </label>
                <div class="flex items-center gap-3">
                    <!-- Color Preview -->
                    <div class="w-14 h-14 rounded-full flex-shrink-0 flex items-center justify-center text-white text-xl font-bold shadow-sm"
                         style="background-color: {{ $color }}">
                        {{ substr($name ?: 'G', 0, 1) }}
                    </div>

                    <!-- Color Picker -->
                    <div class="flex-1">
                        <input
                            type="color"
                            id="color"
                            wire:model.live="color"
                            class="w-full h-12 cursor-pointer rounded-2xl border border-[#e7e5e4] bg-white"
                        >
                    </div>
                </div>
                <p class="text-xs text-[#78716c] mt-2">This color will be used for the group avatar</p>
            </div>

            <!-- Info Box -->
            <div class="p-4 bg-[#8B5CF6]/10 rounded-2xl border border-[#8B5CF6]/20">
                <div class="flex gap-3">
                    <svg class="w-5 h-5 text-[#8B5CF6] flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="text-sm font-semibold text-[#292524]">New Group</p>
                        <p class="text-sm text-[#57534e] mt-1">You will be the admin and can invite members using the invite code that will be generated.</p>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-2">
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="w-full bg-[#8B5CF6] hover:bg-[#7C3AED] text-white font-semibold py-4 rounded-full shadow-sm transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                    <span wire:loading.remove>Create Group</span>
                    <span wire:loading class="flex items-center justify-center gap-2">
                        <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Creating...
                    </span>
                </button>
            </div>
        </form>
    </div>

    <!-- Success Message -->
    @if (session()->has('success'))
        <div class="fixed top-20 left-4 right-4 z-50">
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl shadow-lg">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            </div>
        </div>
    @endif
</div>
