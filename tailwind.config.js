/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./templates/**/*.twig",
    "./templates/**/**/*.twig",
    "./templates/**/**/**/*.twig",
  ],
  theme: {
    extend: {
      animation: {
        'fade-in': 'fadeIn .5s ease-out;',
      },
      keyframes: {
        fadeIn: {
          '0%': {opacity: 0},
          '100%': {opacity: 1},
        },
      },
    },
  },
  plugins: [
    function ({ addVariant }) {
      addVariant('active', '&[active]');
    }
  ],
}

