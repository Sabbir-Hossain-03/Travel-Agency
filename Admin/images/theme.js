(function () {
    function applyStoredTheme() {
        const theme = localStorage.getItem('theme') === 'light' ? 'light' : 'dark';
        document.body.classList.toggle('light-mode', theme === 'light');
        document.body.classList.toggle('dark-mode', theme !== 'light');
        return theme;
    }

    function wireThemeToggle(buttonId) {
        const btn = document.getElementById(buttonId);
        if (!btn) return;
        const setIcon = (theme) => {
            btn.textContent = theme === 'light' ? '☀️' : '🌙';
        };
        let current = applyStoredTheme();
        setIcon(current);
        btn.addEventListener('click', function () {
            current = current === 'light' ? 'dark' : 'light';
            localStorage.setItem('theme', current);
            applyStoredTheme();
            setIcon(current);
        });
    }


    window.applyStoredTheme = applyStoredTheme;
    window.wireThemeToggle = wireThemeToggle;


    applyStoredTheme();
})();
