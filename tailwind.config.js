/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
  ],
  theme: {
    extend: {
        fontFamily: {
            cinzel: ['Cinzel', 'serif'],
        },
    },
  },
  plugins: [],
};
