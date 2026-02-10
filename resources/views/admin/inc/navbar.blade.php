<div class="navbar">
    <div class="navbar-inner">
        <div class="navbar-container">

            {{-- BRAND --}}
            <div class="navbar-header pull-left">
                <a href="{{ url('/admin') }}" class="navbar-brand">
                    <small>
                        <img src="{{ asset('assets/img/logo.png') }}" alt="Logo">
                    </small>
                </a>
            </div>

            {{-- SIDEBAR TOGGLE --}}
            <div class="sidebar-collapse" id="sidebar-collapse">
                <i class="collapse-icon fa fa-bars"></i>
            </div>

            <div class="navbar-header pull-right">
                <div class="navbar-account">
                    <ul class="account-area">

                        {{-- 🔔 Notifications --}}
                        <li>
                            <a class="dropdown-toggle" data-toggle="dropdown" title="Notifications" href="#">
                                <i class="icon fa fa-warning"></i>
                            </a>

                            <ul class="pull-right dropdown-menu dropdown-arrow dropdown-notifications">
                                <li>
                                    <a href="#">
                                        <div class="clearfix">
                                            <div class="notification-icon">
                                                <i class="fa fa-phone bg-themeprimary white"></i>
                                            </div>
                                            <div class="notification-body">
                                                <span class="title">Skype meeting with Patty</span>
                                                <span class="description">01:00 pm</span>
                                            </div>
                                            <div class="notification-extra">
                                                <i class="fa fa-clock-o themeprimary"></i>
                                                <span class="description">office</span>
                                            </div>
                                        </div>
                                    </a>
                                </li>

                                <li class="dropdown-footer">
                                    <span>Today</span>
                                </li>
                            </ul>
                        </li>

                        {{-- ✉ Messages --}}
                        <li>
                            <a class="dropdown-toggle" data-toggle="dropdown" title="Messages" href="#">
                                <i class="icon fa fa-envelope"></i>
                                <span class="badge">3</span>
                            </a>

                            <ul class="pull-right dropdown-menu dropdown-arrow dropdown-messages">
                                <li>
                                    <a href="#">
                                        <img src="{{ asset('assets/img/avatars/divyia.jpg') }}"
                                            class="message-avatar"
                                            alt="Divyia Austin">
                                        <div class="message">
                                            <span class="message-sender">Divyia Austin</span>
                                            <span class="message-time">2 minutes ago</span>
                                            <span class="message-subject">Apple pie recipe</span>
                                            <span class="message-body">
                                                to identify the sending application
                                            </span>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        {{-- ✅ Tasks --}}
                        <li>
                            <a class="dropdown-toggle" data-toggle="dropdown" title="Tasks" href="#">
                                <i class="icon fa fa-tasks"></i>
                                <span class="badge">4</span>
                            </a>

                            <ul class="pull-right dropdown-menu dropdown-tasks dropdown-arrow">
                                <li class="dropdown-header bordered-darkorange">
                                    <i class="fa fa-tasks"></i>
                                    4 Tasks In Progress
                                </li>

                                <li>
                                    <a href="#">
                                        <div class="clearfix">
                                            <span class="pull-left">Account Creation</span>
                                            <span class="pull-right">65%</span>
                                        </div>
                                        <div class="progress progress-xs">
                                            <div class="progress-bar" style="width:65%"></div>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        {{-- 👤 Logged-in User --}}
                        <li>
                            <a class="login-area dropdown-toggle" data-toggle="dropdown">
                                <div class="avatar">
                                    <img src="{{ asset('assets/img/avatars/adam-jansen.jpg') }}"
                                        alt="admin">
                                </div>
                                <section>
                                    <h2>
                                        <span class="profile">
                                            <span>Admin User</span>
                                        </span>
                                    </h2>
                                </section>
                            </a>

                            <ul class="pull-right dropdown-menu dropdown-arrow dropdown-login-area">
                                <li class="username">
                                    <a>Admin User</a>
                                </li>
                                <li class="email">
                                    <a>admin@example.com</a>
                                </li>

                                <li class="edit">
                                    <a href="#" class="pull-left">Profile</a>
                                    <a href="#" class="pull-right">Settings</a>
                                </li>

                                <li class="dropdown-footer">
                                    <form method="POST" action="logout">
                                        @csrf
                                        <button type="submit" class="btn btn-link">
                                            Sign out
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>

                    </ul>

                    {{-- ⚙ Settings --}}
                    <div class="setting">
                        <a id="btn-setting" title="Setting" href="#">
                            <i class="icon glyphicon glyphicon-cog"></i>
                        </a>
                    </div>

                    <div class="setting-container">
                        <label>
                            <input type="checkbox" id="checkbox_fixednavbar">
                            <span class="text">Fixed Navbar</span>
                        </label>
                        <label>
                            <input type="checkbox" id="checkbox_fixedsidebar">
                            <span class="text">Fixed Sidebar</span>
                        </label>
                    </div>

                </div>
            </div>


        </div>
    </div>
</div>
