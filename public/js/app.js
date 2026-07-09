/**
 * Mini Cookpad - Main JavaScript
 * Handles: wizard navigation, dynamic form rows, countdown timer,
 * bookmark toggle (AJAX), form validation, and mobile menu.
 */

(function () {
    'use strict';

    // ============================================================
    // 1. Chef Verify Wizard Step Navigation
    // ============================================================

    window.mcWizardGoToStep = function (step) {
        const panels = document.querySelectorAll('.mc-wizard-panel');
        const steps = document.querySelectorAll('.mc-wizard-step');
        const lines = document.querySelectorAll('.mc-wizard-line');

        if (!panels.length) return;

        // Find current step
        let currentStep = 1;
        panels.forEach(function (panel, idx) {
            if (panel.style.display !== 'none') {
                currentStep = idx + 1;
            }
        });

        // Validate current step before moving forward
        if (step > currentStep) {
            if (!mcValidateCurrentStep(currentStep)) return;
        }

        // Hide all panels, show target
        panels.forEach(function (panel) {
            panel.style.display = 'none';
        });
        var targetPanel = document.getElementById('mcStep' + step);
        if (targetPanel) targetPanel.style.display = '';

        // Update step indicators
        steps.forEach(function (el, idx) {
            var s = idx + 1;
            el.classList.remove('active', 'done');
            if (s < step) el.classList.add('done');
            if (s === step) el.classList.add('active');
        });

        // Update lines
        lines.forEach(function (line, idx) {
            if (idx + 1 < step) {
                line.classList.add('done');
            } else {
                line.classList.remove('done');
            }
        });

        // Update review summary on last step
        if (step === 4 && typeof mcUpdateReview === 'function') {
            mcUpdateReview();
        }

        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    function mcValidateCurrentStep(step) {
        var panel = document.getElementById('mcStep' + step);
        if (!panel) return true;

        var requiredFields = panel.querySelectorAll('[required]');
        for (var i = 0; i < requiredFields.length; i++) {
            var field = requiredFields[i];
            if (!field.value || !field.value.trim()) {
                field.focus();
                field.style.borderColor = 'var(--mc-red)';
                alert('Mohon lengkapi field yang wajib diisi');
                return false;
            }
            field.style.borderColor = '';
        }
        return true;
    }

    // Make available globally for inline onclick handlers
    window.mcGoToStep = window.mcWizardGoToStep;

    // ============================================================
    // 2. Recipe Creation - Dynamic Ingredients & Steps
    // ============================================================

    var ingredientCount = 0;
    var stepCount = 0;

    // Count existing rows on page load
    function initDynamicRows() {
        var existingIngredients = document.querySelectorAll('.mc-ingredient-row');
        ingredientCount = existingIngredients.length;

        var existingSteps = document.querySelectorAll('.mc-step-row');
        stepCount = existingSteps.length;
    }

    window.mcAddIngredient = function () {
        var container = document.getElementById('mcIngredientsList');
        if (!container) return;

        var idx = ingredientCount++;
        var row = document.createElement('div');
        row.className = 'mc-ingredient-row';
        row.style.cssText = 'display:flex;gap:0.5rem;align-items:flex-start;margin-bottom:0.5rem';
        row.innerHTML =
            '<input type="text" name="ingredients[' + idx + '][name]" class="mc-input" placeholder="Nama bahan" style="flex:2">' +
            '<input type="text" name="ingredients[' + idx + '][amount]" class="mc-input" placeholder="Jumlah" style="flex:1">' +
            '<input type="text" name="ingredients[' + idx + '][unit]" class="mc-input" placeholder="Satuan" style="flex:1">' +
            '<button type="button" class="mc-btn mc-btn-danger mc-btn-sm" onclick="mcRemoveRow(this)" style="flex-shrink:0">✕</button>';
        container.appendChild(row);
        row.querySelector('input').focus();
    };

    window.mcAddStep = function () {
        var container = document.getElementById('mcStepsList');
        if (!container) return;

        var idx = stepCount++;
        var num = container.querySelectorAll('.mc-step-row').length + 1;
        var row = document.createElement('div');
        row.className = 'mc-step-row';
        row.style.cssText = 'display:flex;gap:0.75rem;align-items:flex-start;margin-bottom:0.75rem;padding:1rem;background:var(--mc-muted);border-radius:8px';
        row.innerHTML =
            '<div style="width:2rem;height:2rem;background:var(--mc-green);color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.8125rem;flex-shrink:0" class="mc-step-number">' + num + '</div>' +
            '<div style="flex:1">' +
            '<textarea name="steps[' + idx + '][description]" class="mc-textarea" placeholder="Jelaskan langkah ini..." rows="2" style="min-height:60px"></textarea>' +
            '<input type="text" name="steps[' + idx + '][tip]" class="mc-input" placeholder="💡 Tips (opsional)" style="margin-top:0.5rem">' +
            '</div>' +
            '<button type="button" class="mc-btn mc-btn-danger mc-btn-sm" onclick="mcRemoveRow(this)" style="flex-shrink:0">✕</button>';
        container.appendChild(row);
        row.querySelector('textarea').focus();
    };

    window.mcRemoveRow = function (btn) {
        var row = btn.closest('.mc-ingredient-row, .mc-step-row');
        if (row) {
            row.remove();
            mcReindexStepNumbers();
        }
    };

    function mcReindexStepNumbers() {
        var steps = document.querySelectorAll('#mcStepsList .mc-step-row');
        steps.forEach(function (row, idx) {
            var numEl = row.querySelector('.mc-step-number');
            if (numEl) numEl.textContent = idx + 1;
        });
    }

    // ============================================================
    // 3. Payment Countdown Timer
    // ============================================================

    function initCountdownTimer() {
        var countdownEl = document.getElementById('mcCountdownTime');
        if (!countdownEl) return;

        var expiresAtStr = countdownEl.getAttribute('data-expires-at');
        if (!expiresAtStr) return;

        var expiresAt = new Date(expiresAtStr).getTime();

        function updateCountdown() {
            var now = new Date().getTime();
            var distance = expiresAt - now;

            if (distance <= 0) {
                countdownEl.textContent = '00:00:00';
                var wrapper = document.getElementById('mcCountdown');
                if (wrapper) wrapper.style.borderColor = 'var(--mc-red)';
                // Reload after short delay to reflect expired state
                setTimeout(function () { location.reload(); }, 2000);
                return;
            }

            var hours = Math.floor(distance / (1000 * 60 * 60));
            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((distance % (1000 * 60)) / 1000);

            countdownEl.textContent =
                String(hours).padStart(2, '0') + ':' +
                String(minutes).padStart(2, '0') + ':' +
                String(seconds).padStart(2, '0');

            // Red warning when under 1 hour
            if (distance < 3600000) {
                var wrapper = document.getElementById('mcCountdown');
                if (wrapper) wrapper.style.borderColor = 'var(--mc-red)';
                countdownEl.style.color = 'var(--mc-red)';
            }
        }

        updateCountdown();
        setInterval(updateCountdown, 1000);
    }

    // ============================================================
    // 4. Bookmark Toggle (AJAX)
    // ============================================================

    window.mcToggleBookmark = function (recipeId, btn) {
        if (!recipeId) return;

        var form = document.createElement('form');
        form.method = 'POST';
        form.action = '/bookmark/toggle/' + recipeId;
        document.body.appendChild(form);
        form.submit();
    };

    // AJAX-based bookmark toggle (for enhanced UX)
    window.mcToggleBookmarkAjax = function (recipeId, btn) {
        if (!recipeId) return;

        var xhr = new XMLHttpRequest();
        xhr.open('POST', '/bookmark/toggle/' + recipeId, true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    // Toggle button state
                    if (btn) {
                        var isBookmarked = btn.classList.contains('mc-bookmarked');
                        if (isBookmarked) {
                            btn.classList.remove('mc-bookmarked');
                            btn.innerHTML = '🔖 Simpan';
                            btn.title = 'Simpan resep';
                        } else {
                            btn.classList.add('mc-bookmarked');
                            btn.innerHTML = '✅ Tersimpan';
                            btn.title = 'Hapus dari simpanan';
                        }
                    }
                } else {
                    // Fallback to full page reload
                    window.location.href = '/bookmark/toggle/' + recipeId;
                }
            }
        };

        xhr.send();
    };

    // ============================================================
    // 5. Form Validation Helpers
    // ============================================================

    window.mcValidateForm = function (formEl) {
        if (!formEl) return true;

        var isValid = true;
        var requiredFields = formEl.querySelectorAll('[required]');

        requiredFields.forEach(function (field) {
            // Reset border
            field.style.borderColor = '';

            if (!field.value || !field.value.trim()) {
                field.style.borderColor = 'var(--mc-red)';
                isValid = false;
            }

            // Email validation
            if (field.type === 'email' && field.value) {
                var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(field.value)) {
                    field.style.borderColor = 'var(--mc-red)';
                    isValid = false;
                }
            }

            // Min length check
            var minLength = field.getAttribute('minlength');
            if (minLength && field.value && field.value.length < parseInt(minLength)) {
                field.style.borderColor = 'var(--mc-red)';
                isValid = false;
            }
        });

        if (!isValid) {
            var firstError = formEl.querySelector('[style*="border-color: var(--mc-red)"]');
            if (firstError) firstError.focus();
        }

        return isValid;
    };

    // Auto-clear validation styling on input
    document.addEventListener('input', function (e) {
        if (e.target.style.borderColor) {
            e.target.style.borderColor = '';
        }
    });

    // Confirm before destructive actions
    window.mcConfirm = function (message) {
        return confirm(message || 'Apakah Anda yakin?');
    };

    // Show file name on upload
    window.mcShowFileName = function (input, targetId) {
        var target = document.getElementById(targetId);
        if (target && input.files && input.files[0]) {
            target.textContent = '✓ ' + input.files[0].name;
        }
    };

    // Copy to clipboard
    window.mcCopyToClipboard = function (text, message) {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(text).then(function () {
                alert(message || 'Berhasil disalin!');
            });
        } else {
            // Fallback
            var textarea = document.createElement('textarea');
            textarea.value = text;
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
            alert(message || 'Berhasil disalin!');
        }
    };

    // ============================================================
    // 6. Mobile Menu Toggle
    // ============================================================

    function initMobileMenu() {
        var nav = document.querySelector('.mc-nav');
        if (!nav) return;

        // Create hamburger button
        var hamburger = document.createElement('button');
        hamburger.className = 'mc-mobile-toggle';
        hamburger.innerHTML = '☰';
        hamburger.setAttribute('aria-label', 'Toggle menu');
        hamburger.style.cssText = 'display:none;background:none;border:none;font-size:1.5rem;cursor:pointer;color:var(--mc-dark);padding:0.5rem';

        var header = document.querySelector('.mc-header-inner');
        if (header) {
            header.insertBefore(hamburger, nav);
        }

        // Toggle nav on click
        hamburger.addEventListener('click', function () {
            nav.style.display = nav.style.display === 'flex' ? 'none' : 'flex';
            if (nav.style.display === 'flex') {
                nav.style.flexDirection = 'column';
                nav.style.position = 'absolute';
                nav.style.top = '64px';
                nav.style.left = '0';
                nav.style.right = '0';
                nav.style.background = 'white';
                nav.style.padding = '1rem';
                nav.style.borderBottom = '1px solid var(--mc-border)';
                nav.style.boxShadow = '0 4px 6px rgba(0,0,0,0.1)';
                nav.style.zIndex = '99';
            }
        });

        // Responsive: show hamburger on mobile, hide on desktop
        function checkMobile() {
            if (window.innerWidth <= 768) {
                hamburger.style.display = 'block';
                nav.style.display = 'none';
            } else {
                hamburger.style.display = 'none';
                nav.style.display = 'flex';
                nav.style.flexDirection = '';
                nav.style.position = '';
                nav.style.top = '';
                nav.style.left = '';
                nav.style.right = '';
                nav.style.background = '';
                nav.style.padding = '';
                nav.style.borderBottom = '';
                nav.style.boxShadow = '';
                nav.style.zIndex = '';
            }
        }

        checkMobile();
        window.addEventListener('resize', checkMobile);
    }

    // ============================================================
    // Initialization
    // ============================================================

    document.addEventListener('DOMContentLoaded', function () {
        initDynamicRows();
        initCountdownTimer();
        initMobileMenu();
    });

})();
