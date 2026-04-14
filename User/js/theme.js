
function applyStoredTheme() {
    const savedTheme = localStorage.getItem('theme') || 'light';
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
    const toggleBtn = document.getElementById('mode-toggle');
    if (!toggleBtn) return;

    toggleBtn.textContent = theme === 'dark' ? '☀️' : '🌙';
}
