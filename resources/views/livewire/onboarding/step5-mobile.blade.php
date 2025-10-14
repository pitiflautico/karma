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
                <div class="flex-1 flex flex-col justify-center px-6 py-8">
                    <div class="w-full max-w-md mx-auto">
                        <!-- STEP 5: MOOD SELECTOR -->
                        <div class="text-center mb-16">
                            <h2 class="text-gray-900 text-4xl font-bold">¿Cómo te sientes?</h2>
                        </div>

                        <!-- Mood Selector Widget -->
                        <div x-data="{
                            moodLevel: {{ $moodLevel }},

                            moods: [
                                { level: 1, label: 'deprimido', color: '#C084FC', icon: '/images/moods/depressed_icon.svg' },
                                { level: 2, label: 'mal', color: '#FB923C', icon: '/images/moods/Sad_icon.svg' },
                                { level: 3, label: 'normal', color: '#B1865E', icon: '/images/moods/Normal_icon.svg' },
                                { level: 4, label: 'feliz', color: '#FBBF24', icon: '/images/moods/Happy_icon.svg' },
                                { level: 5, label: 'genial', color: '#9BB167', icon: '/images/moods/Great_icon.svg' }
                            ],

                            get currentMood() {
                                return this.moods[this.moodLevel - 1];
                            },

                            init() {
                                $wire.set('moodLevel', this.moodLevel);
                            },

                            selectMood(level) {
                                this.moodLevel = level;
                                $wire.set('moodLevel', this.moodLevel);
                            }
                        }" class="relative">

                            <!-- Central Emoji Display -->
                            <div class="flex justify-center items-center mb-12">
                                <img :src="currentMood.icon" :alt="currentMood.label" class="w-64 h-64 object-contain">
                            </div>

                            <!-- Mood text -->
                            <div class="text-center mb-24">
                                <p class="text-gray-800 text-2xl font-normal" x-text="'Me siento ' + currentMood.label + '.'"></p>
                            </div>

                            <!-- Mood selector buttons in white container -->
                            <div class="flex justify-center">
                                <div class="bg-white rounded-full shadow-lg py-4 px-6 inline-flex items-center gap-3">
                                    <template x-for="mood in moods" :key="'mood-'+mood.level">
                                        <button
                                            type="button"
                                            @click="selectMood(mood.level)"
                                            class="transition-all"
                                            :class="moodLevel === mood.level ? '' : 'grayscale opacity-40'">
                                            <img :src="mood.icon" :alt="mood.label" class="w-14 h-14 object-contain">
                                        </button>
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
