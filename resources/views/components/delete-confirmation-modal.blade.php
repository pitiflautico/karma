@props([
    'show' => false,
    'title' => 'Delete Mood Entry?',
    'message' => 'This action cannot be undone.',
    'confirmText' => 'Yes ✓',
    'cancelText' => 'Cancel',
    'onConfirm' => '',
    'onCancel' => ''
])

@if($show)
    <div class="fixed inset-0 z-50 flex items-end justify-center" x-data="{ show: true }" x-show="show" x-transition>
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black bg-opacity-50" wire:click="{{ $onCancel }}"></div>

        <!-- Modal -->
        <div class="relative bg-white rounded-t-3xl w-full max-w-lg p-6 pb-8">
            <div class="text-center mb-6">
                <!-- Illustration -->
                <div class="flex items-center justify-center mx-auto mb-6">
                    <img src="{{ asset('images/delete_popup_art.png') }}"
                         alt="Delete confirmation"
                         class="w-48 h-auto">
                </div>

                <!-- Title -->
                <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $title }}</h3>

                <!-- Message -->
                <p class="text-sm text-gray-600">{{ $message }}</p>
            </div>

            <!-- Buttons -->
            <div class="space-y-3">
                <!-- Confirm Button (Pink) -->
                <button
                    wire:click="{{ $onConfirm }}"
                    class="w-full py-4 bg-gradient-to-r from-pink-400 to-pink-500 hover:from-pink-500 hover:to-pink-600 text-white font-semibold rounded-full transition-all shadow-sm"
                >
                    {{ $confirmText }}
                </button>

                <!-- Cancel Button (White with Red Text) -->
                <button
                    wire:click="{{ $onCancel }}"
                    class="w-full py-4 bg-white border-2 border-gray-200 hover:bg-gray-50 text-red-500 font-semibold rounded-full transition-all"
                >
                    {{ $cancelText }}
                </button>
            </div>
        </div>
    </div>
@endif
