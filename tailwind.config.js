/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    './storage/framework/views/*.php',
    './resources/views/**/*.blade.php',
    './resources/js/**/*.vue',
  ],

  theme: {
    
  },
  safelist: [
    {
      pattern: /^(bg|text|border|ring)-(sky|blue|cyan|red|green|yellow|purple|pink|indigo|gray)-(500|600)$/,
    },
  ],

  plugins: [require('@tailwindcss/forms')],
};
