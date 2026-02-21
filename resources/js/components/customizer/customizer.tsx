import '../../../css/customizer.css';

import { Check, Paintbrush, RotateCcw } from 'lucide-react';
import React, { useCallback, useEffect, useRef, useState } from 'react';

import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import {
    applyTheme,
    availableGrayPalettes,
    availablePrimaryColors,
    availableRadii,
    availableSpacings,
    grayColorPreview,
    primaryColorPreview,
    setDarkMode,
    type ThemeConfig,
} from '@/lib/theme';

type PresetConfig = {
    primary: string;
    gray: string;
    dark: boolean;
};

type CustomizerConfig = {
    enabled: boolean;
    preset: string;
    presets: Record<string, PresetConfig>;
};

export type CustomizerInitialData = {
    theme: ThemeConfig;
    customizer?: CustomizerConfig;
};

async function postCustomizer(path: string, data: Record<string, unknown>): Promise<ThemeConfig> {
    const token = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content ?? '';

    const r = await fetch(path, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
            'X-CSRF-TOKEN': token,
        },
        body: JSON.stringify(data),
    });
    return await r.json();
}

const radiusPreviewValues: Record<string, string> = {
    none: '0',
    small: '3px',
    medium: '6px',
    large: '10px',
};

function PresetDots({ preset }: { preset: PresetConfig }) {
    const primaryColor = primaryColorPreview[preset.primary] ?? preset.primary;
    const grayColor = grayColorPreview[preset.gray] ?? preset.gray;

    return (
        <span className="cz-preset-dots" data-dark={preset.dark}>
            <span className="cz-preset-dot" style={{ backgroundColor: primaryColor }} />
            <span className="cz-preset-dot" style={{ backgroundColor: grayColor }} />
        </span>
    );
}

function SwatchTooltip({ label, children }: { label: string; children: React.ReactNode }) {
    return (
        <Tooltip>
            <TooltipTrigger asChild>{children}</TooltipTrigger>
            <TooltipContent className="plume-customizer-tooltip">{label}</TooltipContent>
        </Tooltip>
    );
}

export function Customizer({ initialData }: { initialData?: CustomizerInitialData }) {
    const customizerConfig = initialData?.customizer;

    const [open, setOpen] = useState(false);
    const [config, setConfig] = useState<ThemeConfig | null>(initialData?.theme ?? null);
    const [preset, setPreset] = useState(customizerConfig?.preset ?? '');
    const [customColor, setCustomColor] = useState('');
    const buttonRef = useRef<HTMLButtonElement>(null);
    const customColorTimeout = useRef<ReturnType<typeof setTimeout>>(null);

    const isCustomColor = config ? !availablePrimaryColors.includes(config.primary) : false;

    useEffect(() => {
        if (isCustomColor && config) {
            setCustomColor(config.primary);
        }
    }, [isCustomColor, config]);

    const updateConfig = useCallback(
        (partial: Partial<ThemeConfig>) => {
            if (!config) return;

            const next = { ...config, ...partial };
            setConfig(next);
            applyTheme(next);

            if ('dark' in partial) {
                setDarkMode(next.dark);
            }

            postCustomizer('/_plume/customizer', partial).then();
        },
        [config],
    );

    const switchPreset = useCallback((name: string) => {
        setPreset(name);
        postCustomizer('/_plume/customizer', { theme: name }).then((resolved) => {
            setConfig(resolved);
            applyTheme(resolved);
            if (resolved.dark !== document.documentElement.classList.contains('dark')) {
                setDarkMode(resolved.dark);
            }
        });
    }, []);

    const resetDefaults = useCallback(() => {
        postCustomizer('/_plume/customizer/reset', {}).then((resolved) => {
            setConfig(resolved);
            applyTheme(resolved);
            if (resolved.dark !== document.documentElement.classList.contains('dark')) {
                setDarkMode(resolved.dark);
            }
        });
    }, []);

    if (!customizerConfig?.enabled) {
        return null;
    }

    const isDark = config?.dark ?? false;
    const presets = customizerConfig.presets ?? {};
    const presetNames = Object.keys(presets);

    return (
        <TooltipProvider>
            <div className="plume-customizer">
                {open && config && (
                    <div className="cz-panel">
                        <div className="cz-title">Customizer</div>

                        <div className="cz-sections">
                            {/* Preset list */}
                            {presetNames.length > 0 && (
                                <div>
                                    <div className="cz-label">Preset</div>
                                    <div className="cz-preset-list">
                                        {presetNames.map((name) => (
                                            <button
                                                key={name}
                                                type="button"
                                                className="cz-preset-item"
                                                data-active={preset === name}
                                                onClick={() => switchPreset(name)}
                                            >
                                                <PresetDots preset={presets[name]} />
                                                <span className="cz-preset-name">{name.charAt(0).toUpperCase() + name.slice(1)}</span>
                                            </button>
                                        ))}
                                    </div>
                                </div>
                            )}

                            {/* Primary color */}
                            <div>
                                <div className="cz-label">Primary color</div>
                                <div className="cz-swatches">
                                    {availablePrimaryColors.map((color) => (
                                        <SwatchTooltip key={color} label={color}>
                                            <button
                                                type="button"
                                                className="cz-swatch"
                                                data-active={config.primary === color}
                                                onClick={() => {
                                                    setCustomColor('');
                                                    updateConfig({ primary: color });
                                                }}
                                                style={{ backgroundColor: primaryColorPreview[color] ?? color }}
                                            >
                                                {config.primary === color && <Check className="cz-check" />}
                                            </button>
                                        </SwatchTooltip>
                                    ))}
                                    <SwatchTooltip label={isCustomColor ? `custom: ${config.primary}` : 'custom'}>
                                        <label className="cz-custom-color" data-active={isCustomColor}>
                                            <input
                                                type="color"
                                                className="cz-color-input"
                                                value={customColor || '#000000'}
                                                onChange={(e) => {
                                                    const value = e.target.value;
                                                    setCustomColor(value);
                                                    if (customColorTimeout.current) {
                                                        clearTimeout(customColorTimeout.current);
                                                    }
                                                    customColorTimeout.current = setTimeout(() => {
                                                        updateConfig({ primary: value });
                                                    }, 100);
                                                }}
                                            />
                                            {isCustomColor && <Check className="cz-check" />}
                                        </label>
                                    </SwatchTooltip>
                                </div>
                            </div>

                            {/* Gray palette */}
                            <div>
                                <div className="cz-label">Gray palette</div>
                                <div className="cz-swatches">
                                    {availableGrayPalettes.map((gray) => (
                                        <SwatchTooltip key={gray} label={gray}>
                                            <button
                                                type="button"
                                                className="cz-swatch"
                                                data-active={config.gray === gray}
                                                onClick={() => updateConfig({ gray })}
                                                style={{ backgroundColor: grayColorPreview[gray] ?? gray }}
                                            >
                                                {config.gray === gray && <Check className="cz-check" />}
                                            </button>
                                        </SwatchTooltip>
                                    ))}
                                </div>
                            </div>

                            {/* Radius */}
                            <div>
                                <div className="cz-label">Radius</div>
                                <div className="cz-pills">
                                    {availableRadii.map((radius) => (
                                        <button
                                            key={radius}
                                            type="button"
                                            className="cz-pill"
                                            data-active={config.radius === radius}
                                            onClick={() => updateConfig({ radius: radius })}
                                        >
                                            <span className="cz-radius-icon" style={{ borderRadius: `${radiusPreviewValues[radius]} 0 0 0` }} />
                                            {radius}
                                        </button>
                                    ))}
                                </div>
                            </div>

                            {/* Spacing */}
                            <div>
                                <div className="cz-label">Spacing</div>
                                <div className="cz-pills">
                                    {availableSpacings.map((spacing) => (
                                        <button
                                            key={spacing}
                                            type="button"
                                            className="cz-pill"
                                            data-active={config.spacing === spacing}
                                            onClick={() => updateConfig({ spacing: spacing })}
                                        >
                                            <span className="cz-spacing-icon" data-spacing={spacing}>
                                                <span />
                                                <span />
                                            </span>
                                            {spacing}
                                        </button>
                                    ))}
                                </div>
                            </div>

                            {/* Dark mode toggle */}
                            <div className="cz-toggle-row">
                                <div className="cz-label" style={{ marginBottom: 0 }}>
                                    Dark mode
                                </div>
                                <button
                                    type="button"
                                    role="switch"
                                    aria-checked={isDark}
                                    className="cz-toggle"
                                    data-on={isDark}
                                    onClick={() => updateConfig({ dark: !isDark })}
                                >
                                    <span className="cz-toggle-knob" />
                                </button>
                            </div>

                            {/* Restore defaults */}
                            <button type="button" className="cz-reset" onClick={resetDefaults}>
                                <RotateCcw style={{ width: 14, height: 14 }} />
                                Restore defaults
                            </button>
                        </div>
                    </div>
                )}

                <button ref={buttonRef} type="button" className="cz-fab" onClick={() => setOpen(!open)}>
                    <Paintbrush style={{ width: 20, height: 20 }} />
                </button>
            </div>
        </TooltipProvider>
    );
}
