
<aside class="main-sidebar {{ config('adminlte.classes_sidebar', 'sidebar-dark-primary elevation-4') }}">

    {{-- Sidebar brand logo --}}
    @if(config('adminlte.logo_img_xl'))
        @include('adminlte::partials.common.brand-logo-xl')
    @else
        @include('adminlte::partials.common.brand-logo-xs')
    @endif

    {{-- Sidebar menu --}}
    <div class="sidebar">
        <nav class="pt-2">
            <ul class="nav nav-pills nav-sidebar flex-column {{ config('adminlte.classes_sidebar_nav', '') }}"
                data-widget="treeview" role="menu"
                @if(config('adminlte.sidebar_nav_animation_speed') != 300)
                    data-animation-speed="{{ config('adminlte.sidebar_nav_animation_speed') }}"
                @endif
                @if(!config('adminlte.sidebar_nav_accordion'))
                    data-accordion="false"
                @endif>
              


                @can('is_admin')
                    <hr class="my-4 border-t border-white">
                    <div class="text-center">
                        <p class="text-white">
                            <i class="fas fa-user-shield"></i>
                            Admin Panel
                        </p>
                        <br>
                    </div>
                @endcan

                @each('adminlte::partials.sidebar.menu-item', $adminlte->menu('sidebar'), 'item')
        
                
                {{-- para adm --}}
                @can('is_admin')

                    <li class="nav-item">
                        <a href="{{ route('admin.user') }}" class="nav-link">
                            <i class="fas fa-users"></i>
                            <p>
                                Users
                            </p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('admin.categorias') }}" class="nav-link">
                            <i class="fas fa-tag"></i>
                            <p>
                                Categorias
                            </p>
                        </a>
                    </li>
                       
                    <li class="nav-item">
                        <a href="{{ route('admin.produtos') }}" class="nav-link">
                            <i class="fas fa-shopping-cart"></i>
                            <p>
                                Produtos
                            </p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('admin.vendas') }}" class="nav-link">
                            <i class="fas fa-dollar-sign"></i>
                            <p>
                                Vendas
                            </p>
                        </a>
                    </li>
                @endcan
                
            </ul>
        </nav>
    </div>

</aside>
