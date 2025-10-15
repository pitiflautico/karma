@props(['mood'])

<div class="flex items-center justify-between">
    <!-- Left: Icon + Info -->
    <div class="flex items-center space-x-3 flex-1">
        <!-- Mood Icon -->
        <div class="w-12 h-12 flex items-center justify-center flex-shrink-0">
            <img src="{{ asset('images/moods/' . $mood->mood_icon) }}"
                 alt="{{ $mood->mood_name }}"
                 class="w-12 h-12">
        </div>

        <!-- Mood Info -->
        <div class="flex-1 min-w-0">
            <h4 class="font-semibold text-gray-900 text-base">{{ $mood->mood_name }}</h4>

            @if($mood->note)
                <p class="text-sm text-gray-600 line-clamp-2">{{ Str::words($mood->note, 14, '...') }}</p>
            @elseif($mood->calendarEvent)
                <p class="text-sm text-gray-600 line-clamp-2">{{ Str::words($mood->calendarEvent->title, 14, '...') }}</p>
            @else
                <p class="text-sm text-gray-400 italic">No notes</p>
            @endif

            <!-- Doctor Warning for low moods -->
            @if($mood->needsDoctorConsultation())
                <div class="flex items-center mt-1 text-yellow-600 text-xs">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    Consult with your doctor
                </div>
            @endif
        </div>
    </div>

    <!-- Right: Time + Arrow -->
    <div class="flex items-center space-x-3 ml-3 flex-shrink-0">
        <span class="text-sm text-gray-500 flex-shrink-0">{{ $mood->created_at->format('g:iA') }}</span>
        <button
            type="button"
            @click.prevent.stop="Livewire.dispatch('view-mood', { id: '{{ $mood->id }}' })"
            class="text-gray-400 flex-shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
    </div>
</div>
