/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        // Kita kunci warna sesuai aset logo lu pak
        'siperu-yellow': '#F3C31B', // Kuning cerah khas logo
        'siperu-blue': '#0A3981',   // Biru tua/royal blue base logo
      }
    },
  },
  plugins: [],
}
