<div>
    <div class="min-h-screen bg-gradient-to-br from-purple-50 to-blue-50 flex items-center justify-center p-8">
        <div class="max-w-3xl w-full bg-white rounded-3xl shadow-2xl p-12">

            <!-- Progress Steps -->
            <div class="mb-12">
                <div class="flex items-center justify-center gap-8">
                    <!-- Step 1: Welcome -->
                    <div class="flex flex-col items-center">
                        <div class="w-16 h-16 rounded-full bg-[#8BC34A] flex items-center justify-center mb-2">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <span class="text-sm text-gray-700 font-medium">Welcome</span>
                    </div>

                    <!-- Connector Line -->
                    <div class="flex-1 h-1 bg-gray-300 max-w-[100px]"></div>

                    <!-- Step 2: Info Personal -->
                    <div class="flex flex-col items-center">
                        <div class="w-16 h-16 rounded-full bg-[#8BC34A] flex items-center justify-center mb-2">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <span class="text-sm text-gray-700 font-medium">Info Personal</span>
                    </div>

                    <!-- Connector Line -->
                    <div class="flex-1 h-1 bg-gray-300 max-w-[100px]"></div>

                    <!-- Step 3: Empezar -->
                    <div class="flex flex-col items-center">
                        <div class="w-16 h-16 rounded-full bg-[#8BC34A] flex items-center justify-center mb-2">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <span class="text-sm text-gray-700 font-medium">Empezar</span>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="text-center">
                <!-- Illustration -->
                <div class="mb-10">
                    <img src="/images/celebration.png" alt="Celebration" class="w-96 h-96 object-contain mx-auto">
                </div>

                <!-- Badge -->
                <div class="mb-8 flex justify-center">
                    <div class="inline-flex items-center gap-2 px-8 py-3 border-2 border-[#7C4DFF] rounded-full">
                        <svg class="w-6 h-6 text-[#7C4DFF]" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 10.5a1.5 1.5 0 113 0v6a1.5 1.5 0 01-3 0v-6zM6 10.333v5.43a2 2 0 001.106 1.79l.05.025A4 4 0 008.943 18h5.416a2 2 0 001.962-1.608l1.2-6A2 2 0 0015.56 8H12V4a2 2 0 00-2-2 1 1 0 00-1 1v.667a4 4 0 01-.8 2.4L6.8 7.933a4 4 0 00-.8 2.4z"/>
                        </svg>
                        <span class="text-[#7C4DFF] text-lg font-medium">gracias</span>
                    </div>
                </div>

                <!-- Title -->
                <h1 class="text-gray-900 text-5xl font-bold mb-8">
                    Muchas gracias ya lo tenemos todo!
                </h1>

                <!-- Description -->
                <p class="text-gray-600 text-xl leading-relaxed mb-12 max-w-2xl mx-auto">
                    Ahora ya puedes empezar a cuidar de ti y saber aprender día a día a como te encuentras
                </p>

                <!-- Start Button -->
                <button
                    wire:click="start"
                    class="bg-[#7C4DFF] text-white font-semibold py-5 px-12 rounded-full text-xl shadow-lg hover:bg-[#6A3DE8] transition-all inline-flex items-center gap-3">
                    Empezar
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </button>
            </div>

        </div>
    </div>
</div>
