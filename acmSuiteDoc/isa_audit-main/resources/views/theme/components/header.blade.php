<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-fixed">
    <div class="container-fluid">
        <div class="navbar-wrapper">
            <div class="row mr-3">
                <a href="/">
                    <img id="sidebarLogo" class="img-responsive mr-2" width="145px" style="padding: 0px 10px"
                         src="/logos/acm_suite_logo_h.png">
                </a>
            </div>
            <a class="navbar-brand" id="linkPage" href="#"><span id="titleComeback"></span></a><span id="titlePage"></span>
        </div>
        <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-bar burger-lines"></span>
            <span class="navbar-toggler-bar burger-lines"></span>
            <span class="navbar-toggler-bar burger-lines"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <notifications :id-user="{{ intval(Auth::user()->id_user) }}" />
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="nc-icon nc-bullet-list-67"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                        <a href="/v2/account/view" class="dropdown-item">
                            <i class="nc-icon nc-single-02"></i> Mi cuenta
                        </a>
                        <a href="/logout" class="dropdown-item text-danger">
                            <i class="nc-icon nc-button-power"></i> Cerrar sesión
                        </a>
                    </div>
                </li>
                
            </ul>
        </div>
    </div>
</nav>
<!-- End Navbar -->
