<!-- Sidebar -->
<div class="sidebar" data-color="black" data-image="/assets/img/img_5.jpg">
    <div class="logo">
        <div class="navbar-wrapper">
            <div class="navbar-minimize" id="minimizeSidebar">
                <span class="icon">
                    <i class="fa fa-times visible-on-sidebar-regular"></i>
                    <i class="fa fa-navicon visible-on-sidebar-mini"></i>
                </span>
                <a id="txtCloseMenu" class="logo-normal" style="display: unset;">
                    <span >Cerrar</span>
                </a>
            </div>
        </div>
    </div>
    <div class="sidebar-wrapper">
        <div class="user">
            @if(Session::has('user') && is_array(Session::get('user')))
                <div class="photo">
                    <img id="sidebarImgProfile" src="{{ Session::get('image') }}" />
                </div>
                <div class="info">
                    <a><span id="sidebarUserName">{{ Session::get('user')['complete_name'] }}</span></a>
                </div>
            @endif
        </div>
        <ul class="nav">
            @if(Session::has('menu') && is_array(Session::get('menu')))
                @foreach(Session::get('menu') as $module)
                    @if($module['has_submodule'])
                        <li class="nav-item module-pop {{ $module['active'] ? 'active' : '' }}" 
                            id="module-{{ $module['id_module'] }}" 
                            data-toggle="popover" 
                            data-trigger="hover" 
                            data-html="true" 
                            title="<div class='text-center'>{{ $module['name_module'] }}</div>" 
                            data-content="<div class='text-center display-4'><i class='nc-icon nc-{{ $module['icon_module'] }} icon-color-{{ $module['color_module'] }}'></i></div><div class='text-justify font-size-popover'>{{ $module['description'] }}</div>"
                            >
                            <a class="nav-link {{ ($module['collapse']) ? 'collapsed' : '' }}" 
                                data-toggle="collapse" href="#nav-{{ $module['name_module'] }}" 
                                aria-expanded="{{ ($module['collapse']) ? 'true' : 'false' }}">
                                <i class="nc-icon nc-{{ $module['icon_module'] }} icon-color-{{ $module['color_module'] }}"></i>
                                <p>{{ $module['name_module'] }} <b class="caret"></b> </p>
                            </a>
                            <div class="collapse {{ ($module['collapse']) ? 'show' : '' }}" id="nav-{{ $module['name_module'] }}" style="">
                                <ul class="nav">
                                    @foreach ( $module['submodules'] as $submodule)
                                        <li class="nav-item {{ $submodule['active'] ? 'active' : '' }}"
                                            id="submodule-{{ $submodule['id_submodule'] }}" 
                                            >
                                            <a class="nav-link" href="{{ asset($submodule['path']) }}">
                                                <span class="sidebar-mini text-{{ $module['color_module'] }} " >{{ $submodule['initials_submodule'] }}</span>
                                                <span class="sidebar-normal">{{ $submodule['name_submodule'] }}</span>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </li>
                    @else
                    <li class="nav-item module-pop {{ $module['active'] ? 'active' : '' }}" 
                        id="module-{{ $module['id_module'] }}" 
                        data-toggle="popover" 
                        data-trigger="hover" 
                        data-html="true" 
                        title="<div class='text-center'>{{ $module['name_module'] }}</div>" 
                        data-content="<div class='text-center display-4'><i class='nc-icon nc-{{ $module['icon_module'] }} icon-color-{{ $module['color_module'] }}'></i></div><div class='text-justify font-size-popover'>{{ $module['description'] }}</div>"
                        >
                        <a class="nav-link" href="{{ asset($module['path']) }}">
                            <i class="nc-icon nc-{{ $module['icon_module'] }} icon-color-{{ $module['color_module'] }}"></i>
                            <p>{{ $module['name_module'] }}</p>
                        </a>
                    </li>
                    @endif
                @endforeach
            @endif
        </ul>
    </div>
</div>
<!--  End Sidebar-->
