module.exports = {
  content: ["./**/*.{html,js,php}"],
  theme: {
    container: {
      center: true,
    },
    extend: {
      colors: {
        "jc-primary": "#262335",
        "jc-secondary": "#241B2F",
      },
      fontFamily: {
        roboto: ["var(--font-roboto)"],
      },
    },
  },
  plugins: [],
};
