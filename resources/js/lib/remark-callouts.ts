import type { Root } from 'mdast';
import type { Plugin } from 'unified';
import type { Node, Parent } from 'unist';
import { visit } from 'unist-util-visit';

const CALLOUT_TYPES = ['tip', 'warning', 'danger', 'info', 'note', 'details'] as const;
type CalloutType = (typeof CALLOUT_TYPES)[number];

/** Maps aliases to their canonical callout type. */
const ALIASES: Record<string, CalloutType> = {
    important: 'info',
    caution: 'danger',
};

/** All recognized names: canonical types + aliases. */
const ALL_NAMES = [...CALLOUT_TYPES, ...Object.keys(ALIASES)] as const;

const ICONS: Record<CalloutType, string> = {
    tip: '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 14c.2-1 .7-1.7 1.5-2.5 1-.9 1.5-2.2 1.5-3.5A6 6 0 0 0 6 8c0 1 .2 2.2 1.5 3.5.7.7 1.3 1.5 1.5 2.5"/><path d="M9 18h6"/><path d="M10 22h4"/></svg>',
    warning:
        '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>',
    danger: '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m15 9-6 6"/><path d="m9 9 6 6"/></svg>',
    info: '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>',
    note: '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.376 3.622a1 1 0 0 1 3.002 3.002L7.368 18.635a2 2 0 0 1-.855.506l-2.872.838a.5.5 0 0 1-.62-.62l.838-2.872a2 2 0 0 1 .506-.854z"/></svg>',
    details:
        '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>',
};

interface DirectiveNode extends Node {
    type: 'containerDirective';
    name: string;
    attributes?: Record<string, string>;
    children: Node[];
}

function resolveCalloutType(name: string): CalloutType | null {
    const lower = name.toLowerCase();
    if (CALLOUT_TYPES.includes(lower as CalloutType)) {
        return lower as CalloutType;
    }
    return ALIASES[lower] ?? null;
}

const typesPattern = ALL_NAMES.join('|');

/**
 * Normalizes VitePress-style callout syntax to remark-directive syntax.
 *
 * `::: tip`           → `:::tip`
 * `::: tip My Title`  → `:::tip[My Title]`
 */
export function normalizeCalloutSyntax(markdown: string): string {
    const calloutRe = new RegExp(`^::: *(${typesPattern})(?: +(.+?))? *$`, 'gmi');
    const fenceRe = /^(`{3,}|~{3,})/;

    const lines = markdown.split('\n');
    let insideFence = false;
    let fenceChar = '';
    let fenceLen = 0;

    for (let i = 0; i < lines.length; i++) {
        const trimmed = lines[i].trimStart();
        if (insideFence) {
            const closing = trimmed.match(fenceRe);
            if (closing && closing[1][0] === fenceChar && closing[1].length >= fenceLen && trimmed.slice(closing[1].length).trim() === '') {
                insideFence = false;
            }
            continue;
        }

        const fenceMatch = trimmed.match(fenceRe);
        if (fenceMatch) {
            insideFence = true;
            fenceChar = fenceMatch[1][0];
            fenceLen = fenceMatch[1].length;
            continue;
        }

        lines[i] = lines[i].replace(calloutRe, (_, name: string, title?: string) => {
            if (title) {
                return `:::${name.toLowerCase()}[${title.trim()}]`;
            }
            return `:::${name.toLowerCase()}`;
        });
    }

    return lines.join('\n');
}

export const remarkCallouts: Plugin<[], Root> = () => {
    return (tree: Root) => {
        visit(tree, 'containerDirective', (node: Node, index: number | undefined, parent: Parent | undefined) => {
            const directive = node as DirectiveNode;

            const type = resolveCalloutType(directive.name);
            if (!type || index === undefined || !parent) {
                return;
            }
            const isDetails = type === 'details';
            const icon = ICONS[type];

            // Extract custom title from directive label if provided.
            let title: string | null = null;
            const firstChild = directive.children[0] as (Node & { data?: Record<string, unknown>; children?: Node[] }) | undefined;
            if (firstChild?.data?.directiveLabel) {
                title = ((firstChild.children ?? []) as { value?: string }[]).map((c) => c.value ?? '').join('');
                directive.children.splice(0, 1);
            }

            const titleHtml = title ? `<p class="plume-callout__title">${escapeHtml(title)}</p>` : '';

            if (isDetails) {
                const openTag = {
                    type: 'html' as const,
                    value: `<details class="plume-callout plume-callout--details"><summary class="plume-callout__summary"><div class="plume-callout__icon">${icon}</div><span>${escapeHtml(title ?? 'Details')}</span></summary><div class="plume-callout__content">`,
                };
                const closingNode = { type: 'html' as const, value: '</div></details>' };

                (parent.children as Node[]).splice(index, 1, openTag as unknown as Node, ...directive.children, closingNode as unknown as Node);
            } else {
                const openTag = {
                    type: 'html' as const,
                    value: `<div class="plume-callout plume-callout--${type}"><div class="plume-callout__icon">${icon}</div><div class="plume-callout__content">${titleHtml}`,
                };
                const closeTag = { type: 'html' as const, value: '</div></div>' };

                (parent.children as Node[]).splice(index, 1, openTag as unknown as Node, ...directive.children, closeTag as unknown as Node);
            }
        });
    };
};

function escapeHtml(str: string): string {
    return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}
