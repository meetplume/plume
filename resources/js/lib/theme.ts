export type ThemeConfig = {
    primary: string;
    gray: string;
    radius: string;
    spacing: string;
    dark: boolean;
};

type CssVars = Record<string, string>;
type ColorScale = { light: CssVars; dark: CssVars };

const radiusStyles: Record<string, string> = {
    none: `
        :root { --radius: 0rem; }
        [data-slot="button"] { border-radius: 0; }
        [data-slot="badge"] { border-radius: 0; }
    `,
    small: `
        :root { --radius: 0.375rem; }
        [data-slot="button"] { border-radius: var(--radius); }
        [data-slot="badge"] { border-radius: var(--radius); }
    `,
    medium: `
        :root { --radius: 0.625rem; }
        [data-slot="button"] { border-radius: var(--radius); }
        [data-slot="badge"] { border-radius: 9999px; }
    `,
    large: `
        :root { --radius: 1rem; }
        [data-slot="button"] { border-radius: calc(var(--radius) + 16px); }
        [data-slot="badge"] { border-radius: 9999px; }
    `,
};

const spacingSizes: Record<string, string> = {
    dense: '0.2rem',
    compact: '0.225rem',
    default: '0.25rem',
    spacious: '0.3rem',
};

function resolveSpacing(value: string): string {
    return spacingSizes[value] ?? spacingSizes.default;
}

const RADIUS_STYLE_ID = 'plume-radius';

function applyRadiusStyle(radius: string): void {
    const css = radiusStyles[radius] ?? radiusStyles.medium;

    let style = document.getElementById(RADIUS_STYLE_ID) as HTMLStyleElement | null;
    if (!style) {
        style = document.createElement('style');
        style.id = RADIUS_STYLE_ID;
        document.head.appendChild(style);
    }
    style.textContent = css;
}

const primaryColors: Record<string, ColorScale> = {
    neutral: {
        light: {
            '--primary': 'oklch(0.205 0 0)',
            '--primary-foreground': 'oklch(0.985 0 0)',
        },
        dark: {
            '--primary': 'oklch(0.985 0 0)',
            '--primary-foreground': 'oklch(0.205 0 0)',
        },
    },
    blue: {
        light: {
            '--primary': 'oklch(0.546 0.245 262.881)',
            '--primary-foreground': 'oklch(0.985 0 0)',
        },
        dark: {
            '--primary': 'oklch(0.546 0.245 262.881)',
            '--primary-foreground': 'oklch(0.985 0 0)',
        },
    },
    red: {
        light: {
            '--primary': 'oklch(0.577 0.245 27.325)',
            '--primary-foreground': 'oklch(0.985 0 0)',
        },
        dark: {
            '--primary': 'oklch(0.577 0.245 27.325)',
            '--primary-foreground': 'oklch(0.985 0 0)',
        },
    },
    green: {
        light: {
            '--primary': 'oklch(0.527 0.185 155.023)',
            '--primary-foreground': 'oklch(0.985 0 0)',
        },
        dark: {
            '--primary': 'oklch(0.527 0.185 155.023)',
            '--primary-foreground': 'oklch(0.985 0 0)',
        },
    },
    violet: {
        light: {
            '--primary': 'oklch(0.541 0.281 293.009)',
            '--primary-foreground': 'oklch(0.985 0 0)',
        },
        dark: {
            '--primary': 'oklch(0.702 0.183 293.541)',
            '--primary-foreground': 'oklch(0.205 0 0)',
        },
    },
    orange: {
        light: {
            '--primary': 'oklch(0.705 0.213 47.604)',
            '--primary-foreground': 'oklch(0.985 0 0)',
        },
        dark: {
            '--primary': 'oklch(0.705 0.213 47.604)',
            '--primary-foreground': 'oklch(0.985 0 0)',
        },
    },
    rose: {
        light: {
            '--primary': 'oklch(0.585 0.233 14.645)',
            '--primary-foreground': 'oklch(0.985 0 0)',
        },
        dark: {
            '--primary': 'oklch(0.585 0.233 14.645)',
            '--primary-foreground': 'oklch(0.985 0 0)',
        },
    },
    amber: {
        light: {
            '--primary': 'oklch(0.769 0.188 70.08)',
            '--primary-foreground': 'oklch(0.205 0 0)',
        },
        dark: {
            '--primary': 'oklch(0.769 0.188 70.08)',
            '--primary-foreground': 'oklch(0.205 0 0)',
        },
    },
    emerald: {
        light: {
            '--primary': 'oklch(0.596 0.178 163.231)',
            '--primary-foreground': 'oklch(0.985 0 0)',
        },
        dark: {
            '--primary': 'oklch(0.596 0.178 163.231)',
            '--primary-foreground': 'oklch(0.985 0 0)',
        },
    },
};

const grayPalettes: Record<string, ColorScale> = {
    slate: {
        light: {
            '--background': 'oklch(1 0 0)',
            '--foreground': 'oklch(0.129 0.042 264.695)',
            '--card': 'oklch(1 0 0)',
            '--card-foreground': 'oklch(0.129 0.042 264.695)',
            '--popover': 'oklch(1 0 0)',
            '--popover-foreground': 'oklch(0.129 0.042 264.695)',
            '--secondary': 'oklch(0.968 0.007 264.536)',
            '--secondary-foreground': 'oklch(0.208 0.042 265.755)',
            '--muted': 'oklch(0.968 0.007 264.536)',
            '--muted-foreground': 'oklch(0.554 0.023 264.364)',
            '--accent': 'oklch(0.968 0.007 264.536)',
            '--accent-foreground': 'oklch(0.208 0.042 265.755)',
            '--border': 'oklch(0.929 0.013 255.508)',
            '--input': 'oklch(0.929 0.013 255.508)',
            '--ring': 'oklch(0.704 0.04 256.788)',
        },
        dark: {
            '--background': 'oklch(0.129 0.042 264.695)',
            '--foreground': 'oklch(0.984 0.003 247.858)',
            '--card': 'oklch(0.129 0.042 264.695)',
            '--card-foreground': 'oklch(0.984 0.003 247.858)',
            '--popover': 'oklch(0.129 0.042 264.695)',
            '--popover-foreground': 'oklch(0.984 0.003 247.858)',
            '--secondary': 'oklch(0.279 0.029 260.031)',
            '--secondary-foreground': 'oklch(0.984 0.003 247.858)',
            '--muted': 'oklch(0.279 0.029 260.031)',
            '--muted-foreground': 'oklch(0.704 0.04 256.788)',
            '--accent': 'oklch(0.279 0.029 260.031)',
            '--accent-foreground': 'oklch(0.984 0.003 247.858)',
            '--border': 'oklch(0.279 0.029 260.031)',
            '--input': 'oklch(0.279 0.029 260.031)',
            '--ring': 'oklch(0.442 0.017 257.281)',
        },
    },
    gray: {
        light: {
            '--background': 'oklch(1 0 0)',
            '--foreground': 'oklch(0.13 0.028 261.692)',
            '--card': 'oklch(1 0 0)',
            '--card-foreground': 'oklch(0.13 0.028 261.692)',
            '--popover': 'oklch(1 0 0)',
            '--popover-foreground': 'oklch(0.13 0.028 261.692)',
            '--secondary': 'oklch(0.967 0.003 264.542)',
            '--secondary-foreground': 'oklch(0.21 0.028 264.531)',
            '--muted': 'oklch(0.967 0.003 264.542)',
            '--muted-foreground': 'oklch(0.551 0.018 264.436)',
            '--accent': 'oklch(0.967 0.003 264.542)',
            '--accent-foreground': 'oklch(0.21 0.028 264.531)',
            '--border': 'oklch(0.928 0.006 264.531)',
            '--input': 'oklch(0.928 0.006 264.531)',
            '--ring': 'oklch(0.707 0.022 261.325)',
        },
        dark: {
            '--background': 'oklch(0.13 0.028 261.692)',
            '--foreground': 'oklch(0.985 0.002 247.839)',
            '--card': 'oklch(0.13 0.028 261.692)',
            '--card-foreground': 'oklch(0.985 0.002 247.839)',
            '--popover': 'oklch(0.13 0.028 261.692)',
            '--popover-foreground': 'oklch(0.985 0.002 247.839)',
            '--secondary': 'oklch(0.278 0.02 256.848)',
            '--secondary-foreground': 'oklch(0.985 0.002 247.839)',
            '--muted': 'oklch(0.278 0.02 256.848)',
            '--muted-foreground': 'oklch(0.707 0.022 261.325)',
            '--accent': 'oklch(0.278 0.02 256.848)',
            '--accent-foreground': 'oklch(0.985 0.002 247.839)',
            '--border': 'oklch(0.278 0.02 256.848)',
            '--input': 'oklch(0.278 0.02 256.848)',
            '--ring': 'oklch(0.446 0.03 256.802)',
        },
    },
    zinc: {
        light: {
            '--background': 'oklch(1 0 0)',
            '--foreground': 'oklch(0.141 0.005 285.823)',
            '--card': 'oklch(1 0 0)',
            '--card-foreground': 'oklch(0.141 0.005 285.823)',
            '--popover': 'oklch(1 0 0)',
            '--popover-foreground': 'oklch(0.141 0.005 285.823)',
            '--secondary': 'oklch(0.967 0.001 286.375)',
            '--secondary-foreground': 'oklch(0.21 0.006 285.885)',
            '--muted': 'oklch(0.967 0.001 286.375)',
            '--muted-foreground': 'oklch(0.552 0.016 285.938)',
            '--accent': 'oklch(0.967 0.001 286.375)',
            '--accent-foreground': 'oklch(0.21 0.006 285.885)',
            '--border': 'oklch(0.92 0.004 286.32)',
            '--input': 'oklch(0.92 0.004 286.32)',
            '--ring': 'oklch(0.705 0.015 286.067)',
        },
        dark: {
            '--background': 'oklch(0.141 0.005 285.823)',
            '--foreground': 'oklch(0.985 0 0)',
            '--card': 'oklch(0.141 0.005 285.823)',
            '--card-foreground': 'oklch(0.985 0 0)',
            '--popover': 'oklch(0.141 0.005 285.823)',
            '--popover-foreground': 'oklch(0.985 0 0)',
            '--secondary': 'oklch(0.274 0.006 286.033)',
            '--secondary-foreground': 'oklch(0.985 0 0)',
            '--muted': 'oklch(0.274 0.006 286.033)',
            '--muted-foreground': 'oklch(0.705 0.015 286.067)',
            '--accent': 'oklch(0.274 0.006 286.033)',
            '--accent-foreground': 'oklch(0.985 0 0)',
            '--border': 'oklch(0.274 0.006 286.033)',
            '--input': 'oklch(0.274 0.006 286.033)',
            '--ring': 'oklch(0.442 0.017 285.786)',
        },
    },
    neutral: {
        light: {
            '--background': 'oklch(1 0 0)',
            '--foreground': 'oklch(0.145 0 0)',
            '--card': 'oklch(1 0 0)',
            '--card-foreground': 'oklch(0.145 0 0)',
            '--popover': 'oklch(1 0 0)',
            '--popover-foreground': 'oklch(0.145 0 0)',
            '--secondary': 'oklch(0.97 0 0)',
            '--secondary-foreground': 'oklch(0.205 0 0)',
            '--muted': 'oklch(0.97 0 0)',
            '--muted-foreground': 'oklch(0.556 0 0)',
            '--accent': 'oklch(0.97 0 0)',
            '--accent-foreground': 'oklch(0.205 0 0)',
            '--border': 'oklch(0.922 0 0)',
            '--input': 'oklch(0.922 0 0)',
            '--ring': 'oklch(0.708 0 0)',
        },
        dark: {
            '--background': 'oklch(0.145 0 0)',
            '--foreground': 'oklch(0.985 0 0)',
            '--card': 'oklch(0.145 0 0)',
            '--card-foreground': 'oklch(0.985 0 0)',
            '--popover': 'oklch(0.145 0 0)',
            '--popover-foreground': 'oklch(0.985 0 0)',
            '--secondary': 'oklch(0.269 0 0)',
            '--secondary-foreground': 'oklch(0.985 0 0)',
            '--muted': 'oklch(0.269 0 0)',
            '--muted-foreground': 'oklch(0.708 0 0)',
            '--accent': 'oklch(0.269 0 0)',
            '--accent-foreground': 'oklch(0.985 0 0)',
            '--border': 'oklch(0.269 0 0)',
            '--input': 'oklch(0.269 0 0)',
            '--ring': 'oklch(0.439 0 0)',
        },
    },
    stone: {
        light: {
            '--background': 'oklch(1 0 0)',
            '--foreground': 'oklch(0.147 0.004 49.25)',
            '--card': 'oklch(1 0 0)',
            '--card-foreground': 'oklch(0.147 0.004 49.25)',
            '--popover': 'oklch(1 0 0)',
            '--popover-foreground': 'oklch(0.147 0.004 49.25)',
            '--secondary': 'oklch(0.97 0.001 106.424)',
            '--secondary-foreground': 'oklch(0.216 0.006 56.043)',
            '--muted': 'oklch(0.97 0.001 106.424)',
            '--muted-foreground': 'oklch(0.553 0.013 58.071)',
            '--accent': 'oklch(0.97 0.001 106.424)',
            '--accent-foreground': 'oklch(0.216 0.006 56.043)',
            '--border': 'oklch(0.923 0.003 48.717)',
            '--input': 'oklch(0.923 0.003 48.717)',
            '--ring': 'oklch(0.709 0.01 56.259)',
        },
        dark: {
            '--background': 'oklch(0.147 0.004 49.25)',
            '--foreground': 'oklch(0.985 0.001 106.423)',
            '--card': 'oklch(0.147 0.004 49.25)',
            '--card-foreground': 'oklch(0.985 0.001 106.423)',
            '--popover': 'oklch(0.147 0.004 49.25)',
            '--popover-foreground': 'oklch(0.985 0.001 106.423)',
            '--secondary': 'oklch(0.268 0.007 34.298)',
            '--secondary-foreground': 'oklch(0.985 0.001 106.423)',
            '--muted': 'oklch(0.268 0.007 34.298)',
            '--muted-foreground': 'oklch(0.709 0.01 56.259)',
            '--accent': 'oklch(0.268 0.007 34.298)',
            '--accent-foreground': 'oklch(0.985 0.001 106.423)',
            '--border': 'oklch(0.268 0.007 34.298)',
            '--input': 'oklch(0.268 0.007 34.298)',
            '--ring': 'oklch(0.444 0.011 73.639)',
        },
    },
};

function resolvePrimary(name: string): ColorScale {
    if (name in primaryColors) {
        return primaryColors[name];
    }

    return {
        light: { '--primary': name, '--primary-foreground': 'oklch(0.985 0 0)' },
        dark: { '--primary': name, '--primary-foreground': 'oklch(0.985 0 0)' },
    };
}

function applyVars(vars: CssVars): void {
    const root = document.documentElement;
    for (const [prop, value] of Object.entries(vars)) {
        root.style.setProperty(prop, value);
    }
}

export function applyTheme(config: ThemeConfig): void {
    const gray = grayPalettes[config.gray] ?? grayPalettes.neutral;
    const primary = resolvePrimary(config.primary);

    const spacing = resolveSpacing(config.spacing);

    applyRadiusStyle(config.radius);

    const lightVars: CssVars = {
        ...gray.light,
        ...primary.light,
        '--spacing': spacing,
    };

    const darkVars: CssVars = {
        ...gray.dark,
        ...primary.dark,
        '--spacing': spacing,
    };

    const isDark = document.documentElement.classList.contains('dark');
    applyVars(isDark ? darkVars : lightVars);

    // Store vars for dark mode toggling
    window.__plumeTheme = { light: lightVars, dark: darkVars };
}

declare global {
    interface Window {
        __plumeTheme?: { light: CssVars; dark: CssVars };
    }
}
