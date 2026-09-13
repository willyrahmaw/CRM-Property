/**
 * Booking module for customer selection, auto-population, and financial simulation.
 */
export function initBookingForm() {
    const customerSelect = document.getElementById('booking-customer-select');
    const leadSelect = document.getElementById('booking-lead-select');

    const nameInput = document.getElementById('booking-customer-name');
    const phoneInput = document.getElementById('booking-customer-phone');
    const nikInput = document.getElementById('booking-customer-nik');
    const emailInput = document.getElementById('booking-customer-email');

    const infoBanner = document.getElementById('booking-selected-info');
    const infoName = document.getElementById('booking-selected-name');
    const infoPhone = document.getElementById('booking-selected-phone');

    const nameMark = document.getElementById('customer-name-required-mark');
    const phoneMark = document.getElementById('customer-phone-required-mark');

    function updateFromOption(selectElement) {
        if (!selectElement) return;

        const selectedOption = selectElement.options[selectElement.selectedIndex];
        if (selectedOption && selectedOption.value) {
            const name = selectedOption.getAttribute('data-name') || '';
            const phone = selectedOption.getAttribute('data-phone') || '';
            const email = selectedOption.getAttribute('data-email') || '';
            const nik = selectedOption.getAttribute('data-nik') || '';

            if (nameInput) nameInput.value = name;
            if (phoneInput) phoneInput.value = phone;
            if (emailInput && email) emailInput.value = email;
            if (nikInput && nik) nikInput.value = nik;

            if (infoBanner && infoName && infoPhone) {
                infoName.textContent = name;
                infoPhone.textContent = phone;
                infoBanner.classList.remove('hidden');
            }

            if (nameMark) nameMark.classList.add('hidden');
            if (phoneMark) phoneMark.classList.add('hidden');
        } else {
            // Check if other select is active
            const otherSelect = selectElement === customerSelect ? leadSelect : customerSelect;
            if (!otherSelect || !otherSelect.value) {
                if (infoBanner) infoBanner.classList.add('hidden');
                if (nameMark) nameMark.classList.remove('hidden');
                if (phoneMark) phoneMark.classList.remove('hidden');
            }
        }
    }

    if (customerSelect) {
        customerSelect.addEventListener('change', () => {
            if (customerSelect.value && leadSelect) {
                leadSelect.value = '';
            }
            updateFromOption(customerSelect);
        });
    }

    if (leadSelect) {
        leadSelect.addEventListener('change', () => {
            if (leadSelect.value && customerSelect) {
                customerSelect.value = '';
            }
            updateFromOption(leadSelect);
        });
    }

    // Initialize state on page load if pre-selected
    if (customerSelect && customerSelect.value) {
        updateFromOption(customerSelect);
    } else if (leadSelect && leadSelect.value) {
        updateFromOption(leadSelect);
    }
}
