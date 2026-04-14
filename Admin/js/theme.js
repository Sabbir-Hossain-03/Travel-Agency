
function applyStoredTheme() {
    const savedTheme = localStorage.getItem('theme') || 'dark';
    applyTheme(savedTheme);
    return savedTheme;
}

// Apply theme by toggling body class
function applyTheme(theme) {
    localStorage.setItem('theme', theme);
    document.documentElement.setAttribute('data-theme', theme);

    if (theme === 'light') {
        document.body.classList.add('light-mode');
        document.body.classList.remove('dark-mode');
    } else {
        document.body.classList.add('dark-mode');
        document.body.classList.remove('light-mode');
    }

    updateToggleButton(theme);

    // Sync with server session
    const formData = new FormData();
    formData.append('form_type', 'general');
    formData.append('site_theme', theme);
    formData.append('ajax', 'true');

    // Detect if we are in User branch or Admin branch to use correct controller
    const isAdmin = window.location.pathname.toLowerCase().includes('/admin/');
    const controllerPath = isAdmin ? '../controller/SettingsController.php' : '../controller/ThemeSyncController.php';

    fetch(controllerPath, {
        method: 'POST',
        body: formData
    })
        .catch(error => console.warn('AJAX theme sync skipped or failed (unauthorized or network error)'));
}

//  toggle button
function wireThemeToggle(buttonId) {
    const toggleBtn = document.getElementById(buttonId);

    if (!toggleBtn) return;

    toggleBtn.addEventListener('click', function () {
        const currentTheme = localStorage.getItem('theme') || 'dark';
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        applyTheme(newTheme);
    });
}

// Update button appearance
function updateToggleButton(theme) {
    const toggleBtn = document.getElementById('mode-toggle') || document.getElementById('user-theme-toggle');
    if (!toggleBtn) return;

    if (theme === 'dark') {
        toggleBtn.innerHTML = '☀️';
        toggleBtn.title = 'Switch to Light Mode';
    } else {
        toggleBtn.innerHTML = '🌙';
        toggleBtn.title = 'Switch to Dark Mode';
    }
}
