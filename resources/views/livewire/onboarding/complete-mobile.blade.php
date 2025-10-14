<div>
    <div class="fixed inset-0 w-full h-full overflow-hidden bg-[#F5F1EB]">
        <!-- Content -->
        <div class="relative z-10 flex flex-col h-full overflow-y-auto">

            <!-- Progress Steps -->
            <div class="pt-12 px-6">
                <div class="flex items-center justify-between mb-8">
                    <!-- Step 1: Welcome -->
                    <div class="flex flex-col items-center flex-1">
                        <div class="w-12 h-12 rounded-full bg-[#8BC34A] border-2 border-[#8BC34A] flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <span class="text-xs text-gray-700 mt-2 font-medium">Welcome</span>
                    </div>

                    <!-- Connector Line -->
                    <div class="flex-1 h-0.5 bg-gray-300 -mt-6"></div>

                    <!-- Step 2: Info Personal -->
                    <div class="flex flex-col items-center flex-1">
                        <div class="w-12 h-12 rounded-full bg-[#8BC34A] border-2 border-[#8BC34A] flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <span class="text-xs text-gray-700 mt-2 font-medium">Info Personal</span>
                    </div>

                    <!-- Connector Line -->
                    <div class="flex-1 h-0.5 bg-gray-300 -mt-6"></div>

                    <!-- Step 3: Empezar -->
                    <div class="flex flex-col items-center flex-1">
                        <div class="w-12 h-12 rounded-full bg-[#8BC34A] border-2 border-[#8BC34A] flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <span class="text-xs text-gray-700 mt-2 font-medium">Empezar</span>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="flex-1 flex flex-col justify-center items-center px-6 py-8">
                <!-- Illustration -->
                <div class="mb-8">
                    <img src="/images/celebration.png" alt="Celebration" class="w-80 h-80 object-contain">
                </div>

                <!-- Badge -->
                <div class="mb-6">
                    <div class="inline-flex items-center gap-2 px-6 py-2 border-2 border-[#7C4DFF] rounded-full">
                        <svg class="w-5 h-5 text-[#7C4DFF]" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 10.5a1.5 1.5 0 113 0v6a1.5 1.5 0 01-3 0v-6zM6 10.333v5.43a2 2 0 001.106 1.79l.05.025A4 4 0 008.943 18h5.416a2 2 0 001.962-1.608l1.2-6A2 2 0 0015.56 8H12V4a2 2 0 00-2-2 1 1 0 00-1 1v.667a4 4 0 01-.8 2.4L6.8 7.933a4 4 0 00-.8 2.4z"/>
                        </svg>
                        <span class="text-[#7C4DFF] text-base font-medium">gracias</span>
                    </div>
                </div>

                <!-- Title -->
                <h1 class="text-gray-900 text-3xl font-bold text-center mb-6 px-4">
                    Muchas gracias ya lo tenemos todo!
                </h1>

                <!-- Description -->
                <p class="text-gray-600 text-base text-center leading-relaxed px-8">
                    Ahora ya puedes empezar a cuidar de ti y saber aprender día a día a como te encuentras
                </p>
            </div>

            <!-- Start Button -->
            <div class="p-6">
                <button
                    wire:click="start"
                    class="w-full bg-[#7C4DFF] text-white font-semibold py-4 px-6 rounded-full text-lg shadow-lg hover:bg-[#6A3DE8] transition-all flex items-center justify-center gap-2">
                    Empezar
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </button>
            </div>

        </div>
    </div>
</div>
