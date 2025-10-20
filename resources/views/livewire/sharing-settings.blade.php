<div class="min-h-screen bg-[#f7f3ef]" style="font-family: 'Urbanist', sans-serif;">
    <!-- Mobile: Solo botón back (igual que en Figma) -->
    <div class="md:hidden px-4 pt-4 pb-3 bg-[#f7f3ef]">
        <button onclick="window.history.back()" class="text-[#292524] -ml-2">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path>
            </svg>
        </button>
    </div>

    <!-- Desktop: Header completo -->
    <div class="hidden md:flex items-center justify-between px-6 py-4 bg-[#f7f3ef] border-b border-gray-200">
        <button onclick="window.history.back()" class="text-[#292524]">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </button>
        <h1 class="text-xl font-semibold text-[#292524]">Compartir tus estados</h1>
        <button class="text-[#292524]">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                <circle cx="12" cy="5" r="2"></circle>
                <circle cx="12" cy="12" r="2"></circle>
                <circle cx="12" cy="19" r="2"></circle>
            </svg>
        </button>
    </div>

    <div class="px-4 pb-6 md:py-6 md:max-w-2xl md:mx-auto">
        <!-- Success/Error Messages -->
        @if (session()->has('success'))
            <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-2xl">
                {{ session('success') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-2xl">
                {{ session('error') }}
            </div>
        @endif

        <!-- Form -->
        <form wire:submit.prevent="sendInvite">
            <!-- Section: Invitar a alguien -->
            <div class="mb-6">
                <h2 class="text-[22px] font-bold text-[#292524] mb-2">Invitar a alguien a ver tus datos</h2>
                <p class="text-[14px] text-[#57534e] mb-4">Comparte tu mood y tus estados con alguien con quien confies</p>

                <!-- Email Input -->
                <div class="mb-6">
                    <label class="block text-[14px] font-semibold text-[#292524] mb-2">Label</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-[#57534e]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <input
                            type="email"
                            wire:model="recipientEmail"
                            placeholder="friend@example.com"
                            class="w-full pl-12 pr-12 py-3 rounded-full border border-gray-300 bg-white text-[16px] text-[#57534e] focus:border-[#9bb167] focus:ring-2 focus:ring-[#9bb167] focus:ring-opacity-20 transition"
                        >
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center">
                            <svg class="w-5 h-5 text-[#a8a29e]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    @error('recipientEmail')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Section: Datos a compartir -->
            <div class="mb-6">
                <h2 class="text-[16px] font-bold text-[#292524] mb-4">Datos a compartir</h2>

                <div class="bg-white rounded-3xl overflow-hidden shadow-sm">
                    <!-- Basico -->
                    <label class="flex items-center p-4 cursor-pointer hover:bg-gray-50 transition">
                        <div class="flex items-center flex-1">
                            <div class="w-12 h-12 bg-[#fafaf9] rounded-2xl flex items-center justify-center mr-4">
                                <svg class="w-6 h-6 text-[#57534e]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-[16px] font-semibold text-[#292524]">Basico</p>
                                <p class="text-[14px] text-[#57534e]">Puede ver mis moods</p>
                            </div>
                        </div>
                        <div class="relative">
                            <input
                                type="radio"
                                wire:model="permissionLevel"
                                value="basico"
                                class="w-5 h-5 border-2 border-gray-300 text-[#9bb167] focus:ring-[#9bb167] focus:ring-2 cursor-pointer"
                            >
                        </div>
                    </label>

                    <div class="border-t border-gray-100"></div>

                    <!-- Intermedio -->
                    <label class="flex items-center p-4 cursor-pointer hover:bg-gray-50 transition">
                        <div class="flex items-center flex-1">
                            <div class="w-12 h-12 bg-[#fafaf9] rounded-2xl flex items-center justify-center mr-4">
                                <svg class="w-6 h-6 text-[#57534e]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-[16px] font-semibold text-[#292524]">Intermedio</p>
                                <p class="text-[14px] text-[#57534e]">Puede ver Mis mood y mis notas</p>
                            </div>
                        </div>
                        <div class="relative">
                            <input
                                type="radio"
                                wire:model="permissionLevel"
                                value="intermedio"
                                class="w-5 h-5 border-2 border-gray-300 text-[#9bb167] focus:ring-[#9bb167] focus:ring-2 cursor-pointer"
                            >
                        </div>
                    </label>

                    <div class="border-t border-gray-100"></div>

                    <!-- Avanzado -->
                    <label class="flex items-center p-4 cursor-pointer hover:bg-gray-50 transition">
                        <div class="flex items-center flex-1">
                            <div class="w-12 h-12 bg-[#fafaf9] rounded-2xl flex items-center justify-center mr-4">
                                <svg class="w-6 h-6 text-[#57534e]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-[16px] font-semibold text-[#292524]">Avanzado</p>
                                <p class="text-[14px] text-[#57534e]">Puede ver mis mood e informes</p>
                            </div>
                        </div>
                        <div class="relative">
                            <input
                                type="radio"
                                wire:model="permissionLevel"
                                value="avanzado"
                                class="w-5 h-5 border-2 border-gray-300 text-[#9bb167] focus:ring-[#9bb167] focus:ring-2 cursor-pointer"
                            >
                        </div>
                    </label>
                </div>
            </div>

            <!-- Submit Button -->
            <button
                type="submit"
                class="w-full bg-[#8b6f47] hover:bg-[#75603d] text-white text-[18px] font-semibold py-4 px-6 rounded-full transition flex items-center justify-center shadow-lg"
            >
                Enviar Invitacion
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                </svg>
            </button>
        </form>

        <!-- Pending Invitations -->
        @if($pendingInvites->count() > 0)
            <div class="mt-8">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-[18px] font-bold text-[#292524]">Invitaciones Pendientes</h3>
                    <span class="text-[14px] text-[#57534e]">{{ $pendingInvites->count() }}</span>
                </div>
                <div class="space-y-2">
                    @foreach($pendingInvites as $invite)
                        <div class="bg-white rounded-2xl p-4 flex items-center justify-between shadow-sm">
                            <div class="flex items-center flex-1">
                                <!-- Avatar placeholder -->
                                <div class="w-12 h-12 bg-gradient-to-br from-gray-200 to-gray-300 rounded-full flex items-center justify-center mr-3">
                                    <span class="text-gray-600 text-lg font-semibold">{{ substr($invite->recipient_email, 0, 1) }}</span>
                                </div>

                                <div class="flex-1">
                                    <p class="text-[16px] font-semibold text-[#292524]">{{ explode('@', $invite->recipient_email)[0] }}</p>
                                    <div class="flex items-center gap-1">
                                        <svg class="w-4 h-4 text-[#a8a29e]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                        <p class="text-[14px] text-[#57534e]">{{ $invite->recipient_email }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Pending Badge with Cancel Button -->
                            <button
                                wire:click="cancelInvite({{ $invite->id }})"
                                wire:confirm="¿Seguro que quieres cancelar esta invitación?"
                                class="bg-[#f5f5f4] hover:bg-red-100 px-3 py-1.5 rounded-full flex items-center gap-1.5 transition group"
                            >
                                <svg class="w-3.5 h-3.5 text-[#d6d3d1] group-hover:text-red-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm5 11H7v-2h10v2z"></path>
                                </svg>
                                <span class="text-[12px] font-semibold text-[#d6d3d1] group-hover:text-red-600">Pending</span>
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Active Sharing -->
        @if($sharedAccesses->count() > 0)
            <div class="mt-8">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-[18px] font-bold text-[#292524]">Compartidos conmigo</h3>
                    <span class="text-[14px] text-[#57534e]">{{ $sharedAccesses->count() }}</span>
                </div>
                <div class="space-y-2">
                    @foreach($sharedAccesses as $access)
                        <div class="bg-white rounded-2xl p-4 flex items-center justify-between shadow-sm">
                            <div class="flex items-center flex-1">
                                <!-- Avatar -->
                                <div class="w-12 h-12 bg-gradient-to-br from-[#9bb167] to-[#85965b] rounded-full flex items-center justify-center mr-3">
                                    <span class="text-white font-semibold text-lg">{{ substr($access->sharedWithUser->name, 0, 1) }}</span>
                                </div>

                                <div class="flex-1">
                                    <p class="text-[16px] font-semibold text-[#292524]">{{ $access->sharedWithUser->name }}</p>
                                    <div class="flex items-center gap-1">
                                        <svg class="w-4 h-4 text-[#a8a29e]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                        <p class="text-[14px] text-[#57534e]">{{ $access->sharedWithUser->email }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Revoke Button (X icon) -->
                            <button
                                wire:click="revokeAccess('{{ $access->id }}')"
                                wire:confirm="¿Seguro que quieres revocar el acceso a {{ $access->sharedWithUser->name }}?"
                                class="w-10 h-10 bg-[#ef4444] hover:bg-[#dc2626] rounded-full flex items-center justify-center transition group"
                            >
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
