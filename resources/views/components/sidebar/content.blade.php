<x-perfect-scrollbar
    as="nav"
    aria-label="main"
    class="flex flex-col flex-1 gap-4 px-3"
>

    <x-sidebar.link
        title="Dashboard"
        href="{{ route('dashboard') }}"
        :isActive="request()->routeIs('dashboard') || request()->routeIs('post_info') || request()->routeIs('view_profile') "
    >
        <x-slot name="icon">
            <x-icons.dashboard class="flex-shrink-0 w-6 h-6" aria-hidden="true" />
        </x-slot>
    </x-sidebar.link>

    <x-sidebar.link
        title="Uploads"
        href="{{ route('my_uploads') }}"
        :isActive="request()->routeIs('my_uploads') ||  request()->routeIs('uploaded_post_info')"
    >
        <x-slot name="icon">
            <x-icons.gallery class="flex-shrink-0 w-6 h-6" aria-hidden="true" />
        </x-slot>
    </x-sidebar.link>

    <x-sidebar.link
        title="Account"
        href="{{ route('account') }}"
        :isActive="request()->routeIs('account') ||  request()->routeIs('account_file')"
    >
        <x-slot name="icon">
            <x-icons.account class="flex-shrink-0 w-6 h-6" aria-hidden="true" />
        </x-slot>
    </x-sidebar.link>
    @php
        $permissions = \App\Models\Permission::where('receiver_id', Auth::id())
            ->where('permission_status', 0)
            ->with(['requester', 'post'])
            ->orderBy('created_at', 'desc')
            ->get();
    @endphp

    <x-sidebar.link
        title="Notification"
        href="{{ route('notification') }}"
        :isActive="request()->routeIs('notification')"
    >
        <x-slot name="icon">
            <div class="relative">
                <x-icons.notification class="flex-shrink-0 w-6 h-6" aria-hidden="true" />

                {{-- Red dot if pending permissions exist --}}
                @if ($permissions->isNotEmpty())
                    <span class="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full"></span>
                @endif
            </div>
        </x-slot>
    </x-sidebar.link>



    <x-sidebar.link
        title="Favorites"
        href="{{ route('favorites') }}"
        :isActive="request()->routeIs('favorites')"
    >
        <x-slot name="icon">
            <x-icons.favorites class="flex-shrink-0 w-6 h-6" aria-hidden="true" />
        </x-slot>
    </x-sidebar.link>

    {{-- <x-sidebar.dropdown
        title="Buttons"
        :active="Str::startsWith(request()->route()->uri(), 'buttons')"
    >
        <x-slot name="icon">
            <x-heroicon-o-view-grid class="flex-shrink-0 w-6 h-6" aria-hidden="true" />
        </x-slot>

        <x-sidebar.sublink
            title="Text button"
            href="{{ route('buttons.text') }}"
            :active="request()->routeIs('buttons.text')"
        />
        <x-sidebar.sublink
            title="Icon button"
            href="{{ route('buttons.icon') }}"
            :active="request()->routeIs('buttons.icon')"
        />
        <x-sidebar.sublink
            title="Text with icon"
            href="{{ route('buttons.text-icon') }}"
            :active="request()->routeIs('buttons.text-icon')"
        />
    </x-sidebar.dropdown> --}}

    {{-- <div
        x-transition
        x-show="isSidebarOpen || isSidebarHovered"
        class="text-sm text-gray-500"
    >
        Dummy Links
    </div>

    @php
        $links = array_fill(0, 20, '');
    @endphp

    @foreach ($links as $index => $link)
        <x-sidebar.link title="Dummy link {{ $index + 1 }}" href="#" />
    @endforeach --}}

</x-perfect-scrollbar>
