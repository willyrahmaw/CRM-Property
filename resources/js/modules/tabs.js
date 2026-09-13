/**
 * Simple, declarative Tab Switcher
 * Usage:
 *   <button data-tab-target="#tab-panel-id" data-tab-group="my-group" class="active ...">Tab 1</button>
 *   <div id="tab-panel-id" data-tab-panel-group="my-group">...</div>
 */
export function initTabs() {
    document.addEventListener('click', (event) => {
        const tabBtn = event.target.closest('[data-tab-target]');
        if (!tabBtn) return;
        event.preventDefault();

        const targetSelector = tabBtn.getAttribute('data-tab-target');
        const group = tabBtn.getAttribute('data-tab-group') || 'default';
        const targetPanel = document.querySelector(targetSelector);
        if (!targetPanel) return;

        // Reset all buttons in this tab group
        const buttons = document.querySelectorAll(`[data-tab-group="${group}"]`);
        buttons.forEach((btn) => {
            btn.classList.remove('bg-[#161616]', 'text-white', 'shadow-sm');
            btn.classList.add('bg-[#F7F6F2]', 'text-[#79766F]', 'hover:text-[#161616]', 'hover:bg-[#E8E4DA]');
            btn.setAttribute('aria-selected', 'false');
        });

        // Activate clicked button
        tabBtn.classList.remove('bg-[#F7F6F2]', 'text-[#79766F]', 'hover:text-[#161616]', 'hover:bg-[#E8E4DA]');
        tabBtn.classList.add('bg-[#161616]', 'text-white', 'shadow-sm');
        tabBtn.setAttribute('aria-selected', 'true');

        // Hide all panels in this tab group
        const panels = document.querySelectorAll(`[data-tab-panel-group="${group}"]`);
        panels.forEach((panel) => {
            panel.classList.add('hidden');
        });

        // Show target panel
        targetPanel.classList.remove('hidden');
    });
}
