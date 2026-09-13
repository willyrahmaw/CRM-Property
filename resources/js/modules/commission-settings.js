/**
 * Live Commission Simulation Calculator for Company Owner Settings
 */
export function initCommissionSettings() {
    const form = document.getElementById('commission-settings-form');
    if (!form) return;

    const totalRateInput = document.getElementById('input-total-rate');
    const salesShareInput = document.getElementById('input-sales-share');
    const tlShareInput = document.getElementById('input-tl-share');
    const agencyShareInput = document.getElementById('input-agency-share');
    const simulationPriceInput = document.getElementById('input-sim-price');

    // Display elements
    const simTotalCommEl = document.getElementById('sim-total-comm');
    const simSalesCommEl = document.getElementById('sim-sales-comm');
    const simSalesEffEl = document.getElementById('sim-sales-effective');
    const simTlCommEl = document.getElementById('sim-tl-comm');
    const simTlEffEl = document.getElementById('sim-tl-effective');
    const simAgencyCommEl = document.getElementById('sim-agency-comm');
    const simAgencyEffEl = document.getElementById('sim-agency-effective');
    const totalShareSumEl = document.getElementById('total-share-sum');
    const totalShareBadgeEl = document.getElementById('total-share-badge');

    function formatRupiah(num) {
        return 'Rp ' + Math.round(num).toLocaleString('id-ID');
    }

    function updateSimulation() {
        const totalRate = parseFloat(totalRateInput?.value) || 0;
        const salesShare = parseFloat(salesShareInput?.value) || 0;
        const tlShare = parseFloat(tlShareInput?.value) || 0;
        const agencyShare = parseFloat(agencyShareInput?.value) || 0;
        const simPrice = parseFloat(simulationPriceInput?.value) || 0;

        // Calculate total share sum
        const sumShares = Math.round((salesShare + tlShare + agencyShare) * 100) / 100;
        if (totalShareSumEl) {
            totalShareSumEl.textContent = sumShares + '%';
        }

        if (totalShareBadgeEl) {
            if (Math.abs(sumShares - 100) < 0.01) {
                totalShareBadgeEl.className = 'px-2 py-0.5 rounded text-[10px] font-bold bg-[#15803D] text-white';
                totalShareBadgeEl.textContent = 'Tepat 100%';
            } else {
                totalShareBadgeEl.className = 'px-2 py-0.5 rounded text-[10px] font-bold bg-[#991B1B] text-white';
                totalShareBadgeEl.textContent = 'Harus 100%';
            }
        }

        // Calculate values
        const totalCommAmount = (simPrice * totalRate) / 100;
        const salesAmount = (totalCommAmount * salesShare) / 100;
        const tlAmount = (totalCommAmount * tlShare) / 100;
        const agencyAmount = (totalCommAmount * agencyShare) / 100;

        const salesEffective = (totalRate * salesShare) / 100;
        const tlEffective = (totalRate * tlShare) / 100;
        const agencyEffective = (totalRate * agencyShare) / 100;

        if (simTotalCommEl) simTotalCommEl.textContent = formatRupiah(totalCommAmount);
        if (simSalesCommEl) simSalesCommEl.textContent = formatRupiah(salesAmount);
        if (simSalesEffEl) simSalesEffEl.textContent = salesEffective.toFixed(2) + '% dari harga';

        if (simTlCommEl) simTlCommEl.textContent = formatRupiah(tlAmount);
        if (simTlEffEl) simTlEffEl.textContent = tlEffective.toFixed(2) + '% dari harga';

        if (simAgencyCommEl) simAgencyCommEl.textContent = formatRupiah(agencyAmount);
        if (simAgencyEffEl) simAgencyEffEl.textContent = agencyEffective.toFixed(2) + '% dari harga';
    }

    [totalRateInput, salesShareInput, tlShareInput, agencyShareInput, simulationPriceInput].forEach(el => {
        if (el) {
            el.addEventListener('input', updateSimulation);
            el.addEventListener('change', updateSimulation);
        }
    });

    // Run initial update
    updateSimulation();
}
