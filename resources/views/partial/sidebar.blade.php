<!-- Offcanvas Sidebar -->
<div class="offcanvas offcanvas-start custom-offcanvas" tabindex="-1" id="offcanvasSidebar" aria-labelledby="offcanvasSidebarLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasSidebarLabel">Menu</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-0">
        <aside class="sidebar">
            <img src="{{ asset('asset/image/LOGO.png') }}" alt="Logo" class="sidebar-logo mb-4">
            <ul class="menu-list">
                <li>
                    <a href="/dashboard" class="menu-item {{ request()->is('dashboard') ? 'active' : '' }}">
                        <i class="fas fa-gauge me-2"></i> Dashboard
                    </a>
                </li>
                <li class="menu-parent">
                    <a href="#" class="menu-item toggle-submenu">
                        <i class="fas fa-users me-2"></i> Profil
                    </a>
                    <ul class="submenu-list {{ request()->is('profiles*') || request()->routeIs('petugas.index') ? '' : 'd-none' }}">
                        <li>
                            <a href="/profiles" class="menu-item {{ request()->is('profiles') ? 'active' : '' }}">
                                <i class="fas fa-child me-2"></i> Profil Balita
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('petugas.index') }}" class="menu-item {{ request()->routeIs('petugas.index') ? 'active' : '' }}">
                                <i class="fas fa-user-md me-2"></i> Biodata Petugas
                            </a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="/pengukuran" class="menu-item {{ request()->is('pengukuran') ? 'active' : '' }}">
                        <i class="fas fa-solid fa-heart-pulse me-2"></i> Status Gizi 
                    </a>
                </li>
                <li>
                    <a href="{{ route('grafik.index')}}" class="menu-item {{ request()->routeIs('grafik.index') ? 'active' : '' }}">
                        <i class="fas fa-chart-line me-2"></i> Grafik Balita
                    </a>
                </li>

                <!-- Menu Informasi -->
                <li class="menu-parent">
                    <a href="#" class="menu-item toggle-submenu">
                        <i class="fas fa-book-open me-2"></i> Informasi
                    </a>
                    <ul class="submenu-list {{ request()->routeIs('galeri.index') || request()->routeIs('informasi.video.index') || request()->routeIs('informasi.recipe.index') ? '' : 'd-none' }}">
                        <li>
                            <a href="{{ route('galeri.index') }}" class="menu-item {{ request()->routeIs('galeri.index') ? 'active' : '' }}">
                                <i class="fas fa-images me-2"></i> Galeri Kegiatan
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('informasi.video.index') }}" class="menu-item {{ request()->routeIs('informasi.video.index') ? 'active' : '' }}">
                                <i class="fas fa-video me-2"></i> Video Edukasi
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('informasi.recipe.index') }}" class="menu-item {{ request()->routeIs('informasi.recipe.index') ? 'active' : '' }}">
                                <i class="fas fa-utensils me-2"></i> Buku Resep
                            </a>
                        </li>
                    </ul>
                </li>

                <li>
                    @if(auth()->user() && auth()->user()->role === 'Admin')
                        <a href="{{ route('users.index')}}" class="menu-item {{ request()->routeIs('users.index') ? 'active' : '' }}">
                            <i class="fas fa-user me-2"></i> Kelola Pengguna
                        </a>
                    @endif
                </li>

                <li>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="menu-item border-0 bg-transparent">
                            <i class="fas fa-sign-out-alt me-2"></i> Keluar
                        </button>
                    </form>
                </li>
            </ul>
        </aside>
    </div>
</div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Dapatkan semua toggle-submenu
            const toggleMenus = document.querySelectorAll('.toggle-submenu');
            
            // Tambahkan event listener untuk setiap toggle-submenu
            toggleMenus.forEach(function(toggleMenu) {
                toggleMenu.addEventListener('click', function (e) {
                    e.preventDefault();
                    // Dapatkan submenu yang tepat (sibling dari parent)
                    const submenu = this.closest('.menu-parent').querySelector('.submenu-list');
                    submenu.classList.toggle('d-none'); // Toggle visibility
                });
            });
        });
    </script>

    <style>
        .submenu-list {
            list-style: none;
            padding-left: 20px;
            margin-top: 10px;
            transition: all 0.3s ease-in-out;
        }

        .submenu-list.d-none {
            display: none; /* Hidden state */
        }

        .submenu-list .menu-item {
            font-size: 0.9rem;
            color: #ffffff;
            text-decoration: none;
        }

        .submenu-list .menu-item.active {
            color: #f07676; 
        }

        .submenu-list .menu-item:hover {
            color: #fff9f9;
            text-decoration: underline;
        }
    </style>