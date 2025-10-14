<div>
    <x-auth-mobile-container :showBackButton="true" backUrl="{{ route('sign-in-mail') }}">
        <x-auth-card
            title="Forgot Password"
            description="Please enter your email address to reset your password.">

            <!-- Flash Notification for Errors -->
            @if (session()->has('error'))
                <x-flash-notification
                    type="error"
                    message="ERROR: {{ session('error') }}"
                    :autoHide="false"
                />
            @endif

            <!-- Email Input -->
            <div class="mb-6">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <input
                        type="email"
                        wire:model="email"
                        placeholder="Enter your email address..."
                        class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                    />
                </div>
            </div>

            <!-- Send Password Button -->
            <button
                wire:click="sendResetLink"
                class="w-full bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white font-medium py-4 px-6 rounded-full mb-6 transition-all duration-200 shadow-lg flex items-center justify-center">
                Send Password
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </button>

            <!-- Footer -->
            <x-slot:footer>
                <p class="text-center text-gray-600 text-sm">
                    Don't remember your email?<br>
                    Contact us at <a href="mailto:hello@feelith.com" class="text-purple-600 hover:text-purple-700 font-medium">hello@feelith.com</a>
                </p>
            </x-slot:footer>
        </x-auth-card>
    </x-auth-mobile-container>
</div>
