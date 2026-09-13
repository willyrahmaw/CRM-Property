document.addEventListener('DOMContentLoaded', () => {
    const siteplanContainer = document.getElementById('siteplan-interactive-wrapper');
    const unitDrawer = document.getElementById('unit-detail-drawer');

    if (!siteplanContainer || !unitDrawer) {
        return;
    }

    const drawerUnitNumber = document.getElementById('drawer-unit-number');
    const drawerCluster = document.getElementById('drawer-cluster');
    const drawerType = document.getElementById('drawer-type');
    const drawerPrice = document.getElementById('drawer-price');
    const drawerBuilding = document.getElementById('drawer-building');
    const drawerLand = document.getElementById('drawer-land');
    const drawerBedrooms = document.getElementById('drawer-bedrooms');
    const drawerStatus = document.getElementById('drawer-status');
    const drawerCustomerInfo = document.getElementById('drawer-customer-info');
    const drawerCustomerName = document.getElementById('drawer-customer-name');
    const drawerBookingBtn = document.getElementById('drawer-booking-btn');

    // Unit element click handler
    siteplanContainer.addEventListener('click', (event) => {
        const unitEl = event.target.closest('[data-unit-id]');
        if (!unitEl) return;

        const unitId = unitEl.getAttribute('data-unit-id');
        const fetchUrl = unitEl.getAttribute('data-detail-url');

        fetch(fetchUrl, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (drawerUnitNumber) drawerUnitNumber.textContent = `Unit ${data.unit_number} (Blok ${data.block || '-'})`;
            if (drawerCluster) drawerCluster.textContent = data.cluster_name;
            if (drawerType) drawerType.textContent = data.type_name;
            if (drawerPrice) drawerPrice.textContent = data.formatted_price;
            if (drawerBuilding) drawerBuilding.textContent = `${data.building_area} m²`;
            if (drawerLand) drawerLand.textContent = `${data.land_area} m²`;
            if (drawerBedrooms) drawerBedrooms.textContent = `${data.bedrooms} KT / ${data.bathrooms} KM`;

            if (drawerStatus) {
                drawerStatus.textContent = data.status_label;
                drawerStatus.style.backgroundColor = data.status_color;
                drawerStatus.style.color = '#FFFFFF';
            }

            if (data.status === 'available') {
                if (drawerCustomerInfo) drawerCustomerInfo.classList.add('hidden');
                if (drawerBookingBtn) {
                    drawerBookingBtn.classList.remove('hidden');
                    drawerBookingBtn.href = `/sales/bookings/create?unit_id=${data.id}`;
                }
            } else {
                if (drawerCustomerInfo) {
                    drawerCustomerInfo.classList.remove('hidden');
                    if (drawerCustomerName) {
                        drawerCustomerName.textContent = data.customer_name || 'Customer Terdata';
                    }
                }
                if (drawerBookingBtn) {
                    drawerBookingBtn.classList.add('hidden');
                }
            }

            unitDrawer.classList.remove('hidden');
        })
        .catch(err => {
            console.error('Failed to load unit detail:', err);
        });
    });

    // Close drawer button
    const closeDrawerBtn = document.getElementById('close-drawer-btn');
    if (closeDrawerBtn) {
        closeDrawerBtn.addEventListener('click', () => {
            unitDrawer.classList.add('hidden');
        });
    }
});
