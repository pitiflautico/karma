<div class="relative min-h-screen w-full overflow-hidden">
    <!-- Background Video -->
    <video
        autoplay
        loop
        muted
        playsinline
        class="absolute inset-0 w-full h-full object-cover"
    >
        <source src="{{ asset('videos/background-login.mp4') }}" type="video/mp4">
    </video>

    <!-- Overlay gradient for better readability -->
    <div class="absolute inset-0 bg-gradient-to-b from-purple-900/20 via-transparent to-white/40"></div>

    <!-- Content -->
    <div class="relative z-10 flex flex-col items-center justify-between min-h-screen px-6 py-12">
        <!-- Logo Section -->
        <div class="flex-1 flex items-center justify-center">
            <h1 class="text-5xl md:text-6xl font-serif text-white tracking-wide">
                Kharma<sup class="text-2xl">®</sup>
            </h1>
        </div>

        <!-- Login Section -->
        <div class="w-full max-w-md mb-8">
            <!-- Login prompt -->
            <p class="text-center text-white text-lg mb-6 font-light">
                Haz login con:
            </p>

            <!-- Sign In with Google Button (Black) -->
            <a href="{{ route('auth.google') }}"
               class="flex items-center justify-center w-full bg-black hover:bg-gray-900 text-white font-medium py-4 px-6 rounded-full mb-4 transition-all duration-200 shadow-lg hover:shadow-xl">
                <svg class="w-6 h-6 mr-3" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path fill="#EA4335" d="M5.26620003,9.76452941 C6.19878754,6.93863203 8.85444915,4.90909091 12,4.90909091 C13.6909091,4.90909091 15.2181818,5.50909091 16.4181818,6.49090909 L19.9090909,3 C17.7818182,1.14545455 15.0545455,0 12,0 C7.27006974,0 3.1977497,2.69829785 1.23999023,6.65002441 L5.26620003,9.76452941 Z"/>
                    <path fill="#34A853" d="M16.0407269,18.0125889 C14.9509167,18.7163016 13.5660892,19.0909091 12,19.0909091 C8.86648613,19.0909091 6.21911939,17.076871 5.27698177,14.2678769 L1.23746264,17.3349879 C3.19279051,21.2936293 7.26500293,24 12,24 C14.9328362,24 17.7353462,22.9573905 19.834192,20.9995801 L16.0407269,18.0125889 Z"/>
                    <path fill="#4A90E2" d="M19.834192,20.9995801 C22.0291676,18.9520994 23.4545455,15.903663 23.4545455,12 C23.4545455,11.2909091 23.3454545,10.5818182 23.1818182,9.90909091 L12,9.90909091 L12,14.4545455 L18.4363636,14.4545455 C18.1187732,16.013626 17.2662994,17.2212117 16.0407269,18.0125889 L19.834192,20.9995801 Z"/>
                    <path fill="#FBBC05" d="M5.27698177,14.2678769 C5.03832634,13.556323 4.90909091,12.7937589 4.90909091,12 C4.90909091,11.2182781 5.03443647,10.4668121 5.26620003,9.76452941 L1.23999023,6.65002441 C0.43658717,8.26043162 0,10.0753848 0,12 C0,13.9195484 0.444780743,15.7301709 1.23746264,17.3349879 L5.27698177,14.2678769 Z"/>
                </svg>
                Sign In With Google
            </a>

            <!-- Sign In with Email Button (Purple) -->
            <button
                wire:click="$dispatch('openEmailLoginModal')"
                class="flex items-center justify-center w-full bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white font-medium py-4 px-6 rounded-full transition-all duration-200 shadow-lg hover:shadow-xl">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                Sign In With Email
            </button>
        </div>

        <!-- Bottom Rounded Section -->
        <div class="w-full">
            <div class="bg-white/95 backdrop-blur-sm rounded-t-[3rem] py-8 px-6 text-center shadow-2xl">
                <p class="text-gray-700 text-sm">
                    Don't have an account?
                    <button
                        wire:click="$dispatch('openSignUpModal')"
                        class="text-cyan-500 hover:text-cyan-600 font-semibold underline transition-colors">
                        Sign Up
                    </button>
                </p>
            </div>
        </div>
    </div>

    <!-- Email Login Modal -->
    <div x-data="{ showEmailLogin: false }"
         x-on:open-email-login-modal.window="showEmailLogin = true"
         x-show="showEmailLogin"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="showEmailLogin = false"></div>

            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-8">
                <button @click="showEmailLogin = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                <h2 class="text-2xl font-bold text-gray-900 mb-6">Sign In</h2>

                <form wire:submit="login">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input
                            type="email"
                            wire:model="email"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                            placeholder="tu@email.com"
                            required>
                        @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                        <input
                            type="password"
                            wire:model="password"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                            placeholder="••••••••"
                            required>
                        @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    @if (session()->has('error'))
                        <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded-lg text-sm">
                            {{ session('error') }}
                        </div>
                    @endif

                    <button
                        type="submit"
                        class="w-full bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white font-medium py-3 rounded-lg transition-all duration-200">
                        Sign In
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Sign Up Modal -->
    <div x-data="{ showSignUp: false }"
         x-on:open-sign-up-modal.window="showSignUp = true"
         x-show="showSignUp"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="showSignUp = false"></div>

            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-8">
                <button @click="showSignUp = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                <h2 class="text-2xl font-bold text-gray-900 mb-6">Create Account</h2>

                <form wire:submit="register">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                        <input
                            type="text"
                            wire:model="name"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                            placeholder="Tu nombre"
                            required>
                        @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input
                            type="email"
                            wire:model="registerEmail"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                            placeholder="tu@email.com"
                            required>
                        @error('registerEmail') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                        <input
                            type="password"
                            wire:model="registerPassword"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                            placeholder="••••••••"
                            required>
                        @error('registerPassword') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    @if (session()->has('success'))
                        <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded-lg text-sm">
                            {{ session('success') }}
                        </div>
                    @endif

                    <button
                        type="submit"
                        class="w-full bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white font-medium py-3 rounded-lg transition-all duration-200">
                        Create Account
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
