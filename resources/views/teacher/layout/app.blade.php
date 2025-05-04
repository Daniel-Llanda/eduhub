<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Dashboard | Teacher</title>
        <script src="/source/js/sweetalert2@11.js"></script>
        <link rel="icon" type="image/png" href="{{ asset('east_logo.png') }}">
        <link rel="stylesheet" href="{{ asset('source/css/styles.css') }}">

        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <style>
              * {
                scrollbar-width: none;
            }

            *::-webkit-scrollbar {
                display: none;
            }
        </style>
    </head>
    <body class="sb-nav-fixed">
        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
            <!-- Navbar Brand-->
            <a class="navbar-brand ps-3" href="index.html">EPCST EduHub</a>
            <!-- Sidebar Toggle-->
            <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
            <!-- Navbar Search-->
            <div class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
                <div class="input-group">
                </div>
            </div>
            <!-- Navbar-->
            <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="{{route('teacher.profile.edit')}}">Profile</a></li>
                        <li><hr class="dropdown-divider" /></li>
                        <li>
                            <form method="POST" action="{{ route('teacher.logout') }}">
                                @csrf
            
                                <a class="dropdown-item text-red-500"
                                    :href="route('teacher.logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();"
                                >
                                    {{ __('Log Out') }}
                                </a>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
        <div id="layoutSidenav">
            <div id="layoutSidenav_nav">
                <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                    <div class="sb-sidenav-menu">
                        <div class="nav">
                            <div class="sb-sidenav-menu-heading">Core</div>
                            <a class="nav-link {{ request()->routeIs('teacher.dashboard') ? 'text-white' : '' }}" href="{{ route('teacher.dashboard') }}">
                                <div class="sb-nav-link-icon"><i class="bi bi-speedometer2"></i></div>
                                Dashboard
                            </a>
                            <a class="nav-link {{ request()->routeIs('teacher.student') ? 'text-white' : '' }}" href="{{ route('teacher.student') }}">
                                <div class="sb-nav-link-icon"><i class="bi bi-person-circle"></i></div>
                                Student
                            </a>
                            <a class="nav-link {{ request()->routeIs('teacher.student_post', 'teacher.student_post_info') ? 'text-white' : '' }}" href="{{ route('teacher.student_post') }}">
                                <div class="sb-nav-link-icon"><i class="bi bi-file-post"></i></div>
                                Student Post
                            </a>
                            <a class="nav-link {{ request()->routeIs('teacher.upload_post') ? 'text-white' : '' }}" href="{{ route('teacher.upload_post') }}">
                                <div class="sb-nav-link-icon"><i class="bi bi-image"></i></div>
                                Upload
                            </a>
                            <a class="nav-link {{ request()->routeIs('teacher.post_favorites') ? 'text-white' : '' }}" href="{{ route('teacher.post_favorites') }}">
                                <div class="sb-nav-link-icon"><i class="bi bi-bookmark"></i></div>
                                Favorites
                            </a>
                            
                            
                        </div>
                    </div>
                    <div class="sb-sidenav-footer">
                        <div class="small">Logged in as:</div>
                        {{ Auth::user()->name }}
                    </div>
                </nav>
            </div>
            <div id="layoutSidenav_content">
                <main>
                    @yield('main_teacher')
                    
                </main>
                
            </div>
        </div>
        <script src="{{ asset('source/js/scripts.js') }}"></script>
    </body>
</html>

