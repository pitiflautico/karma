<div>
    <div class="min-h-screen bg-gradient-to-br from-purple-50 to-blue-50 flex items-center justify-center p-8">
        <div class="max-w-2xl w-full bg-white rounded-3xl shadow-2xl p-12">

            <!-- Progress indicator -->
            <div class="mb-8">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-sm font-medium text-gray-600">Step 6 of 7</span>
                    <button wire:click="skip" class="text-sm text-gray-500 hover:text-gray-700">
                        Skip
                    </button>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-[#7C4DFF] rounded-full h-2 transition-all duration-300" style="width: {{ (6 / 7) * 100 }}%"></div>
                </div>
            </div>

            <!-- Title -->
            <h2 class="text-4xl font-bold text-gray-900 mb-12 text-center">What is your weight?</h2>

            <!-- Weight Input -->
            <div class="space-y-8">
                <!-- Unit Toggle -->
                <div class="flex justify-center mb-8">
                    <div class="bg-gray-100 rounded-full p-1 inline-flex">
                        <button
                            type="button"
                            wire:click="$set('unit', 'kg')"
                            class="px-8 py-3 rounded-full text-base font-medium transition-all"
                            @class([
                                'bg-white text-gray-900 shadow-sm' => $unit === 'kg',
                                'text-gray-600' => $unit !== 'kg'
                            ])>
                            kg
                        </button>
                        <button
                            type="button"
                            wire:click="$set('unit', 'lbs')"
                            class="px-8 py-3 rounded-full text-base font-medium transition-all"
                            @class([
                                'bg-white text-gray-900 shadow-sm' => $unit === 'lbs',
                                'text-gray-600' => $unit !== 'lbs'
                            ])>
                            lbs
                        </button>
                    </div>
                </div>

                <!-- Weight Input -->
                <div class="flex justify-center items-center gap-4">
                    <input
                        type="number"
                        wire:model="weight"
                        min="20"
                        max="500"
                        class="w-40 text-center text-5xl font-bold text-gray-900 border-b-4 border-[#8BC34A] focus:outline-none focus:border-[#7C4DFF] transition-colors"
                        placeholder="70">
                    <span class="text-3xl text-gray-600 font-medium">{{ $unit }}</span>
                </div>

                @error('weight')
                    <p class="text-red-500 text-sm text-center">{{ $message }}</p>
                @enderror
            </div>

            <!-- Navigation buttons -->
            <div class="flex gap-4 mt-16">
                <button
                    wire:click="back"
                    class="flex-1 bg-gray-200 text-gray-700 font-semibold py-4 px-6 rounded-full text-lg hover:bg-gray-300 transition-all">
                    ← Back
                </button>
                <button
                    wire:click="saveAndContinue"
                    class="flex-1 bg-[#7C4DFF] text-white font-semibold py-4 px-6 rounded-full text-lg shadow-lg hover:bg-[#6A3DE8] transition-all">
                    Continue →
                </button>
            </div>
        </div>
    </div>
</div>
