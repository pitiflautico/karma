<div>
    @if($isOpen && $mood)
        <!-- Modal Backdrop -->
        <div class="fixed inset-0 z-50 flex items-end justify-center" x-data="{ show: true }" x-show="show" x-transition>
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-black bg-opacity-50" wire:click="closeModal"></div>

            <!-- Modal Content (Bottom Sheet) -->
            <div class="relative bg-white rounded-t-3xl w-full max-w-lg pb-8 transform transition-transform">
            <!-- Close Button -->
            <div class="flex justify-end p-4">
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Mood Icon -->
            <div class="flex justify-center mb-6">
                <img src="{{ asset('images/moods/' . $mood->mood_icon) }}"
                     alt="{{ $mood->mood_name }}"
                     class="w-24 h-24">
            </div>

            <!-- Mood Name -->
            <h2 class="text-2xl font-bold text-center text-gray-900 mb-2">{{ $mood->mood_name }}</h2>

            <!-- Date and Time -->
            <p class="text-center text-gray-500 mb-6">
                {{ $mood->created_at->format('M d, Y') }} • {{ $mood->created_at->format('g:i A') }}
            </p>

            <!-- Calendar Event (if exists) -->
            @if($mood->calendarEvent)
                <div class="px-6 mb-6">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <svg class="h-5 w-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <div class="ml-3 flex-1">
                                <p class="text-sm font-medium text-blue-900">
                                    {{ $mood->calendarEvent->title }}
                                </p>
                                <p class="text-xs text-blue-700 mt-1">
                                    {{ $mood->calendarEvent->start_time->format('M d, Y • g:i A') }}
                                    @if($mood->calendarEvent->location)
                                        • {{ $mood->calendarEvent->location }}
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Note -->
            <div class="px-6 mb-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-2">Note</h3>
                @if($mood->note)
                    <p class="text-gray-600 text-sm leading-relaxed">{{ $mood->note }}</p>
                @else
                    <p class="text-gray-400 text-sm italic">No notes added</p>
                @endif
            </div>

            <!-- Doctor Warning for low moods -->
            @if($mood->needsDoctorConsultation())
                <div class="px-6 mb-6">
                    <div class="flex items-start bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <svg class="w-5 h-5 text-yellow-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-yellow-800">Consult with your doctor</p>
                            <p class="text-xs text-yellow-700 mt-1">You've been feeling low for a while. Consider talking to a healthcare professional.</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Action Buttons -->
            <div class="px-6 space-y-3">
                <!-- Edit Button -->
                <button
                    wire:click="editMood"
                    class="w-full py-4 bg-gradient-to-r from-purple-400 to-purple-500 hover:from-purple-500 hover:to-purple-600 text-white font-semibold rounded-full transition-all shadow-sm">
                    Edit Mood Entry
                </button>

                <!-- Close Button -->
                <button
                    wire:click="closeModal"
                    class="w-full py-4 bg-white border-2 border-gray-200 hover:bg-gray-50 text-gray-700 font-semibold rounded-full transition-all">
                    Close
                </button>
            </div>
        </div>
        </div>
    @endif
</div>
