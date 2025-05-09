<div class="sidebar">
    <div class="link-bar d-flex">
        <li class="{{ Route::is('admin.dashboard') ? 'active' : '' }}">
            <a href="{{ route('admin.dashboard') }}">
                <span class="material-symbols-outlined">home</span>Dashboard
            </a>
        </li>
        <li class="{{ Route::is('admin.news') ? 'active' : '' }}">
            <a href="{{ route('admin.news') }}">
                <span class="material-symbols-outlined">campaign</span>NEWS
            </a>
        </li>
        <li class="{{ Route::is('admin.arsip') ? 'active' : '' }}">
            <a href="{{ route('admin.arsip') }}">
                <span class="material-symbols-outlined">bookmark</span>Arsip
            </a>
        </li>
        <li class="{{ Route::is('admin.absensi') ? 'active' : '' }}">
            <a href="{{ route('admin.absensi') }}">
                <span class="material-symbols-outlined">inventory</span>Data Ormawa
            </a>
        </li>
        <li class="{{ Route::is('admin.tambahAdminView') ? 'active' : '' }}">
            <a href="{{ route('admin.tambahAdminView') }}">
                <span class="material-symbols-outlined">person</span>Create User
            </a>
        </li>
        <li>
            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button type="submit"
                    style="display:flex; align-items:center; justify-content: center; color:black; border:none; background:none;">
                    <span class="material-symbols-outlined">close</span>Keluar
                </button>
            </form>
        </li>
    </div>
</div>
