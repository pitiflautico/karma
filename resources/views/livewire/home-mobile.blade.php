<div>
<!-- Flash Notifications -->
@if (session()->has('error'))
    <x-flash-notification
        type="error"
        message="ERROR: {{ session('error') }}"
        :autoHide="false"
    />
@endif

@if (session()->has('success'))
    <x-flash-notification
        type="success"
        message="{{ session('success') }}"
        :autoHide="true"
        :autoHideDelay="3000"
    />
@endif

<div class="relative min-h-screen w-full overflow-hidden">
    <!-- Background Gradient -->
    <div class="absolute inset-0 bg-gradient-to-br from-blue-400 via-purple-400 to-blue-600"></div>

    <!-- Radial gradient overlay for the glow effect -->
    <div class="absolute inset-0" style="background: radial-gradient(circle at center top, rgba(255, 192, 203, 0.6) 0%, rgba(138, 196, 255, 0.4) 30%, transparent 60%);"></div>

    <!-- Content -->
    <div class="relative z-10 flex flex-col min-h-screen">
        <!-- Logo/Title -->
        <div class="flex-1 flex items-center justify-center">
            <h1 class="text-white text-5xl font-serif">Kharma®</h1>
        </div>

        <!-- Bottom Rounded White Section -->
        <div class="relative overflow-hidden">
            <!-- Large rounded white section with wide circular curve -->
            <div class="relative w-[200%] -left-[50%]">
                <div class="bg-white rounded-t-[50%] pt-16 pb-8 px-6">
                    <div class="w-[50%] mx-auto">
                        @if(!$showSignUp)
                        <!-- LOGIN FORM -->
                        <!-- Email Address -->
                        <div class="mb-6">
                            <label class="block text-gray-900 text-base font-medium mb-3">Email Address</label>
                            <div class="relative">
                                <div class="absolute left-5 top-1/2 -translate-y-1/2 text-gray-400">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <input
                                    type="email"
                                    wire:model="email"
                                    class="w-full pl-14 pr-4 py-4 border border-gray-200 rounded-full focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-gray-50 text-gray-900 placeholder-gray-400"
                                    placeholder="Enter your email address...">
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="mb-4">
                            <label class="block text-gray-900 text-base font-medium mb-3">Password</label>
                            <div class="relative" x-data="{ showPassword: false }">
                                <div class="absolute left-5 top-1/2 -translate-y-1/2 text-gray-400">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </div>
                                <input
                                    :type="showPassword ? 'text' : 'password'"
                                    wire:model="password"
                                    class="w-full pl-14 pr-14 py-4 border border-gray-200 rounded-full focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-gray-50 text-gray-900 placeholder-gray-400"
                                    placeholder="Enter your password...">
                                <button
                                    type="button"
                                    @click="showPassword = !showPassword"
                                    class="absolute right-5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <svg x-show="!showPassword" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <svg x-show="showPassword" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Keep me signed in & Forgot Password -->
                        <div class="flex items-center justify-between mb-8">
                            <label class="flex items-center">
                                <input type="checkbox" class="w-5 h-5 rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                <span class="ml-2 text-gray-900 text-base">Keep me signed in</span>
                            </label>
                            <a href="#" class="text-purple-600 text-base font-medium hover:text-purple-700">Forgot Password</a>
                        </div>

                        <!-- Sign In Button -->
                        <button
                            wire:click="login"
                            class="w-full bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white font-medium py-4 px-6 rounded-full mb-6 transition-all duration-200 shadow-lg flex items-center justify-center gap-3">
                            <span>Sign In</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </button>

                        <!-- Divider -->
                        <div class="flex items-center my-6">
                            <div class="flex-1 border-t border-gray-200"></div>
                            <span class="px-4 text-gray-500 text-sm font-medium">OR</span>
                            <div class="flex-1 border-t border-gray-200"></div>
                        </div>

                        <!-- Sign In with Google Button -->
                        <a href="{{ route('auth.google') }}"
                           class="flex items-center justify-center w-full bg-black hover:bg-gray-900 text-white font-medium py-4 px-6 rounded-full mb-8 transition-all duration-200 shadow-lg">
                            <svg class="w-6 h-6 mr-3" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path fill="#EA4335" d="M5.26620003,9.76452941 C6.19878754,6.93863203 8.85444915,4.90909091 12,4.90909091 C13.6909091,4.90909091 15.2181818,5.50909091 16.4181818,6.49090909 L19.9090909,3 C17.7818182,1.14545455 15.0545455,0 12,0 C7.27006974,0 3.1977497,2.69829785 1.23999023,6.65002441 L5.26620003,9.76452941 Z"/>
                                <path fill="#34A853" d="M16.0407269,18.0125889 C14.9509167,18.7163016 13.5660892,19.0909091 12,19.0909091 C8.86648613,19.0909091 6.21911939,17.076871 5.27698177,14.2678769 L1.23746264,17.3349879 C3.19279051,21.2936293 7.26500293,24 12,24 C14.9328362,24 17.7353462,22.9573905 19.834192,20.9995801 L16.0407269,18.0125889 Z"/>
                                <path fill="#4A90E2" d="M19.834192,20.9995801 C22.0291676,18.9520994 23.4545455,15.903663 23.4545455,12 C23.4545455,11.2909091 23.3454545,10.5818182 23.1818182,9.90909091 L12,9.90909091 L12,14.4545455 L18.4363636,14.4545455 C18.1187732,16.013626 17.2662994,17.2212117 16.0407269,18.0125889 L19.834192,20.9995801 Z"/>
                                <path fill="#FBBC05" d="M5.27698177,14.2678769 C5.03832634,13.556323 4.90909091,12.7937589 4.90909091,12 C4.90909091,11.2182781 5.03443647,10.4668121 5.26620003,9.76452941 L1.23999023,6.65002441 C0.43658717,8.26043162 0,10.0753848 0,12 C0,13.9195484 0.444780743,15.7301709 1.23746264,17.3349879 L5.27698177,14.2678769 Z"/>
                            </svg>
                            Sign In With Google
                        </a>

                        <!-- Sign Up Link -->
                        <p class="text-center text-gray-700 text-base mb-6">
                            Don't have an account?
                            <button
                                wire:click="showSignUp"
                                class="text-purple-600 hover:text-purple-700 font-semibold">
                                Sign Up
                            </button>
                        </p>

                        @else
                        <!-- SIGN UP FORM -->
                        <!-- Email Address -->
                        <div class="mb-6">
                            <label class="block text-gray-900 text-base font-medium mb-3">Email Address</label>
                            <div class="relative">
                                <div class="absolute left-5 top-1/2 -translate-y-1/2 text-gray-400">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <input
                                    type="email"
                                    wire:model="registerEmail"
                                    class="w-full pl-14 pr-4 py-4 border border-gray-200 rounded-full focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-gray-50 text-gray-900 placeholder-gray-400"
                                    placeholder="Enter your email address...">
                                @error('registerEmail') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="mb-4">
                            <label class="block text-gray-900 text-base font-medium mb-3">Password</label>
                            <div class="relative" x-data="{ showPassword: false }">
                                <div class="absolute left-5 top-1/2 -translate-y-1/2 text-gray-400">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </div>
                                <input
                                    :type="showPassword ? 'text' : 'password'"
                                    wire:model.live="registerPassword"
                                    class="w-full pl-14 pr-14 py-4 border border-gray-200 rounded-full focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-gray-50 text-gray-900 placeholder-gray-400"
                                    placeholder="Enter your password...">
                                <button
                                    type="button"
                                    @click="showPassword = !showPassword"
                                    class="absolute right-5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <svg x-show="!showPassword" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <svg x-show="showPassword" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                    </svg>
                                </button>
                            </div>
                            @error('registerPassword') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror

                            <!-- Password Strength Indicator -->
                            <x-password-strength :strength="$passwordStrength" />
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-6">
                            <label class="block text-gray-900 text-base font-medium mb-3">Confirm Password</label>
                            <div class="relative" x-data="{ showPassword: false }">
                                <div class="absolute left-5 top-1/2 -translate-y-1/2 text-gray-400">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </div>
                                <input
                                    :type="showPassword ? 'text' : 'password'"
                                    wire:model="registerPasswordConfirmation"
                                    class="w-full pl-14 pr-14 py-4 border border-gray-200 rounded-full focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-gray-50 text-gray-900 placeholder-gray-400"
                                    placeholder="Confirm your password...">
                                <button
                                    type="button"
                                    @click="showPassword = !showPassword"
                                    class="absolute right-5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <svg x-show="!showPassword" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <svg x-show="showPassword" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                    </svg>
                                </button>
                            </div>
                            @error('registerPasswordConfirmation') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Sign Up Button -->
                        <button
                            wire:click="register"
                            class="w-full bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white font-medium py-4 px-6 rounded-full mb-6 transition-all duration-200 shadow-lg flex items-center justify-center gap-3">
                            <span>Sign Up</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </button>

                        <!-- Divider -->
                        <div class="flex items-center my-6">
                            <div class="flex-1 border-t border-gray-200"></div>
                            <span class="px-4 text-gray-500 text-sm font-medium">OR</span>
                            <div class="flex-1 border-t border-gray-200"></div>
                        </div>

                        <!-- Sign In with Google Button -->
                        <a href="{{ route('auth.google') }}"
                           class="flex items-center justify-center w-full bg-black hover:bg-gray-900 text-white font-medium py-4 px-6 rounded-full mb-8 transition-all duration-200 shadow-lg">
                            <svg class="w-6 h-6 mr-3" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path fill="#EA4335" d="M5.26620003,9.76452941 C6.19878754,6.93863203 8.85444915,4.90909091 12,4.90909091 C13.6909091,4.90909091 15.2181818,5.50909091 16.4181818,6.49090909 L19.9090909,3 C17.7818182,1.14545455 15.0545455,0 12,0 C7.27006974,0 3.1977497,2.69829785 1.23999023,6.65002441 L5.26620003,9.76452941 Z"/>
                                <path fill="#34A853" d="M16.0407269,18.0125889 C14.9509167,18.7163016 13.5660892,19.0909091 12,19.0909091 C8.86648613,19.0909091 6.21911939,17.076871 5.27698177,14.2678769 L1.23746264,17.3349879 C3.19279051,21.2936293 7.26500293,24 12,24 C14.9328362,24 17.7353462,22.9573905 19.834192,20.9995801 L16.0407269,18.0125889 Z"/>
                                <path fill="#4A90E2" d="M19.834192,20.9995801 C22.0291676,18.9520994 23.4545455,15.903663 23.4545455,12 C23.4545455,11.2909091 23.3454545,10.5818182 23.1818182,9.90909091 L12,9.90909091 L12,14.4545455 L18.4363636,14.4545455 C18.1187732,16.013626 17.2662994,17.2212117 16.0407269,18.0125889 L19.834192,20.9995801 Z"/>
                                <path fill="#FBBC05" d="M5.27698177,14.2678769 C5.03832634,13.556323 4.90909091,12.7937589 4.90909091,12 C4.90909091,11.2182781 5.03443647,10.4668121 5.26620003,9.76452941 L1.23999023,6.65002441 C0.43658717,8.26043162 0,10.0753848 0,12 C0,13.9195484 0.444780743,15.7301709 1.23746264,17.3349879 L5.27698177,14.2678769 Z"/>
                            </svg>
                            Sign In With Google
                        </a>

                        <!-- Login Link -->
                        <p class="text-center text-gray-700 text-base mb-6">
                            I already have
                            <button
                                wire:click="showLogin"
                                class="text-purple-600 hover:text-purple-700 font-semibold">
                                an account.
                            </button>
                        </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<style>
    [x-cloak] { display: none !important; }
</style>
</div>
