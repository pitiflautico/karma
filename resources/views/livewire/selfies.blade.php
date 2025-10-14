<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900">My Selfies</h2>
            <p class="text-gray-600 mt-1">View all your captured selfies</p>
        </div>

        <!-- Flash Messages -->
        @if (session()->has('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Selfies Grid -->
        @if($selfies->count() > 0)
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($selfies as $selfie)
                    <div class="relative group" x-data="{ showHeatmap: false }">
                        <!-- Image -->
                        <div class="aspect-square bg-gray-100 rounded-lg overflow-hidden relative">
                            <!-- Original Photo -->
                            <img
                                x-show="!showHeatmap"
                                src="{{ Storage::disk('public')->url($selfie->selfie_photo_path) }}"
                                alt="Selfie from {{ $selfie->selfie_taken_at->format('M d, Y') }}"
                                class="w-full h-full object-cover absolute inset-0"
                            >

                            <!-- Heatmap Photo -->
                            @if($selfie->selfie_heatmap_path)
                                <img
                                    x-show="showHeatmap"
                                    src="{{ Storage::disk('public')->url($selfie->selfie_heatmap_path) }}"
                                    alt="Heatmap from {{ $selfie->selfie_taken_at->format('M d, Y') }}"
                                    class="w-full h-full object-cover absolute inset-0"
                                >
                            @endif
                        </div>

                        <!-- Overlay with info -->
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-60 transition-all duration-200 rounded-lg flex flex-col justify-between p-3">
                            <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                <!-- Heatmap Toggle -->
                                @if($selfie->selfie_heatmap_path)
                                    <button
                                        @click="showHeatmap = !showHeatmap"
                                        class="px-3 py-1 bg-purple-600 hover:bg-purple-700 text-white text-xs rounded transition-colors flex items-center gap-1"
                                    >
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                        </svg>
                                        <span x-text="showHeatmap ? 'Original' : 'Heatmap'"></span>
                                    </button>
                                @endif
                            </div>

                            <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-200 w-full">
                                <p class="text-white text-sm font-medium">
                                    {{ $selfie->selfie_taken_at->format('M d, Y') }}
                                </p>
                                <p class="text-white text-xs opacity-75 mb-2">
                                    {{ $selfie->selfie_taken_at->format('h:i A') }}
                                </p>

                                <!-- Delete button -->
                                <button
                                    wire:click="deleteSelfie({{ $selfie->id }})"
                                    wire:confirm="Are you sure you want to delete this selfie?"
                                    class="w-full bg-red-500 hover:bg-red-600 text-white text-sm py-1 px-3 rounded transition-colors"
                                >
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $selfies->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No selfies yet</h3>
                <p class="mt-1 text-sm text-gray-500">Start capturing selfies from your mobile app!</p>
            </div>
        @endif
    </div>
</div>
