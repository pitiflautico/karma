<div>
    <div class="fixed inset-0 w-full h-full overflow-hidden bg-[#F5F1EB]">
        <!-- Content -->
        <div class="relative z-10 flex flex-col h-full overflow-y-auto">

            <!-- 3-Stage Progress Bar -->
            <div class="pt-16 px-6">
                <div class="flex items-center justify-center gap-2">
                    <!-- Welcome Stage -->
                    <div class="flex items-center">
                        <div class="flex flex-col items-center gap-2">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center border-2 bg-white border-gray-300 transition-all">
                                <div class="w-3 h-3 rounded-full bg-gray-300"></div>
                            </div>
                            <span class="text-gray-700 text-xs font-medium">Welcome</span>
                        </div>
                    </div>

                    <!-- Connector Line -->
                    <div class="w-16 h-0.5 bg-[#8BC34A] transition-all mb-6"></div>

                    <!-- Info Personal Stage -->
                    <div class="flex items-center">
                        <div class="flex flex-col items-center gap-2">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center border-2 bg-[#8BC34A] border-[#8BC34A] transition-all">
                                <div class="w-3 h-3 rounded-full bg-white"></div>
                            </div>
                            <span class="text-gray-700 text-xs font-medium">Info Personal</span>
                        </div>
                    </div>

                    <!-- Connector Line -->
                    <div class="w-16 h-0.5 bg-gray-300 transition-all mb-6"></div>

                    <!-- Empezar Stage -->
                    <div class="flex items-center">
                        <div class="flex flex-col items-center gap-2">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center border-2 bg-white border-gray-300 transition-all">
                                <div class="w-3 h-3 rounded-full bg-gray-300"></div>
                            </div>
                            <span class="text-gray-700 text-xs font-medium">Empezar</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- STEP WIZARD -->
            <div class="flex-1 flex flex-col">
                <!-- 7-Step Progress Bar -->
                <div class="pt-6 px-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-8"></div>
                        <span class="text-gray-700 text-sm font-medium">Step 1 of 7</span>
                        <div class="w-8"></div>
                    </div>
                    <div class="w-full bg-gray-300 rounded-full h-2">
                        <div class="bg-[#7C4DFF] rounded-full h-2 transition-all duration-300" style="width: {{ (1 / 7) * 100 }}%"></div>
                    </div>
                </div>

                <!-- Step Content -->
                <div class="flex-1 flex items-center justify-center px-6 py-8">
                    <div class="w-full max-w-md">
                        <!-- STEP 1: NAME -->
                        <div class="text-center mb-8">
                            <h2 class="text-gray-900 text-3xl font-semibold mb-4">¿Cómo te llamas?</h2>
                            <p class="text-gray-600 text-base">Nos encantaría saber cómo llamarte.</p>
                        </div>
                        <input
                            type="text"
                            wire:model="name"
                            placeholder="Escribe tu nombre..."
                            class="w-full px-6 py-4 rounded-full text-lg text-center border-2 border-gray-300 bg-white text-gray-900 placeholder-gray-400 focus:outline-none focus:border-[#7C4DFF]">

                        @error('name')
                            <p class="text-red-500 text-sm mt-2 text-center">{{ $message }}</p>
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
