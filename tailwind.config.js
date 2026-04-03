const defaultTheme = require('tailwindcss/defaultTheme');

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                // Primary Font - Poppins (Modern, Clean)
                sans: ['Poppins', ...defaultTheme.fontFamily.sans],
                // Secondary Font - Playfair Display (Stylish Serif untuk quotes)
                serif: ['Playfair Display', ...defaultTheme.fontFamily.serif],
                // Monospace Font - JetBrains Mono (untuk tanggal, code)
                mono: ['JetBrains Mono', ...defaultTheme.fontFamily.mono],
            },
            colors: {
                // ========================================
                // GEN-Z COLOR PALETTE
                // ========================================

                // Deep Teal - Primary Accent
                teal: {
                    50: '#F0FDFA',
                    100: '#CCFBF1',
                    200: '#99F6E4',
                    300: '#5EEAD4',
                    400: '#2DD4BF',
                    500: '#14B8A6',
                    600: '#0D9488',
                    700: '#0F766E',
                    800: '#115E59',
                    900: '#134E4A',
                    950: '#042F2E',
                },

                // Sunny Yellow - Bright Accent
                yellow: {
                    50: '#FEFCE8',
                    100: '#FEF9C3',
                    200: '#FEF08A',
                    300: '#FDE047',
                    400: '#FACC15',
                    500: '#EAB308',
                    600: '#CA8A04',
                    700: '#A16207',
                    800: '#854D0E',
                    900: '#713F12',
                    950: '#422006',
                },

                // Lavender - Soft Accent
                lavender: {
                    50: '#F5F3FF',
                    100: '#EDE9FE',
                    200: '#DDD6FE',
                    300: '#C4B5FD',
                    400: '#A78BFA',
                    500: '#8B5CF6',
                    600: '#7C3AED',
                    700: '#6D28D9',
                    800: '#5B21B6',
                    900: '#4C1D95',
                    950: '#2E1065',
                },

                // Dusty Coral Orange - CTA Primary
                coral: {
                    50: '#FFF7ED',
                    100: '#FFEDD5',
                    200: '#FED7AA',
                    300: '#FDBA74',
                    400: '#FB923C',
                    500: '#F97316',
                    600: '#EA580C',
                    700: '#C2410C',
                    800: '#9A3412',
                    900: '#7C2D12',
                    950: '#431407',
                },

                // Refined Green - WhatsApp CTA
                green: {
                    50: '#F0FDF4',
                    100: '#DCFCE7',
                    200: '#BBF7D0',
                    300: '#86EFAC',
                    400: '#4ADE80',
                    500: '#22C55E',
                    600: '#16A34A',
                    700: '#15803D',
                    800: '#166534',
                    900: '#14532D',
                    950: '#052E16',
                },

                // Layered Pastel Backgrounds
                'bg-cream': '#FEF9F3',
                'bg-soft-white': '#FFFBF7',
                'bg-pastel-teal': '#F0FDFA',
                'bg-pastel-lavender': '#F5F3FF',
                'bg-pastel-yellow': '#FEFCE8',

                // Soft Text Colors (Avoid harsh blacks)
                'text-primary': '#1E293B',
                'text-secondary': '#475569',
                'text-muted': '#94A3B8',

                // ========================================
                // LEGACY LOVE COLORS (Preserved for compatibility)
                // ========================================
                love: {
                    50: '#FDF5F2',
                    100: '#FCE8E0',
                    200: '#F8D0C2',
                    300: '#F2B09A',
                    400: '#EA8669',
                    500: '#D96B4F',
                    600: '#C4553D',
                    700: '#A64636',
                    800: '#8B3D31',
                    900: '#75352D',
                    950: '#4E1F1A',
                },
                brown: {
                    soft: '#D4A574',
                    light: '#E8C9A0',
                    medium: '#C4956A',
                    dark: '#8B6F47',
                },
            },
            animation: {
                // ========================================
                // GEN-Z ANIMATIONS
                // ========================================
                'heartbeat': 'heartbeat 2s ease-in-out infinite',
                'float': 'float 3s ease-in-out infinite',
                'fade-in': 'fadeIn 0.6s ease-out forwards',
                'slide-up': 'slideUp 0.5s ease-out forwards',
                'slide-in-left': 'slideInLeft 0.5s ease-out forwards',
                'slide-in-right': 'slideInRight 0.5s ease-out forwards',
                'pulse-glow': 'pulseGlow 2s ease-in-out infinite',
                'pulse-soft': 'pulseSoft 2s ease-in-out infinite',
                'bounce-subtle': 'bounceSubtle 2s ease-in-out infinite',
                'scale-in': 'scaleIn 0.3s ease-out forwards',
                'rotate-slow': 'rotateSlow 20s linear infinite',

                // ========================================
                // LEGACY ANIMATIONS (Preserved)
                // ========================================
                'love-fall': 'loveFall linear forwards',
                'particle-explode': 'particleExplode ease-out forwards',
            },
            keyframes: {
                // ========================================
                // GEN-Z KEYFRAMES
                // ========================================
                heartbeat: {
                    '0%, 100%': { transform: 'scale(1)' },
                    '25%': { transform: 'scale(1.1)' },
                    '50%': { transform: 'scale(1)' },
                    '75%': { transform: 'scale(1.1)' },
                },
                float: {
                    '0%, 100%': { transform: 'translateY(0px)' },
                    '50%': { transform: 'translateY(-10px)' },
                },
                fadeIn: {
                    '0%': { opacity: '0', transform: 'translateY(20px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                slideUp: {
                    '0%': { opacity: '0', transform: 'translateY(30px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                slideInLeft: {
                    '0%': { opacity: '0', transform: 'translateX(-30px)' },
                    '100%': { opacity: '1', transform: 'translateX(0)' },
                },
                slideInRight: {
                    '0%': { opacity: '0', transform: 'translateX(30px)' },
                    '100%': { opacity: '1', transform: 'translateX(0)' },
                },
                pulseGlow: {
                    '0%, 100%': { boxShadow: '0 0 20px rgba(249, 115, 22, 0.3)' },
                    '50%': { boxShadow: '0 0 40px rgba(249, 115, 22, 0.6)' },
                },
                pulseSoft: {
                    '0%, 100%': { opacity: '1', transform: 'scale(1)' },
                    '50%': { opacity: '0.8', transform: 'scale(1.05)' },
                },
                bounceSubtle: {
                    '0%, 100%': { transform: 'translateY(0)' },
                    '50%': { transform: 'translateY(-5px)' },
                },
                scaleIn: {
                    '0%': { opacity: '0', transform: 'scale(0.9)' },
                    '100%': { opacity: '1', transform: 'scale(1)' },
                },
                rotateSlow: {
                    '0%': { transform: 'rotate(0deg)' },
                    '100%': { transform: 'rotate(360deg)' },
                },

                // ========================================
                // LEGACY KEYFRAMES (Preserved)
                // ========================================
                loveFall: {
                    '0%': {
                        transform: 'translateY(-100vh) rotate(0deg)',
                        opacity: '1',
                    },
                    '100%': {
                        transform: 'translateY(100vh) rotate(720deg)',
                        opacity: '0',
                    },
                },
                particleExplode: {
                    '0%': {
                        transform: 'translate(0, 0) scale(1)',
                        opacity: '1',
                    },
                    '100%': {
                        transform: 'translate(var(--tx), var(--ty)) scale(0)',
                        opacity: '0',
                    },
                },
            },
            borderRadius: {
                '4xl': '2rem',
                '5xl': '2.5rem',
            },
        },
    },

    plugins: [require('@tailwindcss/forms')],
};
