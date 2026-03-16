import { Check, CircleAlert, CircleCheck, Paintbrush, Pipette, Plus, RefreshCw, RotateCcw, Save, Stethoscope } from 'lucide-react';
import React, { useCallback, useEffect, useMemo, useRef, useState } from 'react';

import { bundledThemesInfo } from 'shiki';

import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
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
import { cn } from '@/lib/utils';

const codeThemes = bundledThemesInfo;

type CodeThemePreview = { bg: string; c1: string; c2: string };

function extractPreviewColors(theme: {
    colors?: Record<string, string>;
    tokenColors?: Array<{ scope?: string | string[]; settings?: { foreground?: string } }>;
}): CodeThemePreview {
    const bg = theme.colors?.['editor.background'] ?? '#1e1e1e';
    const fg = theme.colors?.['editor.foreground'] ?? '#d4d4d4';
    let keyword: string | null = null;
    let entity: string | null = null;

    for (const t of theme.tokenColors ?? []) {
        const scopes = Array.isArray(t.scope) ? t.scope : [t.scope];
        const color = t.settings?.foreground;
        if (color == null) continue;
        if (keyword == null && scopes.some((s) => s?.includes('keyword'))) keyword = color;
        if (entity == null && scopes.some((s) => s === 'entity.name.function' || s === 'entity' || s === 'entity.name')) entity = color;
        if (keyword && entity) break;
    }

    return { bg, c1: keyword ?? fg, c2: entity ?? fg };
}

type PresetConfig = {
    primary: string;
    gray: string;
    radius: string;
    spacing: string;
    dark: boolean;
    code_theme_light: string;
    code_theme_dark: string;
};

type CustomizerConfig = {
    enabled: boolean;
    preset: string;
    presets: Record<string, PresetConfig>;
    vault?: string | null;
};

export type CustomizerInitialData = {
    theme: ThemeConfig;
    customizer?: CustomizerConfig;
};

type DiagnosticsPage = {
    key: string;
    slug: string;
    path: string;
    resolvedFilePath: string;
    fileExists: boolean;
    hidden: boolean;
    order: number | null;
    inNavigation: boolean;
};

type DiagnosticsRoute = {
    uri: string;
    name: string | null;
    methods: string[];
};

type DiagnosticsData = {
    vault: string;
    prefix: string;
    path: string;
    absolutePath: string;
    layout: string;
    discovery: string;
    hasNavigation: boolean;
    hasTabs: boolean;
    hasVersions: boolean;
    hasLanguages: boolean;
    hasPages: boolean;
    allSlugs: string[];
    pages: DiagnosticsPage[];
    routes: DiagnosticsRoute[];
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

async function fetchDiagnostics(vaultPrefix: string): Promise<DiagnosticsData> {
    const r = await fetch(`/${vaultPrefix}/_plume`, {
        headers: { Accept: 'application/json' },
    });
    return await r.json();
}

const radiusPreviewValues: Record<string, string> = {
    none: '0',
    small: '3px',
    medium: '6px',
    large: '10px',
};

const spacingGaps: Record<string, string> = {
    dense: '1px',
    compact: '2px',
    default: '3px',
    spacious: '5px',
};

type Tab = 'customizer' | 'diagnostics';

function PresetDots({ preset }: { preset: PresetConfig }) {
    const primaryColor = primaryColorPreview[preset.primary] ?? preset.primary;
    const grayColor = grayColorPreview[preset.gray] ?? preset.gray;

    return (
        <span
            className={`inline-flex items-center gap-0.5 rounded-sm border px-1 py-0.5 ${preset.dark ? 'border-neutral-700 bg-neutral-800' : 'border-neutral-200 bg-white'}`}
        >
            <span className="size-2 rounded-full" style={{ backgroundColor: primaryColor }} />
            <span className="size-2 rounded-full" style={{ backgroundColor: grayColor }} />
        </span>
    );
}

function SwatchTooltip({ label, children }: { label: string; children: React.ReactNode }) {
    return (
        <Tooltip>
            <TooltipTrigger asChild>{children}</TooltipTrigger>
            <TooltipContent className="theme-reset">{label}</TooltipContent>
        </Tooltip>
    );
}

function CodeThemeDots({ preview }: { preview: CodeThemePreview }) {
    return (
        <span className="inline-flex items-center gap-0.5 rounded-sm border px-1 py-0.5" style={{ backgroundColor: preview.bg }}>
            <span className="size-2 rounded-full" style={{ backgroundColor: preview.c1 }} />
            <span className="size-2 rounded-full" style={{ backgroundColor: preview.c2 }} />
        </span>
    );
}

function CodeThemeSelect({
    label,
    value,
    onChange,
    previews,
}: {
    label: string;
    value: string;
    onChange: (id: string) => void;
    previews: Record<string, CodeThemePreview>;
}) {
    return (
        <div>
            <div className="mb-1.5 text-xs font-medium">{label}</div>
            <Select value={value} onValueChange={onChange}>
                <SelectTrigger size="sm" className="w-full text-xs">
                    <SelectValue>
                        <span className="flex items-center gap-2">
                            {previews[value] && <CodeThemeDots preview={previews[value]} />}
                            <span>{codeThemes.find((t) => t.id === value)?.displayName ?? value}</span>
                        </span>
                    </SelectValue>
                </SelectTrigger>
                <SelectContent position="popper" className="theme-reset max-h-[200px]">
                    {codeThemes.map((t) => (
                        <SelectItem key={t.id} value={t.id} className="text-xs">
                            <span className="flex items-center gap-2">
                                {previews[t.id] && <CodeThemeDots preview={previews[t.id]} />}
                                <span>{t.displayName}</span>
                            </span>
                        </SelectItem>
                    ))}
                </SelectContent>
            </Select>
        </div>
    );
}

function TabBar({ active, onChange }: { active: Tab; onChange: (tab: Tab) => void }) {
    return (
        <div className="flex border-b border-black/5 dark:border-white/5">
            <button
                type="button"
                className={cn(
                    'flex flex-1 items-center justify-center gap-1.5 px-3 py-2.5 text-xs font-medium transition-colors',
                    active === 'customizer' ? 'border-b-2 border-foreground text-foreground' : 'text-muted-foreground hover:text-foreground',
                )}
                onClick={() => onChange('customizer')}
            >
                <Paintbrush className="size-3" />
                Customizer
            </button>
            <button
                type="button"
                className={cn(
                    'flex flex-1 items-center justify-center gap-1.5 px-3 py-2.5 text-xs font-medium transition-colors',
                    active === 'diagnostics' ? 'border-b-2 border-foreground text-foreground' : 'text-muted-foreground hover:text-foreground',
                )}
                onClick={() => onChange('diagnostics')}
            >
                <Stethoscope className="size-3" />
                Diagnostics
            </button>
        </div>
    );
}

function DiagnosticsPanel({ vaultPrefix }: { vaultPrefix: string }) {
    const [data, setData] = useState<DiagnosticsData | null>(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    const load = useCallback(() => {
        setLoading(true);
        setError(null);
        fetchDiagnostics(vaultPrefix)
            .then(setData)
            .catch((e) => setError(e.message))
            .finally(() => setLoading(false));
    }, [vaultPrefix]);

    useEffect(() => {
        load();
    }, [load]);

    if (loading) {
        return (
            <div className="flex flex-col gap-3 p-4">
                <div className="h-4 w-2/3 animate-pulse rounded bg-muted" />
                <div className="h-3 w-full animate-pulse rounded bg-muted" />
                <div className="h-3 w-5/6 animate-pulse rounded bg-muted" />
                <div className="h-3 w-4/6 animate-pulse rounded bg-muted" />
                <div className="mt-2 h-20 w-full animate-pulse rounded bg-muted" />
            </div>
        );
    }

    if (error) {
        return (
            <div className="p-4">
                <div className="rounded-lg border border-destructive/20 bg-destructive/5 p-3 text-xs text-destructive">{error}</div>
            </div>
        );
    }

    if (!data) {
        return null;
    }

    const missingFiles = data.pages.filter((p) => !p.fileExists);

    return (
        <div className="flex flex-col gap-4 p-4">
            {/* Vault info */}
            <div>
                <div className="mb-2 text-xs font-semibold">Vault</div>
                <div className="flex flex-col gap-1.5 text-[0.7rem]">
                    <div className="flex items-center justify-between gap-2">
                        <span className="text-muted-foreground">Class</span>
                        <code className="max-w-[200px] truncate rounded bg-muted px-1 py-0.5 text-[0.65rem]">{data.vault.split('\\').pop()}</code>
                    </div>
                    <div className="flex items-center justify-between gap-2">
                        <span className="text-muted-foreground">Prefix</span>
                        <code className="rounded bg-muted px-1 py-0.5 text-[0.65rem]">/{data.prefix}</code>
                    </div>
                    <div className="flex items-center justify-between gap-2">
                        <span className="text-muted-foreground">Layout</span>
                        <code className="rounded bg-muted px-1 py-0.5 text-[0.65rem]">{data.layout}</code>
                    </div>
                    <div className="flex items-center justify-between gap-2">
                        <span className="text-muted-foreground">Discovery</span>
                        <Badge variant="outline" className="text-[0.6rem]">
                            {data.discovery}
                        </Badge>
                    </div>
                    <div className="flex items-center justify-between gap-2">
                        <span className="text-muted-foreground">Path</span>
                        <code className="max-w-[200px] truncate rounded bg-muted px-1 py-0.5 text-[0.65rem]" title={data.absolutePath}>
                            {data.path}
                        </code>
                    </div>
                </div>
            </div>

            {/* Features */}
            <div>
                <div className="mb-2 text-xs font-semibold">Features</div>
                <div className="flex flex-wrap gap-1.5">
                    {data.hasNavigation && (
                        <Badge variant="secondary" className="text-[0.6rem]">
                            Navigation
                        </Badge>
                    )}
                    {data.hasTabs && (
                        <Badge variant="secondary" className="text-[0.6rem]">
                            Tabs
                        </Badge>
                    )}
                    {data.hasVersions && (
                        <Badge variant="secondary" className="text-[0.6rem]">
                            Versions
                        </Badge>
                    )}
                    {data.hasLanguages && (
                        <Badge variant="secondary" className="text-[0.6rem]">
                            Languages
                        </Badge>
                    )}
                    {data.hasPages && (
                        <Badge variant="secondary" className="text-[0.6rem]">
                            Extra pages
                        </Badge>
                    )}
                    {!data.hasNavigation && !data.hasTabs && !data.hasVersions && !data.hasLanguages && !data.hasPages && (
                        <span className="text-[0.7rem] text-muted-foreground">None</span>
                    )}
                </div>
            </div>

            {/* Missing files warning */}
            {missingFiles.length > 0 && (
                <div className="rounded-lg border border-amber-500/20 bg-amber-500/5 p-2.5">
                    <div className="mb-1.5 flex items-center gap-1.5 text-xs font-medium text-amber-600 dark:text-amber-400">
                        <CircleAlert className="size-3.5" />
                        {missingFiles.length} missing {missingFiles.length === 1 ? 'file' : 'files'}
                    </div>
                    <div className="flex flex-col gap-1">
                        {missingFiles.map((p) => (
                            <code key={p.key} className="text-[0.6rem] text-amber-700 dark:text-amber-300">
                                {p.path}
                            </code>
                        ))}
                    </div>
                </div>
            )}

            {/* Pages */}
            <div>
                <div className="mb-2 flex items-center justify-between">
                    <span className="text-xs font-semibold">Pages ({data.pages.length})</span>
                </div>
                <div className="flex flex-col gap-0.5">
                    {data.pages.map((page) => (
                        <div key={page.key} className="flex items-center gap-2 rounded px-1.5 py-1 hover:bg-muted/50">
                            {page.fileExists ? (
                                <CircleCheck className="size-3 shrink-0 text-emerald-500" />
                            ) : (
                                <CircleAlert className="size-3 shrink-0 text-amber-500" />
                            )}
                            <div className="flex min-w-0 flex-1 flex-col">
                                <div className="flex items-center gap-1.5">
                                    <code className="truncate text-[0.65rem]">{page.slug === '/' ? '/ (root)' : page.slug}</code>
                                    {page.hidden && (
                                        <Badge variant="outline" className="px-1 py-0 text-[0.55rem]">
                                            hidden
                                        </Badge>
                                    )}
                                    {!page.inNavigation && (
                                        <Badge variant="outline" className="px-1 py-0 text-[0.55rem]">
                                            extra
                                        </Badge>
                                    )}
                                </div>
                                <span className="truncate text-[0.6rem] text-muted-foreground">{page.path}</span>
                            </div>
                        </div>
                    ))}
                </div>
            </div>

            {/* Routes */}
            <div>
                <div className="mb-2 text-xs font-semibold">Routes ({data.routes.length})</div>
                <div className="flex flex-col gap-0.5">
                    {data.routes.map((route, i) => (
                        <div key={i} className="flex items-center gap-2 rounded px-1.5 py-1 hover:bg-muted/50">
                            <span className="shrink-0 text-[0.55rem] font-semibold text-muted-foreground uppercase">
                                {route.methods.filter((m) => m !== 'HEAD').join('|')}
                            </span>
                            <code className="min-w-0 truncate text-[0.65rem]">{route.uri}</code>
                        </div>
                    ))}
                </div>
            </div>

            {/* Refresh */}
            <button
                type="button"
                className="flex items-center justify-center gap-1.5 rounded-md border border-black/5 py-1.5 text-[0.65rem] text-muted-foreground transition-colors hover:bg-muted/50 hover:text-foreground dark:border-white/5"
                onClick={load}
            >
                <RefreshCw className="size-3" />
                Refresh
            </button>
        </div>
    );
}

export function Customizer({ initialData }: { initialData?: CustomizerInitialData }) {
    const customizerConfig = initialData?.customizer;

    const [open, setOpen] = useState(false);
    const [activeTab, setActiveTab] = useState<Tab>('customizer');
    const [config, setConfig] = useState<ThemeConfig | null>(initialData?.theme ?? null);
    const [savedConfig, setSavedConfig] = useState<ThemeConfig | null>(initialData?.theme ?? null);
    const [preset, setPreset] = useState(customizerConfig?.preset ?? '');
    const [customColor, setCustomColor] = useState('');
    const [saving, setSaving] = useState(false);
    const [codeThemePreviews, setCodeThemePreviews] = useState<Record<string, CodeThemePreview>>({});
    const buttonRef = useRef<HTMLButtonElement>(null);
    const customColorTimeout = useRef<ReturnType<typeof setTimeout>>(null);

    const presets = useMemo(() => customizerConfig?.presets ?? {}, [customizerConfig?.presets]);
    const presetNames = Object.keys(presets);

    const isCustomColor = config ? !availablePrimaryColors.includes(config.primary) : false;
    const isDirty = config && savedConfig ? JSON.stringify(config) !== JSON.stringify(savedConfig) : false;

    useEffect(() => {
        if (isCustomColor && config) {
            setCustomColor(config.primary);
        }
    }, [isCustomColor, config]);

    useEffect(() => {
        import('shiki').then(({ bundledThemes }) => {
            Promise.all(
                codeThemes.map((t) =>
                    bundledThemes[t.id as keyof typeof bundledThemes]().then((m) => [t.id, extractPreviewColors(m.default)] as const),
                ),
            ).then((entries) => setCodeThemePreviews(Object.fromEntries(entries)));
        });
    }, []);

    const updateConfig = useCallback(
        (partial: Partial<ThemeConfig>) => {
            if (!config) return;

            const next = { ...config, ...partial };
            setConfig(next);
            applyTheme(next);

            if ('dark' in partial) {
                setDarkMode(next.dark);
            }

            if ('code_theme_light' in partial || 'code_theme_dark' in partial) {
                window.dispatchEvent(
                    new CustomEvent('plume:code-theme', {
                        detail: { light: next.code_theme_light, dark: next.code_theme_dark },
                    }),
                );
            }
        },
        [config],
    );

    const saveConfig = useCallback(() => {
        if (!config || !isDirty || !customizerConfig?.vault) return;

        setSaving(true);
        const data: Record<string, unknown> = { ...config, vault: customizerConfig.vault };
        postCustomizer('/_plume/customizer', data).then((resolved) => {
            setSavedConfig(resolved);
            setConfig(resolved);
            applyTheme(resolved);
            setSaving(false);
        });
    }, [config, isDirty, customizerConfig?.vault]);

    const switchPreset = useCallback(
        (name: string) => {
            setPreset(name);
            const presetConfig = presets[name];
            if (!presetConfig || !config) return;

            const next: ThemeConfig = {
                primary: presetConfig.primary,
                gray: presetConfig.gray,
                radius: presetConfig.radius,
                spacing: presetConfig.spacing,
                dark: presetConfig.dark,
                code_theme_light: presetConfig.code_theme_light,
                code_theme_dark: presetConfig.code_theme_dark,
            };
            setConfig(next);
            applyTheme(next);
            if (next.dark !== document.documentElement.classList.contains('dark')) {
                setDarkMode(next.dark);
            }
            window.dispatchEvent(
                new CustomEvent('plume:code-theme', {
                    detail: { light: next.code_theme_light, dark: next.code_theme_dark },
                }),
            );
        },
        [config, presets],
    );

    const resetDefaults = useCallback(() => {
        if (!customizerConfig?.vault) return;

        const data: Record<string, unknown> = { vault: customizerConfig.vault };
        postCustomizer('/_plume/customizer/reset', data).then((resolved) => {
            setConfig(resolved);
            setSavedConfig(resolved);
            setPreset('');
            applyTheme(resolved);
            if (resolved.dark !== document.documentElement.classList.contains('dark')) {
                setDarkMode(resolved.dark);
            }
            window.dispatchEvent(
                new CustomEvent('plume:code-theme', {
                    detail: { light: resolved.code_theme_light, dark: resolved.code_theme_dark },
                }),
            );
        });
    }, [customizerConfig?.vault]);

    if (!customizerConfig?.enabled) {
        return null;
    }

    const isDark = config?.dark ?? false;

    return (
        <TooltipProvider>
            <div className="theme-reset fixed bottom-5 left-5 z-50 font-sans text-sm">
                {open && config && (
                    <div className="mb-2 flex max-h-[calc(100vh-94px)] w-[340px] flex-col rounded-xl border border-black/10 bg-white/90 shadow-lg backdrop-blur-sm dark:border-white/10 dark:bg-neutral-900/90">
                        <TabBar active={activeTab} onChange={setActiveTab} />

                        {activeTab === 'customizer' && (
                            <>
                                <div className="flex-1 overflow-y-auto p-4">
                                    <div className="mb-4 text-xs text-muted-foreground">
                                        Customize your Plume theme. Options are stored in{' '}
                                        <code className="rounded bg-muted px-1 py-0.5 text-[0.7rem]">config.yml</code>.
                                    </div>

                                    <div className="flex flex-col gap-5">
                                        {/* Preset dropdown */}
                                        {presetNames.length > 0 && (
                                            <div>
                                                <div className="mb-1.5 text-xs font-medium">Preset</div>
                                                <Select value={preset} onValueChange={switchPreset}>
                                                    <SelectTrigger size="sm" className="w-full text-xs">
                                                        <SelectValue placeholder="Select a preset">
                                                            {preset && presets[preset] ? (
                                                                <span className="flex items-center gap-2">
                                                                    <PresetDots preset={presets[preset]} />
                                                                    <span>{preset.charAt(0).toUpperCase() + preset.slice(1)}</span>
                                                                </span>
                                                            ) : undefined}
                                                        </SelectValue>
                                                    </SelectTrigger>
                                                    <SelectContent position="popper" className="theme-reset">
                                                        {presetNames.map((name) => (
                                                            <SelectItem key={name} value={name} className="text-xs">
                                                                <span className="flex items-center gap-2">
                                                                    <PresetDots preset={presets[name]} />
                                                                    <span>{name.charAt(0).toUpperCase() + name.slice(1)}</span>
                                                                </span>
                                                            </SelectItem>
                                                        ))}
                                                    </SelectContent>
                                                </Select>
                                            </div>
                                        )}

                                        {/* Primary color */}
                                        <div>
                                            <div className="mb-1.5 text-xs font-medium">Primary color</div>
                                            <div className="flex flex-wrap gap-1.5 pt-1">
                                                {availablePrimaryColors.map((color) => (
                                                    <SwatchTooltip key={color} label={color}>
                                                        <button
                                                            type="button"
                                                            className={cn(
                                                                'relative flex size-7 cursor-pointer items-center justify-center rounded-full border transition-transform hover:scale-110',
                                                                config.primary === color
                                                                    ? 'border-transparent ring-2 ring-foreground ring-offset-2 ring-offset-background'
                                                                    : 'border-black/10 dark:border-white/15',
                                                            )}
                                                            onClick={() => {
                                                                setCustomColor('');
                                                                updateConfig({ primary: color });
                                                            }}
                                                            style={{ backgroundColor: primaryColorPreview[color] ?? color }}
                                                        >
                                                            {config.primary === color && (
                                                                <Check className="size-3.5 text-white drop-shadow-[0_1px_1px_rgba(0,0,0,0.5)]" />
                                                            )}
                                                        </button>
                                                    </SwatchTooltip>
                                                ))}
                                                <SwatchTooltip label={isCustomColor ? `custom: ${config.primary}` : 'custom'}>
                                                    <label
                                                        className={cn(
                                                            'relative flex size-7 cursor-pointer items-center justify-center rounded-full border transition-transform hover:scale-110',
                                                            isCustomColor
                                                                ? 'ring-2 ring-foreground ring-offset-2 ring-offset-background'
                                                                : 'border-none',
                                                        )}
                                                        style={
                                                            isCustomColor
                                                                ? { backgroundColor: config.primary }
                                                                : {
                                                                      background:
                                                                          'conic-gradient(from 0deg, #f00, #ff0, #0f0, #0ff, #00f, #f0f, #f00)',
                                                                  }
                                                        }
                                                    >
                                                        <input
                                                            type="color"
                                                            className="sr-only"
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
                                                        {isCustomColor && (
                                                            <Pipette className="size-3.5 text-white drop-shadow-[0_1px_1px_rgba(0,0,0,0.5)]" />
                                                        )}
                                                        {!isCustomColor && (
                                                            <span className="size-4 rounded-full bg-background p-px">
                                                                <Plus className="size-3.5 text-primary" />
                                                            </span>
                                                        )}
                                                    </label>
                                                </SwatchTooltip>
                                            </div>
                                        </div>

                                        {/* Gray palette */}
                                        <div>
                                            <div className="mb-1.5 text-xs font-medium">Gray palette</div>
                                            <div className="flex flex-wrap gap-1.5 pt-1">
                                                {availableGrayPalettes.map((gray) => (
                                                    <SwatchTooltip key={gray} label={gray}>
                                                        <button
                                                            type="button"
                                                            className={cn(
                                                                'relative flex size-7 cursor-pointer items-center justify-center rounded-full border transition-transform hover:scale-110',
                                                                config.gray === gray
                                                                    ? 'border-transparent ring-2 ring-foreground ring-offset-2 ring-offset-background'
                                                                    : 'border-black/10 dark:border-white/15',
                                                            )}
                                                            onClick={() => updateConfig({ gray })}
                                                            style={{ backgroundColor: grayColorPreview[gray] ?? gray }}
                                                        >
                                                            {config.gray === gray && (
                                                                <Check className="size-3.5 text-white drop-shadow-[0_1px_1px_rgba(0,0,0,0.5)]" />
                                                            )}
                                                        </button>
                                                    </SwatchTooltip>
                                                ))}
                                            </div>
                                        </div>

                                        {/* Radius & Spacing */}
                                        <div className="grid grid-cols-2 gap-3">
                                            <div>
                                                <div className="mb-1.5 text-xs font-medium">Radius</div>
                                                <Select value={config.radius} onValueChange={(v) => updateConfig({ radius: v })}>
                                                    <SelectTrigger size="sm" className="w-full text-xs">
                                                        <SelectValue>
                                                            <span className="flex items-center gap-2">
                                                                <span
                                                                    className="inline-block size-3.5 border-t-2 border-l-2 border-current opacity-40"
                                                                    style={{ borderRadius: `${radiusPreviewValues[config.radius]} 0 0 0` }}
                                                                />
                                                                {config.radius}
                                                            </span>
                                                        </SelectValue>
                                                    </SelectTrigger>
                                                    <SelectContent position="popper" className="theme-reset">
                                                        {availableRadii.map((radius) => (
                                                            <SelectItem key={radius} value={radius} className="text-xs">
                                                                <span className="flex items-center gap-2">
                                                                    <span
                                                                        className="inline-block size-3.5 border-t-2 border-l-2 border-current opacity-40"
                                                                        style={{ borderRadius: `${radiusPreviewValues[radius]} 0 0 0` }}
                                                                    />
                                                                    {radius}
                                                                </span>
                                                            </SelectItem>
                                                        ))}
                                                    </SelectContent>
                                                </Select>
                                            </div>
                                            <div>
                                                <div className="mb-1.5 text-xs font-medium">Spacing</div>
                                                <Select value={config.spacing} onValueChange={(v) => updateConfig({ spacing: v })}>
                                                    <SelectTrigger size="sm" className="w-full text-xs">
                                                        <SelectValue>
                                                            <span className="flex items-center gap-2">
                                                                <span
                                                                    className="inline-flex flex-col items-center opacity-40"
                                                                    style={{ gap: spacingGaps[config.spacing] }}
                                                                >
                                                                    <span className="h-0.5 w-3 rounded-full bg-current" />
                                                                    <span className="h-0.5 w-3 rounded-full bg-current" />
                                                                </span>
                                                                {config.spacing}
                                                            </span>
                                                        </SelectValue>
                                                    </SelectTrigger>
                                                    <SelectContent position="popper" className="theme-reset">
                                                        {availableSpacings.map((spacing) => (
                                                            <SelectItem key={spacing} value={spacing} className="text-xs">
                                                                <span className="flex items-center gap-2">
                                                                    <span
                                                                        className="inline-flex flex-col items-center opacity-40"
                                                                        style={{ gap: spacingGaps[spacing] }}
                                                                    >
                                                                        <span className="h-0.5 w-3 rounded-full bg-current" />
                                                                        <span className="h-0.5 w-3 rounded-full bg-current" />
                                                                    </span>
                                                                    {spacing}
                                                                </span>
                                                            </SelectItem>
                                                        ))}
                                                    </SelectContent>
                                                </Select>
                                            </div>
                                        </div>

                                        {/* Dark mode toggle */}
                                        <div className="flex items-center justify-between">
                                            <div className="text-xs font-medium">Dark mode</div>
                                            <Switch checked={isDark} onCheckedChange={(checked) => updateConfig({ dark: checked })} />
                                        </div>

                                        {/* Code theme light */}
                                        <CodeThemeSelect
                                            label="Code theme (light)"
                                            value={config.code_theme_light}
                                            onChange={(id) => updateConfig({ code_theme_light: id })}
                                            previews={codeThemePreviews}
                                        />

                                        {/* Code theme dark */}
                                        <CodeThemeSelect
                                            label="Code theme (dark)"
                                            value={config.code_theme_dark}
                                            onChange={(id) => updateConfig({ code_theme_dark: id })}
                                            previews={codeThemePreviews}
                                        />
                                    </div>
                                </div>

                                {/* Save & Restore — pinned at bottom */}
                                <div className="flex gap-2 border-t border-black/5 p-4 dark:border-white/5">
                                    <Button size="sm" className="flex-1 gap-1.5 text-xs" disabled={!isDirty || saving} onClick={saveConfig}>
                                        <Save className="size-3.5" />
                                        {saving ? 'Saving…' : 'Save'}
                                    </Button>
                                    <Button variant="outline" size="sm" className="flex-1 gap-1.5 text-xs" onClick={resetDefaults}>
                                        <RotateCcw className="size-3.5" />
                                        Restore defaults
                                    </Button>
                                </div>
                            </>
                        )}

                        {activeTab === 'diagnostics' && customizerConfig?.vault && (
                            <div className="flex-1 overflow-y-auto">
                                <DiagnosticsPanel vaultPrefix={customizerConfig.vault} />
                            </div>
                        )}
                    </div>
                )}

                <Button ref={buttonRef} size="icon" className="size-12.5 rounded-full shadow-lg" onClick={() => setOpen(!open)}>
                    <Paintbrush className="size-5" />
                </Button>
            </div>
        </TooltipProvider>
    );
}
