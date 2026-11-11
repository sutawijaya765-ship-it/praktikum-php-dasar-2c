(() => {
    const sidebar = document.getElementById('adminSidebar');
    const toggle = document.querySelector('[data-sidebar-toggle]');
    const backdrop = document.querySelector('[data-sidebar-close]');

    if (!sidebar || !toggle || !backdrop) return;

    const setOpen = (open) => {
        sidebar.classList.toggle('is-open', open);
        document.body.classList.toggle('sidebar-open', open);
        toggle.setAttribute('aria-expanded', String(open));
        toggle.setAttribute('aria-label', open ? 'Tutup navigasi' : 'Buka navigasi');
    };

    toggle.addEventListener('click', () => setOpen(!sidebar.classList.contains('is-open')));
    backdrop.addEventListener('click', () => setOpen(false));
    sidebar.querySelectorAll('.navitem').forEach((link) => link.addEventListener('click', () => setOpen(false)));
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') setOpen(false);
    });
    window.matchMedia('(min-width: 761px)').addEventListener('change', (event) => {
        if (event.matches) setOpen(false);
    });
})();
