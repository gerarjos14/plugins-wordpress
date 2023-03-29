<aside class="main-sidebar {{ config('adminlte.classes_sidebar', 'sidebar-dark-primary elevation-4') }}">
    @php
        $user_client_beon = false;
        $id               = auth()->user()->parent_id;
        $company          = App\Models\User::find($id);
        
        if ($company && ($company->name == 'Lars BEON')) {
            $user_client_beon = true;
        }
    @endphp
    {{-- Sidebar brand logo --}}
    @if (config('adminlte.logo_img_xl'))
        @include('adminlte::partials.common.brand-logo-xl')
    @else
        @include('adminlte::partials.common.brand-logo-xs')
    @endif

    {{-- Sidebar menu --}}
    <div class="sidebar">
        <nav class="mt-2">
            @if (auth()->user()->isCustomer() && $user_client_beon)
                <ul class="nav nav-pills nav-sidebar flex-column {{ config('adminlte.classes_sidebar_nav', '') }}"
                    data-widget="treeview" role="menu"
                    @if (config('adminlte.sidebar_nav_animation_speed') != 300) data-animation-speed="{{ config('adminlte.sidebar_nav_animation_speed') }}" @endif
                    @if (!config('adminlte.sidebar_nav_accordion')) data-accordion="false" @endif>
                    {{-- Configured sidebar links --}}
                    @each('adminlte::partials.sidebar.menu-item', $adminlte->menu('sidebar'), 'item')
                </ul>
            @else
                <ul class="nav nav-pills nav-sidebar flex-column {{ config('adminlte.classes_sidebar_nav', '') }}"
                    data-widget="treeview" role="menu"
                    @if (config('adminlte.sidebar_nav_animation_speed') != 300) data-animation-speed="{{ config('adminlte.sidebar_nav_animation_speed') }}" @endif
                    @if (!config('adminlte.sidebar_nav_accordion')) data-accordion="false" @endif>
                    {{-- Configured sidebar links --}}
                    @each('adminlte::partials.sidebar.menu-item', $adminlte->menu('sidebar'), 'item')
                </ul>
            @endif

        </nav>
    </div>

</aside>
