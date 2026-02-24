/**
 * Dark Mode - Design System 2026
 *
 * Uses Tailwind's 'class' strategy (dark class on <html>).
 * Persists choice to localStorage.
 * Falls back to system preference.
 */

/**
 * Initialize dark mode based on saved preference or system setting
 */
export function initDarkMode() {
    const savedTheme = localStorage.getItem('theme');
    const systemDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

    if (savedTheme === 'dark' || (!savedTheme && systemDark)) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }

    // Listen for system preference changes (only when no manual override)
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
        if (!localStorage.getItem('theme')) {
            if (e.matches) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        }
    });
}

/**
 * Toggle dark mode and persist preference
 * @returns {boolean} true if dark mode is now active
 */
window.toggleDarkMode = function () {
    const isDark = document.documentElement.classList.toggle('dark');
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
    return isDark;
};

/**
 * Check if dark mode is active
 * @returns {boolean}
 */
window.isDarkMode = function () {
    return document.documentElement.classList.contains('dark');
};

// Initialize on load
initDarkMode();
