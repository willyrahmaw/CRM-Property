/**
 * PROPFlow Searchable Dropdown Engine (Production Grade)
 * Automatically transforms native <select> elements into luxury searchable dropdowns
 * with zero clipping, full validation support, keyboard navigation, and smart collision detection.
 */

export function initSearchableSelects(root = document) {
    const selects = root.querySelectorAll('select:not([data-no-search]):not([data-searchable-initialized])');
    selects.forEach((select) => {
        setupSearchableSelect(select);
    });
}

function setupSearchableSelect(select) {
    select.setAttribute('data-searchable-initialized', 'true');

    // 1. Create Wrapper
    const wrapper = document.createElement('div');
    wrapper.className = 'relative w-full prop-searchable-select';

    // Insert wrapper before select, then place select inside wrapper
    select.parentNode.insertBefore(wrapper, select);
    wrapper.appendChild(select);

    // Position native select invisibly behind trigger so form submission & validity are preserved
    select.setAttribute('tabindex', '-1');
    select.style.cssText = 'position: absolute !important; inset: 0 !important; width: 100% !important; height: 100% !important; opacity: 0 !important; pointer-events: none !important; z-index: -1 !important; margin: 0 !important; padding: 0 !important;';

    // Helper to get currently selected option
    const getSelectedOption = () => {
        if (select.selectedIndex >= 0 && select.options[select.selectedIndex]) {
            return select.options[select.selectedIndex];
        }
        return select.options[0] || null;
    };

    let currentSelected = getSelectedOption();

    // 2. Create Trigger Button
    const trigger = document.createElement('button');
    trigger.type = 'button';
    trigger.className = 'w-full px-3 py-2 text-xs rounded-lg border border-[#E8E4DA] bg-white text-[#161616] flex items-center justify-between cursor-pointer focus:outline-none focus:border-[#B89B5E] text-left transition-all min-h-[38px] shadow-xs';
    trigger.setAttribute('aria-haspopup', 'listbox');
    trigger.setAttribute('aria-expanded', 'false');

    if (select.disabled) {
        trigger.disabled = true;
        trigger.classList.add('opacity-60', 'cursor-not-allowed', 'bg-[#F7F6F2]');
    }

    const triggerText = document.createElement('span');
    triggerText.className = 'truncate pr-2 ' + (currentSelected && currentSelected.value ? 'font-semibold text-[#161616]' : 'text-[#79766F]');
    triggerText.textContent = currentSelected ? currentSelected.text.trim() : '-- Pilih --';

    const triggerIcon = document.createElement('i');
    triggerIcon.className = 'fa-solid fa-chevron-down text-[10px] text-[#79766F] transition-transform duration-200 flex-shrink-0';

    trigger.appendChild(triggerText);
    trigger.appendChild(triggerIcon);
    wrapper.appendChild(trigger);

    // 3. Create Dropdown Panel
    const dropdown = document.createElement('div');
    dropdown.className = 'absolute left-0 right-0 z-[9999] bg-white border border-[#E8E4DA] rounded-xl shadow-2xl overflow-hidden hidden transition-all min-w-[220px]';
    dropdown.addEventListener('click', (e) => e.stopPropagation());

    // 4. Search Input Bar
    const searchHeader = document.createElement('div');
    searchHeader.className = 'p-2 bg-[#F7F6F2] border-b border-[#E8E4DA]';

    const searchInputWrapper = document.createElement('div');
    searchInputWrapper.className = 'relative flex items-center';

    const searchIcon = document.createElement('i');
    searchIcon.className = 'fa-solid fa-magnifying-glass absolute left-2.5 text-[#79766F] text-xs pointer-events-none';

    const searchInput = document.createElement('input');
    searchInput.type = 'text';
    searchInput.placeholder = 'Ketik untuk mencari...';
    searchInput.className = 'w-full pl-8 pr-7 py-1.5 text-xs rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E] transition-colors placeholder-[#79766F]';

    const clearSearchBtn = document.createElement('button');
    clearSearchBtn.type = 'button';
    clearSearchBtn.className = 'absolute right-2 text-[#79766F] hover:text-[#161616] text-xs hidden p-0.5';
    clearSearchBtn.innerHTML = '<i class="fa-solid fa-xmark"></i>';

    searchInputWrapper.appendChild(searchIcon);
    searchInputWrapper.appendChild(searchInput);
    searchInputWrapper.appendChild(clearSearchBtn);
    searchHeader.appendChild(searchInputWrapper);
    dropdown.appendChild(searchHeader);

    // 5. Options Container
    const optionsList = document.createElement('div');
    optionsList.className = 'max-h-60 overflow-y-auto p-1.5 space-y-0.5 select-none';
    optionsList.setAttribute('role', 'listbox');

    // Empty state container
    const emptyState = document.createElement('div');
    emptyState.className = 'py-6 px-3 text-center text-xs text-[#79766F] hidden';
    emptyState.innerHTML = '<i class="fa-solid fa-filter-circle-xmark text-sm mb-1 block text-[#79766F]/60"></i>Tidak ada pilihan yang cocok';
    optionsList.appendChild(emptyState);

    const optionElements = [];

    // 6. Build Option Items
    const buildOptions = () => {
        optionElements.forEach((el) => el.remove());
        optionElements.length = 0;

        Array.from(select.options).forEach((opt, index) => {
            const item = document.createElement('div');
            const isSelected = opt.selected;

            item.className = 'px-3 py-2 text-xs rounded-lg cursor-pointer flex items-center justify-between transition-colors ' +
                (isSelected ? 'bg-[#F7F6F2] font-bold text-[#161616]' : 'text-[#161616] hover:bg-[#F7F6F2]');
            item.setAttribute('role', 'option');
            item.setAttribute('data-value', opt.value);
            item.setAttribute('data-index', index);
            item.setAttribute('data-search', opt.text.toLowerCase().trim());

            const labelSpan = document.createElement('span');
            labelSpan.className = 'truncate pr-2';
            labelSpan.textContent = opt.text.trim();

            const checkIcon = document.createElement('i');
            checkIcon.className = 'fa-solid fa-check text-xs text-[#B89B5E] flex-shrink-0 ' + (isSelected ? '' : 'invisible');

            item.appendChild(labelSpan);
            item.appendChild(checkIcon);

            if (opt.disabled) {
                item.classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                item.addEventListener('click', (e) => {
                    e.stopPropagation();
                    selectOption(opt.value, opt.text.trim());
                });
            }

            optionsList.appendChild(item);
            optionElements.push(item);
        });
    };

    buildOptions();
    dropdown.appendChild(optionsList);
    wrapper.appendChild(dropdown);

    // 7. Filtering Functionality
    const filterOptions = (term) => {
        const query = term.toLowerCase().trim();
        let visibleCount = 0;

        optionElements.forEach((item) => {
            const text = item.getAttribute('data-search') || item.textContent.toLowerCase();
            if (text.includes(query)) {
                item.classList.remove('hidden');
                visibleCount++;
            } else {
                item.classList.add('hidden');
            }
        });

        if (visibleCount === 0) {
            emptyState.classList.remove('hidden');
        } else {
            emptyState.classList.add('hidden');
        }

        clearSearchBtn.classList.toggle('hidden', query === '');
    };

    searchInput.addEventListener('input', (e) => {
        filterOptions(e.target.value);
    });

    clearSearchBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        searchInput.value = '';
        filterOptions('');
        searchInput.focus();
    });

    // 8. Select Option Action
    const selectOption = (value, text) => {
        select.value = value;
        triggerText.textContent = text || '-- Pilih --';

        if (value) {
            triggerText.className = 'truncate pr-2 font-semibold text-[#161616]';
            trigger.classList.remove('border-[#991B1B]', 'ring-1', 'ring-[#991B1B]');
        } else {
            triggerText.className = 'truncate pr-2 text-[#79766F]';
        }

        // Update active checkmarks
        optionElements.forEach((item) => {
            const isMatch = item.getAttribute('data-value') === value;
            item.classList.toggle('bg-[#F7F6F2]', isMatch);
            item.classList.toggle('font-bold', isMatch);
            const check = item.querySelector('.fa-check');
            if (check) check.classList.toggle('invisible', !isMatch);
        });

        // Trigger native events for form listeners
        select.dispatchEvent(new Event('change', { bubbles: true }));
        select.dispatchEvent(new Event('input', { bubbles: true }));

        closeDropdown();
        trigger.focus();
    };

    // 9. Smart Viewport Positioning & Toggle
    const openDropdown = () => {
        // Close all other dropdowns
        document.querySelectorAll('.prop-searchable-select .dropdown-open').forEach((openEl) => {
            if (openEl !== dropdown) {
                openEl.classList.add('hidden');
                openEl.classList.remove('dropdown-open');
                const pWrapper = openEl.closest('.prop-searchable-select');
                if (pWrapper) {
                    const pTrigger = pWrapper.querySelector('button');
                    if (pTrigger) pTrigger.setAttribute('aria-expanded', 'false');
                    const pIcon = pWrapper.querySelector('.fa-chevron-down');
                    if (pIcon) pIcon.classList.remove('rotate-180');
                }
            }
        });

        // Check vertical space to open up or down
        const rect = trigger.getBoundingClientRect();
        const spaceBelow = window.innerHeight - rect.bottom;
        const spaceAbove = rect.top;

        if (spaceBelow < 260 && spaceAbove > spaceBelow) {
            dropdown.classList.remove('top-full', 'mt-1.5');
            dropdown.classList.add('bottom-full', 'mb-1.5');
        } else {
            dropdown.classList.remove('bottom-full', 'mb-1.5');
            dropdown.classList.add('top-full', 'mt-1.5');
        }

        dropdown.classList.remove('hidden');
        dropdown.classList.add('dropdown-open');
        trigger.setAttribute('aria-expanded', 'true');
        triggerIcon.classList.add('rotate-180');

        // Reset search
        searchInput.value = '';
        filterOptions('');

        setTimeout(() => {
            searchInput.focus();
        }, 40);
    };

    const closeDropdown = () => {
        dropdown.classList.add('hidden');
        dropdown.classList.remove('dropdown-open');
        trigger.setAttribute('aria-expanded', 'false');
        triggerIcon.classList.remove('rotate-180');
    };

    const toggleDropdown = () => {
        if (dropdown.classList.contains('hidden')) {
            openDropdown();
        } else {
            closeDropdown();
        }
    };

    trigger.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        if (!select.disabled) {
            toggleDropdown();
        }
    });

    // 10. Form Validation & Required Field Handling
    select.addEventListener('invalid', (e) => {
        e.preventDefault(); // Prevent browser error "An invalid form control is not focusable"
        trigger.classList.add('border-[#991B1B]', 'ring-1', 'ring-[#991B1B]');
        trigger.focus();
    });

    // 11. Keyboard Navigation
    dropdown.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeDropdown();
            trigger.focus();
        } else if (e.key === 'ArrowDown') {
            e.preventDefault();
            navigateOption(1);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            navigateOption(-1);
        } else if (e.key === 'Enter') {
            e.preventDefault();
            const activeItem = optionElements.find((el) => el.classList.contains('ring-1') && !el.classList.contains('hidden'));
            if (activeItem) {
                activeItem.click();
            } else {
                const firstVisible = optionElements.find((el) => !el.classList.contains('hidden'));
                if (firstVisible) firstVisible.click();
            }
        }
    });

    let highlightedIndex = -1;
    const navigateOption = (direction) => {
        const visibleElements = optionElements.filter((el) => !el.classList.contains('hidden'));
        if (visibleElements.length === 0) return;

        highlightedIndex = (highlightedIndex + direction + visibleElements.length) % visibleElements.length;

        optionElements.forEach((el) => el.classList.remove('ring-1', 'ring-[#B89B5E]', 'bg-[#F7F6F2]'));
        const target = visibleElements[highlightedIndex];
        if (target) {
            target.classList.add('ring-1', 'ring-[#B89B5E]', 'bg-[#F7F6F2]');
            target.scrollIntoView({ block: 'nearest' });
        }
    };

    // Close when clicking outside
    document.addEventListener('click', (e) => {
        if (!wrapper.contains(e.target)) {
            closeDropdown();
        }
    });

    // 12. Mutation Observer for dynamic <option> changes
    const observer = new MutationObserver(() => {
        buildOptions();
        const updated = getSelectedOption();
        if (updated) {
            triggerText.textContent = updated.text.trim();
            if (updated.value) {
                triggerText.className = 'truncate pr-2 font-semibold text-[#161616]';
            } else {
                triggerText.className = 'truncate pr-2 text-[#79766F]';
            }
        }
    });
    observer.observe(select, { childList: true });

    // Sync when select.value is modified programmatically
    select.addEventListener('change', () => {
        const selected = getSelectedOption();
        if (selected) {
            triggerText.textContent = selected.text.trim();
            triggerText.className = selected.value ? 'truncate pr-2 font-semibold text-[#161616]' : 'truncate pr-2 text-[#79766F]';
            trigger.classList.remove('border-[#991B1B]', 'ring-1', 'ring-[#991B1B]');
            optionElements.forEach((item) => {
                const isMatch = item.getAttribute('data-value') === select.value;
                item.classList.toggle('bg-[#F7F6F2]', isMatch);
                item.classList.toggle('font-bold', isMatch);
                const check = item.querySelector('.fa-check');
                if (check) check.classList.toggle('invisible', !isMatch);
            });
        }
    });
}
