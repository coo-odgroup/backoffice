<div class="page-sidebar" id="sidebar">

    <div class="sidebar-header-wrapper">
        <input type="text" class="searchinput" />
        <i class="searchicon fa fa-search"></i>
    </div>

    <ul class="nav sidebar-menu">

        <li>
            <a href="{{ url('/admin') }}">
                <i class="menu-icon glyphicon glyphicon-home"></i>
                <span class="menu-text">Dashboard</span>
            </a>
        </li>

        <li class="active open">
            <a href="#" class="menu-dropdown">
                <i class="menu-icon fa fa-table"></i>
                <span class="menu-text">Location</span>
                <i class="menu-expand"></i>
            </a>

            <ul class="submenu">
                <li class="active">
                    <a href="{{ url('/admin/cities') }}">
                        <span class="menu-text">Cities</span>
                    </a>
                </li>
            </ul>
        </li>

    </ul>
</div>
