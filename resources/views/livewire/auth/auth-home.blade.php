<div class="fixed inset-0 w-full h-full overflow-hidden">
    <!-- Background Video Loop -->
    <video
        autoplay
        loop
        muted
        playsinline
        class="absolute inset-0 w-full h-full object-cover">
        <source src="{{ asset('videos/video_loop.mp4') }}" type="video/mp4">
    </video>

    <!-- Gradient Overlay (optional, for better text visibility) -->
    <div class="absolute inset-0 bg-gradient-to-b from-transparent via-transparent to-black/30"></div>

    <!-- Content -->
    <div class="relative z-10 flex flex-col h-full">
        <!-- Logo/Title - Centered -->
        <div class="flex-1 flex items-center justify-center">
            <h1 class="text-white text-6xl font-serif">Feelith</h1>
        </div>

        <!-- Bottom Rounded White Section -->
        <div class="relative overflow-hidden">
            <!-- Large rounded white section with wide circular curve -->
            <div class="relative w-[200%] -left-[50%]">
                <div class="bg-white rounded-t-[50%] pt-16 pb-8 px-6">
                    <div class="w-[50%] mx-auto">

                        <!-- Title -->
                        <h2 class="text-center text-gray-900 text-xl font-medium mb-8">Haz login con:</h2>

                        <!-- Sign In with Google Button -->
                        <a href="{{ route('auth.google') }}"
                           class="flex items-center justify-center w-full bg-black hover:bg-gray-900 text-white font-medium py-4 px-6 rounded-full mb-4 transition-all duration-200 shadow-lg">
                            <svg class="w-6 h-6 mr-3" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path fill="#EA4335" d="M5.26620003,9.76452941 C6.19878754,6.93863203 8.85444915,4.90909091 12,4.90909091 C13.6909091,4.90909091 15.2181818,5.50909091 16.4181818,6.49090909 L19.9090909,3 C17.7818182,1.14545455 15.0545455,0 12,0 C7.27006974,0 3.1977497,2.69829785 1.23999023,6.65002441 L5.26620003,9.76452941 Z"/>
                                <path fill="#34A853" d="M16.0407269,18.0125889 C14.9509167,18.7163016 13.5660892,19.0909091 12,19.0909091 C8.86648613,19.0909091 6.21911939,17.076871 5.27698177,14.2678769 L1.23746264,17.3349879 C3.19279051,21.2936293 7.26500293,24 12,24 C14.9328362,24 17.7353462,22.9573905 19.834192,20.9995801 L16.0407269,18.0125889 Z"/>
                                <path fill="#4A90E2" d="M19.834192,20.9995801 C22.0291676,18.9520994 23.4545455,15.903663 23.4545455,12 C23.4545455,11.2909091 23.3454545,10.5818182 23.1818182,9.90909091 L12,9.90909091 L12,14.4545455 L18.4363636,14.4545455 C18.1187732,16.013626 17.2662994,17.2212117 16.0407269,18.0125889 L19.834192,20.9995801 Z"/>
                                <path fill="#FBBC05" d="M5.27698177,14.2678769 C5.03832634,13.556323 4.90909091,12.7937589 4.90909091,12 C4.90909091,11.2182781 5.03443647,10.4668121 5.26620003,9.76452941 L1.23999023,6.65002441 C0.43658717,8.26043162 0,10.0753848 0,12 C0,13.9195484 0.444780743,15.7301709 1.23746264,17.3349879 L5.27698177,14.2678769 Z"/>
                            </svg>
                            Sign In With Google
                        </a>

                        <!-- Sign In with Email Button -->
                        <a href="{{ route('sign-in-mail') }}"
                           class="flex items-center justify-center w-full bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white font-medium py-4 px-6 rounded-full mb-8 transition-all duration-200 shadow-lg">
                            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            Sign In With Email
                        </a>

                        <!-- Sign Up Link -->
                        <p class="text-center text-gray-700 text-base mb-6">
                            Don't have an account?
                            <a href="{{ route('sign-in-mail') }}" class="text-cyan-500 hover:text-cyan-600 font-semibold">
                                Sign Up
                            </a>
                        </p>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
