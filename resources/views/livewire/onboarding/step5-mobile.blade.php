<div>
    <div class="fixed inset-0 w-full h-full overflow-hidden bg-[#F5F1EB]">
        <!-- Content -->
        <div class="relative z-10 flex flex-col h-full overflow-y-auto">

            <!-- STEP WIZARD -->
            <div class="flex-1 flex flex-col">
                <!-- Header with back, step counter, and skip -->
                <div class="pt-16 px-6">
                    <div class="flex items-center justify-between mb-4">
                        <button wire:click="back" class="text-gray-700 text-2xl">←</button>
                        <span class="text-gray-700 text-sm font-medium">Step 5 of 7</span>
                        <button wire:click="skip" class="text-gray-600 text-base font-medium hover:text-gray-800">
                            Skip
                        </button>
                    </div>
                    <div class="w-full bg-gray-300 rounded-full h-2">
                        <div class="bg-[#7C4DFF] rounded-full h-2 transition-all duration-300" style="width: {{ (5 / 7) * 100 }}%"></div>
                    </div>
                </div>

                <!-- Step Content -->
                <div class="flex-1 flex flex-col justify-center overflow-hidden">
                    <div class="w-full">
                        <!-- STEP 5: MOOD SELECTOR -->
                        <div class="text-center mb-4 px-6">
                            <h2 class="text-gray-900 text-xl font-semibold">Describe como te sientes ahora mismo</h2>
                        </div>

                        <!-- Mood Selector Widget (nuevo diseño con control segmentado) -->
                        <div x-data="{
                            moodLevel: {{ $moodLevel }},
                            isDragging: false,
                            startX: 0,
                            startMoodLevel: {{ $moodLevel }},
                            dragThreshold: 50,
                            // Config antigua ya no usada para UI, pero mantenida por compat
                            segmentCount: 5,
                            wheelSize: 560,
                            ringThickness: 150,

                            moods: [
                                { level: 1, label: 'Muy mal', color: '#C084FC', icon: '/images/moods/depressed_icon.svg' },
                                { level: 2, label: 'Mal', color: '#FB923C', icon: '/images/moods/Sad_icon.svg' },
                                { level: 3, label: 'Normal', color: '#B1865E', icon: '/images/moods/Normal_icon.svg' },
                                { level: 4, label: 'Feliz', color: '#FBBF24', icon: '/images/moods/Happy_icon.svg' },
                                { level: 5, label: 'Genial', color: '#9BB167', icon: '/images/moods/Great_icon.svg' }
                            ],

                            get currentMood() {
                                return this.moods[this.moodLevel - 1];
                            },

                            get rotationAngle() {
                                const center = this.centerAngle(this.moodLevel);
                                return -90 - center; // align center of selected segment to pointer
                            },

                            // Geometry helpers for semicircle gauge (bottom half of circle)
                            centerAngle(level) {
                                const segment = 180 / this.segmentCount; // 36° per segment
                                return -180 + (level - 0.5) * segment;
                            },

                            startAngle(level) {
                                const segment = 180 / this.segmentCount;
                                return -180 + (level - 1) * segment;
                            },

                            endAngle(level) {
                                const segment = 180 / this.segmentCount;
                                return -180 + level * segment;
                            },

                            iconCoords(level) {
                                const centerX = this.wheelSize / 2;
                                const centerY = this.wheelSize / 2;
                                const radius = (this.wheelSize / 2) - (this.ringThickness / 2) - 6;
                                const worldAngle = this.centerAngle(level) + this.rotationAngle;
                                const rad = (Math.PI / 180) * worldAngle;
                                return { x: centerX + radius * Math.cos(rad), y: centerY + radius * Math.sin(rad) };
                            },

                            init() { $wire.set('moodLevel', this.moodLevel); },

                            selectMood(level) { this.moodLevel = level; $wire.set('moodLevel', this.moodLevel); },

                            handleStart(e) {
                                this.isDragging = true;
                                const touch = e.touches ? e.touches[0] : e;
                                this.startX = touch.clientX;
                                this.startMoodLevel = this.moodLevel;
                            },

                            handleMove(e) {
                                if (!this.isDragging) return;

                                const touch = e.touches ? e.touches[0] : e;
                                const deltaX = touch.clientX - this.startX;

                                // If dragged enough to the right (positive deltaX), move left (decrease mood level)
                                // If dragged enough to the left (negative deltaX), move right (increase mood level)
                                if (Math.abs(deltaX) > this.dragThreshold) {
                                    if (deltaX > 0) {
                                        // Dragging right -> rotate counterclockwise -> decrease mood level
                                        this.moodLevel = this.startMoodLevel - 1;
                                        if (this.moodLevel < 1) this.moodLevel = 5;
                                    } else {
                                        // Dragging left -> rotate clockwise -> increase mood level
                                        this.moodLevel = this.startMoodLevel + 1;
                                        if (this.moodLevel > 5) this.moodLevel = 1;
                                    }

                                    // Reset start position for next drag increment
                                    this.startX = touch.clientX;
                                    this.startMoodLevel = this.moodLevel;
                                }
                            },

                            handleEnd() {
                                this.isDragging = false;
                                // Sync final position with Livewire
                                $wire.set('moodLevel', this.moodLevel);
                            }
                        }" class="relative">

                            <!-- Current mood text -->
                            <div class="text-center mb-2 px-6">
                                <p class="text-gray-600 text-sm" x-text="'Me encuentro ' + currentMood.label.toLowerCase()"></p>
                            </div>

                            <!-- Central Emoji Display grande -->
                            <div class="flex justify-center mb-3 px-6">
                                <div class="w-36 h-36 rounded-full flex items-center justify-center shadow-sm" :style="`background-color: ${currentMood.color}33`">
                                    <img :src="currentMood.icon" :alt="currentMood.label" class="w-28 h-28">
                                </div>
                            </div>

                            <!-- Control segmentado -->
                            <div class="mx-6 mt-6 bg-white rounded-full border border-gray-200 shadow-sm px-2 py-1">
                                <div class="grid grid-cols-5 divide-x divide-gray-200">
                                    <template x-for="level in [1,2,3,4,5]" :key="'seg-'+level">
                                        <div class="relative flex items-center justify-center py-2">
                                            <button type="button" @click="selectMood(level)" class="p-2 rounded-full">
                                                <img :src="moods[level-1].icon" :alt="moods[level-1].label" class="w-6 h-6" :class="moodLevel === level ? '' : 'opacity-40 grayscale'">
                                            </button>
                                            <span x-show="moodLevel === level" class="absolute -bottom-1 block w-1.5 h-1.5 rounded-full" style="background:#7C4DFF"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        @error('moodLevel')
                            <p class="text-red-500 text-sm mt-4 text-center">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Next Button -->
                <div class="p-6">
                    <button
                        wire:click="saveAndContinue"
                        class="w-full bg-[#7C4DFF] text-white font-semibold py-4 px-6 rounded-full text-lg shadow-lg hover:bg-[#6A3DE8] transition-all">
                        Siguiente
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>
