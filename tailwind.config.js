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
        'ecoplast-green': {
          DEFAULT: '#2E7D32',
          light: '#4CAF50',
          dark: '#1B5E20',
        },
        'ecoplast-blue': {
          DEFAULT: '#1565C0',
          light: '#1E88E5',
          dark: '#0D47A1',
        },
        'ecoplast-gray': {
          DEFAULT: '#455A64',
          light: '#607D8B',
          dark: '#263238',
        },
      },
    },
  },
  plugins: [],
}
