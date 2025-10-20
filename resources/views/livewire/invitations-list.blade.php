<x-app-container
    title="Listado de Invitaciones"
    subtitle="Desde aqui manejaras tus invitaciones"
    :showBackButton="true">

    <div class="space-y-8">
        <!-- Compartidos conmigo Section -->
        <div>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-[#292524]">Compartidos conmigo</h2>
                @if($sharedWithMe->count() > 3)
                    <a href="#" class="text-sm font-medium text-[#926247]">See All</a>
                @endif
            </div>

            <!-- Divider -->
            <div class="w-full h-px mb-4" style="background: linear-gradient(90deg, #C084FC 0%, transparent 100%);"></div>

            @if($sharedWithMe->count() > 0 || $pendingInvites->count() > 0)
                <div class="space-y-3">
                    <!-- Accepted Shares (with swipe to delete) -->
                    @foreach($sharedWithMe->take(3) as $access)
                        <x-swipeable-card :deleteId="$access->id" deleteEvent="confirm-delete-access">
                            <div class="flex items-center gap-3 p-4 bg-white rounded-2xl">
                                <!-- Avatar -->
                                <div class="w-14 h-14 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden flex-shrink-0">
                                    @if($access->owner->avatar)
                                        <img src="{{ $access->owner->avatar }}" alt="{{ $access->owner->name }}" class="w-full h-full object-cover">
                                    @else
                                        <span class="text-xl font-semibold text-gray-600">{{ substr($access->owner->name, 0, 1) }}</span>
                                    @endif
                                </div>

                                <!-- Info -->
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-base font-bold text-[#292524]">{{ $access->owner->name }}</h3>
                                    <p class="text-sm font-normal text-[#78716C] flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                                        </svg>
                                        {{ $access->owner->email }}
                                    </p>
                                </div>

                                <!-- Status Badge -->
                                <div class="px-4 py-2 rounded-full bg-[#f7f3ef] flex-shrink-0">
                                    <span class="text-sm font-medium text-[#926247]">Invited</span>
                                </div>
                            </div>
                        </x-swipeable-card>
                    @endforeach

                    <!-- Pending Invites -->
                    @foreach($pendingInvites as $invite)
                        <div class="flex items-center gap-3 p-4 bg-white rounded-2xl">
                            <!-- Avatar -->
                            <div class="w-14 h-14 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden flex-shrink-0">
                                @if($invite->inviter->avatar)
                                    <img src="{{ $invite->inviter->avatar }}" alt="{{ $invite->inviter->name }}" class="w-full h-full object-cover">
                                @else
                                    <span class="text-xl font-semibold text-gray-600">{{ substr($invite->inviter->name, 0, 1) }}</span>
                                @endif
                            </div>

                            <!-- Info -->
                            <div class="flex-1 min-w-0">
                                <h3 class="text-base font-bold text-[#292524]">{{ $invite->inviter->name }}</h3>
                                <p class="text-sm font-normal text-[#78716C] flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                                    </svg>
                                    {{ $invite->inviter->email }}
                                </p>
                            </div>

                            <!-- Status Badge -->
                            <div class="px-4 py-2 rounded-full bg-[#e7e5e4] flex-shrink-0">
                                <span class="text-sm font-medium text-[#78716C]">Pending</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <p class="text-sm text-[#a8a29e]">No tienes compartidos aún</p>
                </div>
            @endif
        </div>

        <!-- Compartido Section (Shared by me) -->
        <div>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-[#292524]">Compartido</h2>
                @if($mySharedAccesses->count() + $myInvites->count() > 3)
                    <a href="#" class="text-sm font-medium text-[#926247]">See All</a>
                @endif
            </div>

            <!-- Divider -->
            <div class="w-full h-px mb-4" style="background: linear-gradient(90deg, #C084FC 0%, transparent 100%);"></div>

            @if($mySharedAccesses->count() > 0 || $myInvites->count() > 0)
                <div class="space-y-3">
                    <!-- My Shared Accesses (accepted) -->
                    @foreach($mySharedAccesses as $access)
                        <x-swipeable-card :deleteId="$access->id" deleteEvent="confirm-delete-access">
                            <div class="flex items-center gap-3 p-4 bg-white rounded-2xl">
                                <!-- Avatar -->
                                <div class="w-14 h-14 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden flex-shrink-0">
                                    @if($access->sharedWith->avatar)
                                        <img src="{{ $access->sharedWith->avatar }}" alt="{{ $access->sharedWith->name }}" class="w-full h-full object-cover">
                                    @else
                                        <span class="text-xl font-semibold text-gray-600">{{ substr($access->sharedWith->name, 0, 1) }}</span>
                                    @endif
                                </div>

                                <!-- Info -->
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-base font-bold text-[#292524]">{{ $access->sharedWith->name }}</h3>
                                    <p class="text-sm font-normal text-[#78716C] flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                                        </svg>
                                        {{ $access->sharedWith->email }}
                                    </p>
                                </div>

                                <!-- Status Badge -->
                                <div class="px-4 py-2 rounded-full bg-[#f7f3ef] flex-shrink-0">
                                    <span class="text-sm font-medium text-[#926247]">Compartido</span>
                                </div>
                            </div>
                        </x-swipeable-card>
                    @endforeach

                    <!-- My Pending Invites -->
                    @foreach($myInvites->where('status', 'pending') as $invite)
                        <x-swipeable-card :deleteId="$invite->id" deleteEvent="confirm-delete-invite">
                            <div class="flex items-center gap-3 p-4 bg-white rounded-2xl">
                                <!-- Avatar -->
                                <div class="w-14 h-14 rounded-full bg-gray-200 flex items-center justify-center flex-shrink-0">
                                    <span class="text-xl font-semibold text-gray-600">{{ substr($invite->invitee_email, 0, 1) }}</span>
                                </div>

                                <!-- Info -->
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-base font-bold text-[#292524]">{{ $invite->invitee_email }}</h3>
                                    <p class="text-sm font-normal text-[#78716C] flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                                        </svg>
                                        {{ $invite->invitee_email }}
                                    </p>
                                </div>

                                <!-- Invite Button -->
                                <a href="{{ route('sharing.settings') }}"
                                   class="px-5 py-2 rounded-full bg-[#926247] text-white text-sm font-semibold flex-shrink-0 hover:bg-[#7d5239] transition-colors">
                                    Invite
                                </a>
                            </div>
                        </x-swipeable-card>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <p class="text-sm text-[#a8a29e]">No has compartido con nadie aún</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Floating Add Contact Button -->
    <div class="fixed bottom-8 left-0 right-0 px-6 z-50" style="padding-bottom: max(2rem, env(safe-area-inset-bottom, 0px) + 2rem);">
        <a href="{{ route('sharing.settings') }}"
           class="flex items-center justify-center gap-2 w-full py-4 rounded-full bg-gradient-to-r from-[#8B5CF6] to-[#A855F7] text-white font-semibold text-base shadow-lg hover:shadow-xl transition-all">
            <span>Añadir nuevo contacto</span>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
        </a>
    </div>

    <!-- Delete Confirmation Modal -->
    <x-delete-confirmation-modal
        :show="$showDeleteConfirm"
        title="Eliminar acceso?"
        message="Esta acción no se puede deshacer."
        confirmText="Eliminar"
        cancelText="Cancelar"
        onConfirm="confirmDelete"
        onCancel="cancelDelete"
    />
</x-app-container>
