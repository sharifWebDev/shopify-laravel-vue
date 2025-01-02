import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    './storage/framework/views/*.php',
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
    './resources/js/**/*.{js,ts}',
    './node_modules/@shopify/polaris/**/*.js',
  ],
  theme: {
    extend: {
      fontFamily: {
        playwrite: ['Playwrite ES Deco Guides', 'sans-serif'],
        dancingScript: ['Dancing Script', 'cursive'],
        inter: ['Inter', 'sans-serif'],
        lato: ['Lato', 'sans-serif'],
        montserrat: ['Montserrat', 'sans-serif'],
        notoSerif: ['Noto Serif', 'serif'],
        openSans: ['Open Sans', 'sans-serif'],
        oswald: ['Oswald', 'sans-serif'],
        roboto: ['Roboto', 'sans-serif'],
        rubik: ['Rubik', 'sans-serif'],
        ubuntu: ['Ubuntu', 'sans-serif'],
        ptSans: ['PT Sans', 'sans-serif'],
        raleway: ['Raleway', 'sans-serif'],
        workSans: ['Work Sans', 'sans-serif'],
        poppins: ['Poppins', 'sans-serif'],
        sourceSans: ['Source Sans Pro', 'sans-serif'],
        firaSans: ['Fira Sans', 'sans-serif'],
        barlow: ['Barlow', 'sans-serif'],
        josefin: ['Josefin Sans', 'sans-serif'],
        ralewayDots: ['Raleway Dots', 'sans-serif'],
        merriweather: ['Merriweather', 'serif'],
        vollkorn: ['Vollkorn', 'serif'],
        manrope: ['Manrope', 'sans-serif'],
        quicksand: ['Quicksand', 'sans-serif'],
        playfair: ['Playfair Display', 'serif'],
      },
      keyframes: {
        marquee: {
          "0%": { transform: "translateX(100%)" },
          "100%": { transform: "translateX(-100%)" },
        },
      },
      animation: {
        marquee: "marquee 3s linear infinite",
      },
    },
  },
  plugins: [],
};
