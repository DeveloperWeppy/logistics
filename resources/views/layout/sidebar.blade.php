<div class="sidebar-wrapper" style="height: 100vh;">
    <div>
        <div class="logo-wrapper"><a href="{{ route('dashboard') }}"><img class="img-fluid for-light"
                    src="{{ asset('assets/images/logo/logo.png') }}" alt=""><img class="img-fluid for-dark"
                    src="{{ asset('assets/images/logo/logo-dark.png') }}" alt=""></a>
                    <div class="back-btn"><i class="fa fa-angle-left"></i></div>
            <div class="toggle-sidebar"><i class="status_toggle middle sidebar-toggle" data-feather="align-left"> </i>
            </div>
        </div>
        <div class="logo-icon-wrapper"><a href="{{ route('dashboard') }}"><img class="img-fluid for-light"
                    src="{{ asset('assets/images/logo/logo-icon.png') }}" alt=""><img class="img-fluid for-dark"
                    src="{{ asset('assets/images/logo/logo-icon-dark.png') }}" alt=""></a></div>
        <nav class="sidebar-main">
            <div class="left-arrow" id="left-arrow"><i data-feather="arrow-left"></i></div>
            <div id="sidebar-menu">
                <ul class="sidebar-links" id="simple-bar">
                    <li class="back-btn"><a href="{{ route('dashboard') }}"><img class="img-fluid for-light"
                                src="{{ asset('assets/images/logo/logo-icon.png') }}" alt=""><img
                                class="img-fluid for-dark" src="{{ asset('assets/images/logo/logo-icon-dark.png') }}"
                                alt=""></a>
                        <div class="mobile-back text-end"><span>Back</span><i class="fa fa-angle-right ps-2"
                                aria-hidden="true"></i></div>
                    </li>

                    @if (!auth()->user()->hasRole('Inventario') && !auth()->user()->hasRole('Facturador') || auth()->user()->hasRole('Admin'))
                    <li class="sidebar-list"><a class="sidebar-link sidebar-title link-nav"
                        href="{{ route('dashboard') }}"><i
                        data-feather="home"></i><span>Dashboard
                            </span></a></li>
                    @endif

                    @if (!auth()->user()->hasRole('Inventario') && !auth()->user()->hasRole('Facturador') || auth()->user()->hasRole('Admin'))
                        <li class="sidebar-list"><a class="sidebar-link sidebar-title" href="javascript:void(0)"><i
                                    data-feather="shopping-bag"></i><span>Pedidos</span></a>
                            <ul class="sidebar-submenu">
                                <li><a href="{{ route('orders') }}">Activos</a></li>
                                <li><a href="{{ route('orders') }}?type=completed">Completados</a></li>
                                
                            </ul>
                        </li>
                    @endif
                    @if( auth()->user()->hasRole('Admin'))
                    <li class="sidebar-list"><a class="sidebar-link sidebar-title link-nav"
                        href="{{ route('users') }}"><i
                        data-feather="users"></i><span>Usuarios
                            </span></a></li>
                    @endif


                    @if (!auth()->user()->hasRole('Inventario') && !auth()->user()->hasRole('Facturador') || auth()->user()->hasRole('Admin'))
                        <li class="sidebar-list"><a class="sidebar-link sidebar-title link-nav"
                            href="{{ route('dashboard') }}"><i
                            data-feather="settings"></i><span>Configuración
                                </span></a></li>
                    @endif
                    
                    @if (auth()->user()->hasRole('Inventario') || auth()->user()->hasRole('Facturador'))
                        <li class="sidebar-list"><a class="sidebar-link sidebar-title link-nav"
                            href="{{ route('inventory.index') }}"><i
                            data-feather="list"></i><span>Inventario
                                </span></a></li>      
                    @endif
                            
                    <li class="sidebar-list"><a class="sidebar-link sidebar-title link-nav"
                        href="{{ route('orders.get_orders') }}"><i
                        data-feather="shopping-bag"></i><span>Pedidos
                            </span></a></li> 

                </ul>
             
            </div>
            <div class="right-arrow" id="right-arrow"><i data-feather="arrow-right"></i></div>
        </nav>
    </div>
</div>
