// Settings page functionality
document.addEventListener('DOMContentLoaded', function () {
    // Initialize settings page
    initializeSettings();
});

// Initialize settings functionality
function initializeSettings() {
    // Sync the theme dropdown with current state if it exists
    const themeSelect = document.querySelector('#site-theme');
    if (themeSelect) {
        const currentTheme = document.body.classList.contains('dark-mode') ? 'dark' : 'light';
        themeSelect.value = currentTheme;
    }

    // Add event listeners for form submissions
    const settingsForms = document.querySelectorAll('.settings-form');

    settingsForms.forEach(form => {
        form.addEventListener('submit', function (e) {
            const submitBtn = form.querySelector('.save-settings-btn');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Saving...';
            }
        });
    });

    // Show alerts
    showAlerts();
}

// Show success and error alerts
function showAlerts() {
    const alertSuccess = document.querySelector('.alert-success');
    const alertError = document.querySelector('.alert-error');

    if (alertSuccess) {
        alertSuccess.style.display = 'block';
        setTimeout(() => {
            alertSuccess.style.opacity = '1';
        }, 50);

        // Auto hide after 6 seconds with fade animation
        setTimeout(() => {
            alertSuccess.style.opacity = '0';
            alertSuccess.style.transform = 'translateY(-20px)';
            setTimeout(() => {
                alertSuccess.style.display = 'none';
            }, 400);
        }, 6000);
    }

    if (alertError) {
        alertError.style.display = 'block';
        setTimeout(() => {
            alertError.style.opacity = '1';
        }, 50);

        // Auto hide after 6 seconds with fade animation
        setTimeout(() => {
            alertError.style.opacity = '0';
            alertError.style.transform = 'translateY(-20px)';
            setTimeout(() => {
                alertError.style.display = 'none';
            }, 400);
        }, 6000);
    }
}

