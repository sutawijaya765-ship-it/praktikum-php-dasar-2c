<?php
$activePage = in_array($page ?? '', ['book', 'editbook'], true) ? 'books' : ($page ?? '');
$navigation = [
    'dashboard' => ['icon' => '▦', 'label' => 'Dashboard'],
    'books' => ['icon' => '▤', 'label' => 'Data Buku'],
    'categories' => ['icon' => '▧', 'label' => 'Kategori'],
    'members' => ['icon' => '♙', 'label' => 'Anggota'],
    'loans' => ['icon' => '⇄', 'label' => 'Peminjaman'],
    'reports' => ['icon' => '▥', 'label' => 'Laporan'],
];
?>
<aside class="sidebar" id="adminSidebar" aria-label="Navigasi utama">
    <a class="brand" href="?page=dashboard"><span class="brand-mark" aria-hidden="true">▣</span><span>Mini Library</span></a>
    <p class="sidebar-label">MENU UTAMA</p>
    <nav class="sidebar-nav">
        <?php foreach ($navigation as $key => $item): ?>
            <a class="navitem<?= $activePage === $key ? ' active' : '' ?>" href="?page=<?=e($key)?>"<?= $activePage === $key ? ' aria-current="page"' : '' ?>>
                <span class="nav-icon" aria-hidden="true"><?=e($item['icon'])?></span>
                <span class="nav-label"><?=e($item['label'])?></span>
            </a>
        <?php endforeach ?>
    </nav>
</aside>
<div class="sidebar-backdrop" data-sidebar-close aria-hidden="true"></div>
