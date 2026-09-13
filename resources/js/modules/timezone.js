/**
 * Timezone Dropdown Toggle Module
 */
export function initTimezoneDropdown() {
    const dropdowns = document.querySelectorAll('[data-timezone-dropdown]');

    dropdowns.forEach((dropdown) => {
        const toggleBtn = dropdown.querySelector('[data-timezone-toggle]');
        const menu = dropdown.querySelector('[data-timezone-menu]');

        if (!toggleBtn || !menu) return;

        toggleBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            const isHidden = menu.classList.contains('hidden');

            // Close any other open dropdowns first
            document.querySelectorAll('[data-timezone-menu]').forEach((m) => m.classList.add('hidden'));

            if (isHidden) {
                menu.classList.remove('hidden');
            }
        });
    });

    // Close on click outside
    document.addEventListener('click', (e) => {
        if (!e.target.closest('[data-timezone-dropdown]')) {
            document.querySelectorAll('[data-timezone-menu]').forEach((m) => m.classList.add('hidden'));
        }
    });

    // Close on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            document.querySelectorAll('[data-timezone-menu]').forEach((m) => m.classList.add('hidden'));
        }
    });
}
