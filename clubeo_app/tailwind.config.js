/** @type {import('tailwindcss').Config} */
export default {
  darkMode: 'class',
  content: [
    "./components/**/*.{js,vue,ts}",
    "./layouts/**/*.vue",
    "./pages/**/*.vue",
    "./app.vue",
  ],
  theme: {
    extend: {
      colors: {
        custom: {
          background: 'var(--background)',
          cards_menu: 'var(--cards_menus)',
          first_text: 'var(--first_text)',
          second_text: 'var(--second_text)',
          highlight: 'var(--highlight)',
          corners: 'var(--corners)',
          error: 'var(--error)',
          warning: 'var(--warning)',
          success: 'var(--success)'
        }
      },
    },
  },
  plugins: [],
}