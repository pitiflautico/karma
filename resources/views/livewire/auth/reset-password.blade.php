<div>
    <x-auth-mobile-container :showBackButton="true" backUrl="{{ route('sign-in-mail') }}">
        <x-auth-card
            title="Reset Password"
            description="Please enter your new password.">

            <!-- Flash Notification for Errors -->
            @if (session()->has('error'))
                <x-flash-notification
                    type="error"
                    message="ERROR: {{ session('error') }}"
                    :autoHide="false"
                />
            @endif

            <!-- Email Input (Read-only) -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                <input
                    type="email"
                    wire:model="email"
                    readonly
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50 text-gray-600 cursor-not-allowed"
                />
            </div>

            <!-- New Password Input -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                <input
                    type="password"
                    wire:model="password"
                    placeholder="Enter your new password..."
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                />
            </div>

            <!-- Confirm Password Input -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                <input
                    type="password"
                    wire:model="passwordConfirmation"
                    placeholder="Confirm your new password..."
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                />
            </div>

            <!-- Reset Password Button -->
            <button
                wire:click="resetPassword"
                class="w-full bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white font-medium py-4 px-6 rounded-full mb-6 transition-all duration-200 shadow-lg flex items-center justify-center">
                Reset Password
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </button>

            <!-- Footer -->
            <x-slot:footer>
                <p class="text-center text-gray-600 text-sm">
                    Need help?<br>
                    Contact us at <a href="mailto:hello@feelith.com" class="text-purple-600 hover:text-purple-700 font-medium">hello@feelith.com</a>
                </p>
            </x-slot:footer>
        </x-auth-card>
    </x-auth-mobile-container>
</div>
