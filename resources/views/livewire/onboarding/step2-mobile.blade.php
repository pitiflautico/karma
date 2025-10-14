<div>
    <div class="fixed inset-0 w-full h-full overflow-hidden bg-[#F5F1EB]">
        <!-- Content -->
        <div class="relative z-10 flex flex-col h-full overflow-y-auto">

            <!-- STEP WIZARD -->
            <div class="flex-1 flex flex-col">
                <!-- 7-Step Progress Bar -->
                <div class="pt-6 px-6">
                    <div class="flex items-center justify-between mb-4">
                        <button wire:click="back" class="text-gray-700 text-2xl">←</button>
                        <span class="text-gray-700 text-sm font-medium">Step 2 of 7</span>
                        <div class="w-8"></div>
                    </div>
                    <div class="w-full bg-gray-300 rounded-full h-2">
                        <div class="bg-[#7C4DFF] rounded-full h-2 transition-all duration-300" style="width: {{ (2 / 7) * 100 }}%"></div>
                    </div>
                </div>

                <!-- Step Content -->
                <div class="flex-1 flex items-center justify-center px-6 py-8">
                    <div class="w-full max-w-md">
                        <!-- STEP 2: HELP REASONS -->
                        <div class="text-center mb-8">
                            <h2 class="text-gray-900 text-3xl font-semibold mb-4">¿En qué podemos ayudarte?</h2>
                            <p class="text-gray-600 text-base">Selecciona todas las que apliquen</p>
                        </div>
                        <div class="space-y-3">
                            @foreach($availableReasons as $reason)
                                <button
                                    type="button"
                                    wire:click="toggleReason('{{ $reason }}')"
                                    class="w-full px-6 py-4 rounded-full text-left transition-all
                                        {{ in_array($reason, $helpReasons) ? 'bg-[#7C4DFF] text-white font-semibold' : 'bg-white text-gray-700 border-2 border-gray-300' }}">
                                    {{ $reason }}
                                </button>
                            @endforeach
                        </div>

                        @error('helpReasons')
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
