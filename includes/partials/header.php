<header class="topbar">
    <div class="topbar-start">
        <button class="sidebar-toggle" type="button" aria-label="Buka navigasi" aria-controls="adminSidebar" aria-expanded="false" data-sidebar-toggle>
            <span></span><span></span><span></span>
        </button>
        <strong class="page-title"><?=e($titles[$page] ?? 'Mini Digital Library')?></strong>
    </div>
    <div class="topbar-actions">
        <span class="admin-greeting"><?=e($_SESSION['user']['name'] ?? '')?></span>
        <a class="btn btn-sm btn-outline-secondary" href="?page=password">Ganti kata sandi</a>
        <form method="post" action="?page=logout" class="logout-form">
            <input type="hidden" name="csrf" value="<?=e(csrf())?>">
            <button class="btn btn-sm btn-outline-secondary" type="submit">Keluar</button>
        </form>
    </div>
</header>
