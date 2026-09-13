/**
 * Mobile Drawer Navigation Module for PROPFlow CRM
 * Manages off-canvas sidebar drawer open/close transitions,
 * backdrop clicks, Escape key, and body scroll lock.
 */

export function initMobileMenu() {
    const mobileSidebar = document.getElementById('mobile-sidebar');
    const backdrop = document.getElementById('mobile-sidebar-backdrop');
    const openButtons = document.querySelectorAll('[data-mobile-menu-open]');
    const closeButtons = document.querySelectorAll('[data-mobile-menu-close]');

    if (!mobileSidebar || !backdrop) {
        return;
    }

    function openDrawer() {
        backdrop.classList.remove('hidden');
        // Trigger reflow for transition
        void backdrop.offsetWidth;
        backdrop.classList.remove('opacity-0');
        backdrop.classList.add('opacity-100');

        mobileSidebar.classList.remove('-translate-x-full');
        mobileSidebar.classList.add('translate-x-0');
        document.body.classList.add('overflow-hidden');
    }

    function closeDrawer() {
        backdrop.classList.remove('opacity-100');
        backdrop.classList.add('opacity-0');

        mobileSidebar.classList.remove('translate-x-0');
        mobileSidebar.classList.add('-translate-x-full');
        document.body.classList.remove('overflow-hidden');

        setTimeout(() => {
            if (mobileSidebar.classList.contains('-translate-x-full')) {
                backdrop.classList.add('hidden');
            }
        }, 300);
    }

    openButtons.forEach((btn) => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            openDrawer();
        });
    });

    closeButtons.forEach((btn) => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            closeDrawer();
        });
    });

    backdrop.addEventListener('click', () => {
        closeDrawer();
    });

    // Close on Escape key press
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !mobileSidebar.classList.contains('-translate-x-full')) {
            closeDrawer();
        }
    });

    // Close drawer when clicking navigation links inside drawer
    mobileSidebar.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', () => {
            // Give subtle delay for click feedback
            setTimeout(closeDrawer, 150);
        });
    });
}
