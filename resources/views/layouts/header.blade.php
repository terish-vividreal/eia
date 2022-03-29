<!-- BEGIN: Header-->
@php
$user_profile     = (Auth::user()->profile != null) ? asset('storage/store/users/' . Auth::user()->profile) : asset('admin/images/user-icon.png');
@endphp
<header class="page-topbar" id="header">
        <div class="navbar navbar-fixed">
            <nav class="navbar-main navbar-color nav-collapsible sideNav-lock navbar-dark gradient-45deg-light-blue-cyan">
                <div class="nav-wrapper">
                    <ul class="left">
                        <li>
                            <h1 class="logo-wrapper"><a class="brand-logo darken-1" href="{{ url('home/') }}"><img src="{{ asset('admin/images/logo/materialize-logo.png') }}" alt="materialize logo"><span class="logo-text hide-on-med-and-down">Materialize</span></a></h1>
                        </li>
                    </ul>
                    <ul class="navbar-list right">
                        <li class="dropdown-language"><a class="waves-effect waves-block waves-light translation-button" href="javascript:void(0);" data-target="translation-dropdown"><span class="flag-icon flag-icon-gb"></span></a></li>
                        <li class="hide-on-med-and-down"><a class="waves-effect waves-block waves-light toggle-fullscreen" href="javascript:void(0);"><i class="material-icons">settings_overscan</i></a></li>
                        <li class="hide-on-large-only"><a class="waves-effect waves-block waves-light search-button" href="javascript:void(0);"><i class="material-icons">search </i></a></li>
                        <li><a class="waves-effect waves-block waves-light notification-button" href="javascript:void(0);" data-target="notifications-dropdown"><i class="material-icons">notifications_none<small class="notification-badge orange accent-3">2</small></i></a></li>
                        <li><a class="waves-effect waves-block waves-light profile-button" href="javascript:void(0);" data-target="profile-dropdown"><span class="avatar-status avatar-online"><img src="{{auth()->user()->profile}}" alt="Admin"><i></i></span></a></li>
                    </ul>
                    <!-- translation-button-->
                    <ul class="dropdown-content" id="translation-dropdown">
                    <li class="dropdown-item"><a class="grey-text text-darken-1 active" href="#" data-language="en"><i class="flag-icon flag-icon-{{ Config::get('languages')[App::getLocale()]['flag-icon']}}"></i> {{ Config::get('languages')[App::getLocale()]['display'] }}</a></li>
                        @foreach (Config::get('languages') as $lang => $language)
                            @if ($lang != App::getLocale())
                                <a class="dropdown-item" href=""> </a>
                                <li class="dropdown-item"><a class="grey-text text-darken-1 active" href="{{ route('lang.switch', $lang) }}" data-language="en"><i class="flag-icon flag-icon-{{$language['flag-icon']}}"></i> {{$language['display']}}</a></li>
                            @endif
                        @endforeach
                    </ul>
                    <!-- notifications-dropdown-->
                    <ul class="dropdown-content" id="notifications-dropdown">
                        <li>
                            <h6>NOTIFICATIONS<span class="new badge">2</span></h6>
                        </li>
                        <li class="divider"></li>
                         <li><a class="black-text" href="#!"><span class="material-icons icon-bg-circle cyan small">add_shopping_cart</span> A new document uploaded!</a>
                            <time class="media-meta grey-text darken-2" datetime="2015-06-12T20:50:48+08:00">2 hours ago</time>
                        </li>
                        <li><a class="black-text" href="#!"><span class="material-icons icon-bg-circle red small">stars</span> New project submitted. </a>
                            <time class="media-meta grey-text darken-2" datetime="2015-06-12T20:50:48+08:00">3 days ago</time>
                        </li>

                    </ul>
                    <!-- profile-dropdown-->
                    <ul class="dropdown-content" id="profile-dropdown">
                        <li><a class="grey-text text-darken-1" href="{{ url('profile') }}"><i class="material-icons">person_outline</i> Profile</a></li>
                        <li class="divider"></li>
                        <li><a class="grey-text text-darken-1" href="javascript:" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="material-icons">keyboard_tab</i> Logout</a></li>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </ul>
                </div>
                <nav class="display-none search-sm">
                    <div class="nav-wrapper">
                        <form id="navbarForm">
                            <div class="input-field search-input-sm">
                                <input class="search-box-sm" type="search" required="" id="search" placeholder="Explore Materialize" data-search="template-list">
                                <label class="label-icon" for="search"><i class="material-icons search-sm-icon">search</i></label><i class="material-icons search-sm-close">close</i>
                                <ul class="search-list collection search-list-sm display-none"></ul>
                            </div>
                        </form>
                    </div>
                </nav>
            </nav>
            <!-- BEGIN: Horizontal nav start-->
            <nav class="white hide-on-med-and-down" id="horizontal-nav">
                <div class="nav-wrapper">
                    <ul class="right hide-on-med-and-down" id="ul-horizontal-nav" data-menu="menu-navigation">
                        <li>
                            <a class="@if (Request::is('dashboard*')) active @endif" href="{{ url('dashboard') }}"> <i class="material-icons">dashboard</i><span><span class="dropdown-title" data-i18n="Dashboard">Dashboard</span></span></a>
                        </li>
                        <li><a class="dropdown-menu @if (Request::is('projects*') ||  Request::is('projects/create*')) || (Request::is('companies*') ||  Request::is('companies/create*')) active @endif" href="Javascript:void(0)" data-target="ProjectDropdown"><i class="material-icons">assignment</i><span><span class="dropdown-title" data-i18n="Project Profile">Project Profile</span><i class="material-icons right">keyboard_arrow_down</i></span></a>
                            <ul class="dropdown-content dropdown-horizontal-list" id="ProjectDropdown">
                                <li data-menu=""><a href="{{ url('projects') }}" class="@if (Request::is('projects*') ||  Request::is('projects/create*')) active @endif"><span data-i18n="Modern">Projects</span></a></li>
                                <li data-menu=""><a href="{{ url('companies') }}" class="@if (Request::is('companies*') ||  Request::is('companies/create*')) active @endif"><span data-i18n="Modern">Companies</span></a></li>
                            </ul>
                        </li>
                        <li>
                            <a class="@if (Request::is('eias*')) active @endif" href="{{ url('eias') }}"> <i class="material-icons">attach_file</i><span><span class="dropdown-title" data-i18n="Dashboard">EIA</span></span></a>
                        </li>
                        <li>
                            <a class="@if (Request::is('home*')) active @endif" href="javascript:"> <i class="material-icons">assessment</i><span><span class="dropdown-title" data-i18n="Dashboard">Permits</span></span></a>
                        </li>
                    </ul>
                </div>
                <!-- END: Horizontal nav start-->
            </nav>
        </div>
    </header>
    <!-- END: Header-->