<aside class="main-sidebar sidebar-dark-primary elevation-4" style="background: linear-gradient(to bottom, #00044e, #00044e);">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
        <img src="{{asset('/logo/logo.jpg')}}"
             alt=""
             class="brand-image img-circle elevation-3"
             style="opacity: .8">
        <span class="brand-text font-weight-light">Billing Software</span>
    </a>
<style>
    .nav-item.has-treeview.menu-open .nav-treeview {
        display: block;
        padding-left: 20px
    }

    .nav-treeview {
        display: none;
    }

</style>
    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item {{Route::is('home') ? 'active':''}}">
                    <a href="{{route('home')}}" class="nav-link" style="color: #ffffff;">
                        <i class="nav-icon fas fa-desktop"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                
                <li class="nav-item {{Route::is('vendors.index') || Route::is('vendors.setupServices') ? 'active':''}}">
                    <a href="{{route('vendors.index')}}" class="nav-link" style="color: #ffffff;">
                        <i class="nav-icon fa fa-industry"></i>
                        <p>Vendors Management</p>
                    </a>
                </li>

                <li class="nav-item {{Route::is('clients.index') ? 'active':''}}">
                    <a href="{{route('clients.index')}}" class="nav-link" style="color: #ffffff;">
                        <i class="nav-icon fa fa-users"></i>
                        <p>Clients Management</p>
                    </a>
                </li>
                
                <li class="nav-item {{Route::is('services.index') ? 'active':''}}">
                    <a href="{{route('services.index')}}" class="nav-link" style="color: #ffffff;">
                        <i class="nav-icon fa fa-layer-group"></i>
                        <p>Services Management</p>
                    </a>
                </li>
             
                <li class="nav-item {{Route::is('additional_services.index') ? 'active':''}}">
                    <a href="{{route('additional_services.index')}}" class="nav-link" style="color: #ffffff;">
                        <i class="nav-icon fa fa-house"></i>
                        <p>Inhouse Services</p>
                    </a>
                </li>
                
                <li class="nav-item {{Route::is('inclusives.index') ? 'active':''}}">
                    <a href="{{route('inclusives.index')}}" class="nav-link" style="color: #ffffff;">
                        <i class="nav-icon fa fa-clipboard"></i>
                        <p>Inclusive Services</p>
                    </a>
                </li>

                <li class="nav-item {{Route::is('descriptives.index') ? 'active':''}}">
                    <a href="{{route('descriptives.index')}}" class="nav-link" style="color: #ffffff;">
                        <i class="nav-icon fa fa-clipboard"></i>
                        <p>Descriptives</p>
                    </a>
                </li>

                <li class="nav-item {{Route::is('taxes.index') ? 'active':''}}">
                    <a href="{{route('taxes.index')}}" class="nav-link" style="color: #ffffff;">
                        <i class="nav-icon fa fa-layer-group"></i>
                        <p>Tax Management</p>
                    </a>
                </li>
                
                <li class="nav-item {{Route::is('cdrs.index') ? 'active':''}}">
                    <a href="{{route('cdrs.index')}}" class="nav-link" style="color: #ffffff;">
                        <i class="nav-icon fa-solid fa-database"></i>
                        <p>CDRs Management</p>
                    </a>
                </li>
               
                <li class="nav-item {{Route::is('billing.index') ? 'active':''}}">
                    <a href="{{route('billing.index')}}" class="nav-link" style="color: #ffffff;">
                        <i class="nav-icon fa-solid fa-file-invoice"></i>
                        <p>Bills Management</p>
                    </a>
                </li>

                <li class="nav-item has-treeview {{Route::is('general_setting') || Route::is('settings.show') ? 'menu-open' : ''}}">
                    <a href="#" class="nav-link {{Route::is('general_setting') ? 'active' : ''}}" style="color: #ffffff;">
                        <i class="nav-icon fa-solid fa-gear"></i>
                        <p>
                            Settings
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('general_setting') }}" class="nav-link {{Route::is('general_setting') || Route::is('settings.show') ? 'active' : ''}}" style="color: #ffffff;">
                                <i class="fa-solid fa-minus nav-icon" style="font-size:0.6rem;"></i>
                                <p>General Setting</p>
                            </a>
                        </li>
                        <!-- Add more dropdown items as needed -->
                    </ul>
                </li>
                
                <li class="nav-item">
                    <a href="#" class="nav-link" style="color: #ffffff;"
                       onclick="event.preventDefault();
                           document.getElementById('logout-form').submit();"
                    >
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>Logout</p>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        {{ csrf_field() }}
                    </form>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
