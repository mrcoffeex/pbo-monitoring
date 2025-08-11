/** Tailwind CSS configuration (adds safelist for dynamically applied classes) */
export default {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
    './vendor/filament/**/*.blade.php',
    './storage/framework/views/*.php',
  ],
  darkMode: 'class',
  safelist: [
    // Standard color utilities
    'text-emerald-600','dark:text-emerald-400',
    'text-indigo-600','dark:text-indigo-400',
    'text-red-500','dark:text-red-400',
    'text-slate-800','dark:text-slate-200',
    'text-green-800','dark:text-green-200',
    // Important variants (if needed)
    '!text-emerald-600','dark:!text-emerald-400',
    '!text-indigo-600','dark:!text-indigo-400',
    '!text-red-500','dark:!text-red-400',
    '!text-slate-800','dark:!text-slate-200',
    '!text-green-800','dark:!text-green-200',
  ],
  theme: {
    screens: {
      'xs': '420px',      // small phones (custom)
      'sm': '640px',      // default small
      'md': '768px',      // tablets
      'lg': '1024px',     // small laptops
      'xl': '1280px',     // desktops
      '2xl': '1536px',    // large desktops
    },
    extend: {
      container: {
        center: true,
        padding: {
          DEFAULT: '1rem',
          sm: '1.25rem',
          lg: '2rem',
          xl: '2.5rem',
          '2xl': '3rem',
        },
      },
    }
  },
  plugins: [require('@tailwindcss/forms')],
};
