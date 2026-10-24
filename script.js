/**
 * WT Assignment 1 – Electricity Bill Calculator
 * Client-side interactivity
 */

'use strict';

// ── Slider <-> Number input sync ──────────────────────────────────
const unitsInput  = document.getElementById('units');
const unitsSlider = document.getElementById('unitsSlider');

if (unitsInput && unitsSlider) {
    // Slider → Input
    unitsSlider.addEventListener('input', () => {
        unitsInput.value = unitsSlider.value;
        animateInputFocus(unitsInput);
    });

    // Input → Slider
    unitsInput.addEventListener('input', () => {
        const val = parseFloat(unitsInput.value);
        if (!isNaN(val) && val >= 0 && val <= 500) {
            unitsSlider.value = Math.round(val);
        }
    });
}

function animateInputFocus(el) {
    el.classList.add('input-flash');
    setTimeout(() => el.classList.remove('input-flash'), 300);
}

// ── Live bill preview while user types ───────────────────────────
const SLABS = [
    { limit: 50,       rate: 3.50 },
    { limit: 100,      rate: 4.00 },
    { limit: 100,      rate: 5.20 },
    { limit: Infinity, rate: 6.50 },
];

function calculateBill(units) {
    let remaining = units;
    let total = 0;
    for (const slab of SLABS) {
        if (remaining <= 0) break;
        const consumed = Math.min(remaining, slab.limit);
        total += consumed * slab.rate;
        remaining -= consumed;
    }
    return total;
}

// Live preview label
let previewEl = null;

function getOrCreatePreview() {
    if (!previewEl) {
        previewEl = document.createElement('div');
        previewEl.id = 'livePreview';
        previewEl.className = 'live-preview';
        unitsInput.closest('.form-group').appendChild(previewEl);
    }
    return previewEl;
}

if (unitsInput) {
    unitsInput.addEventListener('input', () => {
        const val = parseFloat(unitsInput.value);
        const preview = getOrCreatePreview();

        if (!isNaN(val) && val >= 0) {
            const bill = calculateBill(val);
            preview.textContent = `Estimated Bill: ₹ ${bill.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
            preview.classList.add('visible');
        } else {
            preview.classList.remove('visible');
        }
    });
}

// ── Form Validation ───────────────────────────────────────────────
const form    = document.getElementById('billForm');
const calcBtn = document.getElementById('calcBtn');

if (form) {
    form.addEventListener('submit', (e) => {
        const val = parseFloat(unitsInput?.value ?? '');

        if (isNaN(val) || val < 0) {
            e.preventDefault();
            showInlineError('Please enter a valid positive number of units.');
            unitsInput?.focus();
            return;
        }

        // Loading state on button
        if (calcBtn) {
            calcBtn.disabled = true;
            calcBtn.querySelector('.btn-text').textContent = 'Calculating…';
            calcBtn.querySelector('.btn-icon').textContent = '⏳';
        }
    });
}

function showInlineError(msg) {
    let errEl = document.getElementById('inlineError');
    if (!errEl) {
        errEl = document.createElement('div');
        errEl.id = 'inlineError';
        errEl.className = 'alert alert-error';
        errEl.style.marginTop = '0.75rem';
        errEl.innerHTML = `<span class="alert-icon">⚠️</span><span class="err-msg"></span>`;
        unitsInput?.closest('.form-group')?.insertAdjacentElement('afterend', errEl);
    }
    errEl.querySelector('.err-msg').textContent = msg;
    errEl.style.display = 'flex';

    // Flash animation
    errEl.animate(
        [{ transform: 'translateX(-6px)' }, { transform: 'translateX(6px)' },
         { transform: 'translateX(-4px)' }, { transform: 'translateX(4px)' },
         { transform: 'translateX(0)' }],
        { duration: 350, easing: 'ease-in-out' }
    );
}

// ── Inject live-preview CSS ───────────────────────────────────────
(function injectStyles() {
    const style = document.createElement('style');
    style.textContent = `
        .live-preview {
            margin-top: 0.55rem;
            font-size: 0.85rem;
            font-weight: 600;
            color: #a5b4fc;
            opacity: 0;
            transform: translateY(-4px);
            transition: opacity 0.25s ease, transform 0.25s ease;
            pointer-events: none;
        }
        .live-preview.visible {
            opacity: 1;
            transform: translateY(0);
        }
        .input-flash {
            border-color: #6366f1 !important;
            box-shadow: 0 0 0 3px rgba(99,102,241,0.2) !important;
        }
    `;
    document.head.appendChild(style);
})();

// ── Scroll to result if present ───────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    const result = document.getElementById('result');
    if (result) {
        setTimeout(() => {
            result.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }, 180);
    }
});
