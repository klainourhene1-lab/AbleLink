// Theme Toggle - Dark Mode / Light Mode
// Saves preference to localStorage

const THEME_KEY = 'abelink-theme';

// Initialize theme on page load
function initializeTheme() {
    const savedTheme = localStorage.getItem(THEME_KEY) || 'dark';
    setTheme(savedTheme);
}

// Set theme
function setTheme(theme) {
    document.documentElement.setAttribute('data-theme', theme);
    localStorage.setItem(THEME_KEY, theme);

    // Update toggle button icon
    const toggleBtn = document.getElementById('themeToggle');
    if (toggleBtn) {
        const icon = toggleBtn.querySelector('i');
        if (icon) {
            if (theme === 'dark') {
                icon.className = 'fa fa-sun-o';
                toggleBtn.setAttribute('aria-label', 'Passer en mode clair');
            } else {
                icon.className = 'fa fa-moon-o';
                toggleBtn.setAttribute('aria-label', 'Passer en mode sombre');
            }
        }
    }
}

// Toggle theme
function toggleTheme() {
    const currentTheme = document.documentElement.getAttribute('data-theme') || 'dark';
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    setTheme(newTheme);
}

// Initialize on DOM load
document.addEventListener('DOMContentLoaded', initializeTheme);

// Export functions
window.toggleTheme = toggleTheme;
window.setTheme = setTheme;
