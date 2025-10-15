@props(['deleteId' => ''])

<!-- Swipeable Card Container -->
<div class="relative overflow-hidden rounded-2xl"
     x-data="{
         swiping: false,
         startX: 0,
         currentX: 0,
         deltaX: 0,
         handleTouchStart(e) {
             this.swiping = true;
             this.startX = e.touches[0].clientX;
             this.currentX = this.startX;
         },
         handleTouchMove(e) {
             if (!this.swiping) return;
             this.currentX = e.touches[0].clientX;
             this.deltaX = this.currentX - this.startX;
             if (this.deltaX > 0) this.deltaX = 0; // Only allow left swipe
             if (this.deltaX < -100) this.deltaX = -100; // Max swipe distance
         },
         handleTouchEnd() {
             this.swiping = false;
             if (this.deltaX < -50) {
                 this.deltaX = -100; // Snap to open
             } else {
                 this.deltaX = 0; // Snap to closed
             }
         }
     }">

    <!-- Delete Button (Behind) - positioned absolutely, hidden by default -->
    <div class="absolute top-0 right-0 bottom-0 flex items-center justify-end pr-6 bg-red-500"
         :class="deltaX < -5 ? 'opacity-100' : 'opacity-0'"
         style="width: 100px;">
        <button
            type="button"
            @click.prevent.stop="Livewire.dispatch('confirm-delete', { id: '{{ $deleteId }}' })"
            class="text-white flex items-center justify-center pointer-events-auto">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
        </button>
    </div>

    <!-- Main Card (Swipeable) -->
    <div class="bg-white rounded-2xl p-4 shadow-sm relative"
         @touchstart="handleTouchStart($event)"
         @touchmove="handleTouchMove($event)"
         @touchend="handleTouchEnd()"
         :style="`transform: translateX(${deltaX}px); transition: ${swiping ? 'none' : 'transform 0.3s ease-out'}`">
        {{ $slot }}
    </div>
</div>
