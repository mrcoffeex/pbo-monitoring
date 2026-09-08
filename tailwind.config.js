/** Tailwind CSS configuration (adds safelist for dynamically applied classes) */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
        "./vendor/filament/**/*.blade.php",
        "./storage/framework/views/*.php",
    ],
    darkMode: "class",
    safelist: [
        {
            pattern:
                /^(bg|text|border|ring|fill|stroke|placeholder|caret)-(slate|gray|zinc|neutral|stone|red|orange|amber|yellow|lime|green|emerald|teal|cyan|sky|blue|indigo|violet|purple|fuchsia|pink|rose)-(50|100|200|300|400|500|600|700|800|900|950)$/,
            variants: ["hover", "focus", "active", "disabled", "dark"],
        },
        {
            pattern:
                /^(from|via|to)-(slate|gray|zinc|neutral|stone|red|orange|amber|yellow|lime|green|emerald|teal|cyan|sky|blue|indigo|violet|purple|fuchsia|pink|rose)-(50|100|200|300|400|500|600|700|800|900|950)$/,
            variants: ["dark"],
        },
        {
            pattern:
                /^(divide|ring-offset)-(slate|gray|zinc|neutral|stone|red|orange|amber|yellow|lime|green|emerald|teal|cyan|sky|blue|indigo|violet|purple|fuchsia|pink|rose)-(50|100|200|300|400|500|600|700|800|900|950)$/,
            variants: ["dark"],
        },
        "bg-white",
        "bg-black",
        "text-white",
        "text-black",
        "dark:text-white",
        "dark:bg-black",
        "border-white",
        "border-black",
        "ring-white",
        "ring-black",
    ],
    theme: {
        screens: {
            xs: "420px",
            sm: "640px",
            md: "768px",
            lg: "1024px",
            xl: "1280px",
            "2xl": "1536px",
        },
        extend: {
            container: {
                center: true,
                padding: {
                    DEFAULT: "1rem",
                    sm: "1.25rem",
                    lg: "2rem",
                    xl: "2.5rem",
                    "2xl": "3rem",
                },
            },
        },
    },
    plugins: [require("@tailwindcss/forms")],
};
