<div>
    <x-auth-mobile-container :showBackButton="true" backUrl="{{ route('sign-in-mail') }}">
        <x-auth-card
            title="Password Reset Sent"
            description="We've sent a password recovery link to your email address. Please check your inbox.">

            <!-- Success Icon -->
            <div class="flex justify-center mb-6">
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>

            <!-- Open Email Button -->
            <button
                onclick="window.open('mailto:', '_blank')"
                class="w-full bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white font-medium py-4 px-6 rounded-full mb-6 transition-all duration-200 shadow-lg flex items-center justify-center">
                Open My Email
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </button>

            <!-- Footer -->
            <x-slot:footer>
                <p class="text-center text-gray-600 text-sm">
                    Didn't receive the email?<br>
                    Contact us at <a href="mailto:hello@feelith.com" class="text-purple-600 hover:text-purple-700 font-medium">hello@feelith.com</a>
                </p>
            </x-slot:footer>
        </x-auth-card>
    </x-auth-mobile-container>
</div>
