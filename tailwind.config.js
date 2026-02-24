import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    darkMode: 'class',

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'sans-serif'],
                display: ['Oswald', 'Inter', '-apple-system', 'sans-serif'],
                serif: ['Playfair Display', 'Georgia', 'Cambria', 'Times New Roman', 'serif'],
            },
            colors: {
                // Legacy compatibility
                'abs-primary': '#1A1A2E',
                'brand-color': '#2D5016',
                'abs-secondary': '#8B6914',
                'accent-color': '#E85D04',
                'abs-text': '#1A1A2E',
                'abs-bg': '#F5F1E8',
                'admin-teal': '#2D6069',

                // Chains for Hebb palette
                'earth-primary': '#2D5016',
                'earth-green': '#2D5016',
                'earth-rose': '#E85D04',
                'earth-success': '#2D8B46',
                'earth-amber': '#8B6914',
                'earth-sage': '#6B7280',
                'earth-copper': '#E85D04',

                // Semantic
                'forest': '#2D5016',
                'gold': '#8B6914',
                'disc-orange': '#E85D04',
                'night': '#1A1A2E',
                'parchment': '#F5F1E8',
                'success': '#2D8B46',
            },
            boxShadow: {
                'glass-sm': '0 2px 8px rgba(0, 0, 0, 0.06)',
                'glass': '0 8px 32px rgba(0, 0, 0, 0.08)',
                'glass-lg': '0 16px 48px rgba(0, 0, 0, 0.12)',
                'glow-accent': '0 0 30px rgba(232, 93, 4, 0.3)',
            },
            backdropBlur: {
                xs: '2px',
                glass: '16px',
            },
        },
    },

    plugins: [forms],
};
