<?php
// Partial: Scripts de navegación (menú mobile, dropdown, mobile menu).
?>
<script>
    (function () {
        // Menú hamburguesa del dashboard (mobile)
        const toggle = document.querySelector('.mobile-menu-btn');
        const sidebar = document.getElementById('dashSidebar');
        if (toggle && sidebar) {
            let open = false;
            const setOpen = (v) => {
                open = v;
                sidebar.style.display = open ? 'block' : 'none';
                sidebar.style.position = 'fixed';
                sidebar.style.left = '0';
                sidebar.style.top = '0';
                sidebar.style.width = '260px';
                sidebar.style.height = '100vh';
                sidebar.style.zIndex = '100';
                sidebar.style.boxShadow = '4px 0 24px rgba(0,0,0,0.5)';
            };
            toggle.addEventListener('click', () => setOpen(!open));
            document.addEventListener('click', (e) => {
                if (open && !sidebar.contains(e.target) && !toggle.contains(e.target)) {
                    setOpen(false);
                }
            });
            window.addEventListener('resize', () => {
                if (window.innerWidth > 900) { sidebar.style = ''; setOpen(false); }
            });
        }

        // Toggle del menú de navegación principal (mobile)
        const navToggle = document.querySelector('.nav-toggle');
        const navMenu = document.getElementById('primary-menu');
        if (navToggle && navMenu) {
            const setNav = (o) => {
                navToggle.setAttribute('aria-expanded', String(o));
                navToggle.setAttribute('aria-label', o ? 'Cerrar menú' : 'Abrir menú');
                navToggle.innerHTML = '<i class="fa-solid ' + (o ? 'fa-xmark' : 'fa-bars') + '" aria-hidden="true"></i>';
                navMenu.classList.toggle('is-open', o);
            };
            navToggle.addEventListener('click', () => setNav(navToggle.getAttribute('aria-expanded') !== 'true'));
            navMenu.querySelectorAll('a').forEach((l) => l.addEventListener('click', () => setNav(false)));
            window.addEventListener('resize', () => { if (window.innerWidth > 1080) setNav(false); });
        }

        // Dropdown "Explorar" del header
        const dropdownBtn = document.querySelector('.dropdown-btn');
        const dropdown = document.querySelector('.dropdown');
        if (dropdownBtn && dropdown) {
            const toggleDropdown = () => {
                const isOpen = dropdown.classList.contains('is-open');
                dropdown.classList.toggle('is-open', !isOpen);
                dropdownBtn.setAttribute('aria-expanded', String(!isOpen));
            };
            dropdownBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                toggleDropdown();
            });
            dropdown.addEventListener('mouseenter', () => {
                dropdown.classList.add('is-open');
                dropdownBtn.setAttribute('aria-expanded', 'true');
            });
            dropdown.addEventListener('mouseleave', () => {
                dropdown.classList.remove('is-open');
                dropdownBtn.setAttribute('aria-expanded', 'false');
            });
            document.addEventListener('click', (e) => {
                if (!dropdown.contains(e.target)) {
                    dropdown.classList.remove('is-open');
                    dropdownBtn.setAttribute('aria-expanded', 'false');
                }
            });
        }
    })();
</script>
