export default {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
    './node_modules/flowbite/**/*.js', // 👈 required for Flowbite
  ],
  theme: {
    extend: {},
  },
  plugins: [
    // other plugins...
  ],
};
