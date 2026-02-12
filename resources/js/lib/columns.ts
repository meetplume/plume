export type Columns = number | { default?: number; sm?: number; md?: number; lg?: number; xl?: number };

const colsMap: Record<number, string> = {
    1: 'grid-cols-1',
    2: 'grid-cols-2',
    3: 'grid-cols-3',
    4: 'grid-cols-4',
    5: 'grid-cols-5',
    6: 'grid-cols-6',
};

const smColsMap: Record<number, string> = {
    1: 'sm:grid-cols-1',
    2: 'sm:grid-cols-2',
    3: 'sm:grid-cols-3',
    4: 'sm:grid-cols-4',
    5: 'sm:grid-cols-5',
    6: 'sm:grid-cols-6',
};

const mdColsMap: Record<number, string> = {
    1: 'md:grid-cols-1',
    2: 'md:grid-cols-2',
    3: 'md:grid-cols-3',
    4: 'md:grid-cols-4',
    5: 'md:grid-cols-5',
    6: 'md:grid-cols-6',
};

const lgColsMap: Record<number, string> = {
    1: 'lg:grid-cols-1',
    2: 'lg:grid-cols-2',
    3: 'lg:grid-cols-3',
    4: 'lg:grid-cols-4',
    5: 'lg:grid-cols-5',
    6: 'lg:grid-cols-6',
};

const xlColsMap: Record<number, string> = {
    1: 'xl:grid-cols-1',
    2: 'xl:grid-cols-2',
    3: 'xl:grid-cols-3',
    4: 'xl:grid-cols-4',
    5: 'xl:grid-cols-5',
    6: 'xl:grid-cols-6',
};

export function columnsClass(columns: Columns): string {
    if (typeof columns === 'number') {
        if (columns <= 1) return colsMap[1];
        return [colsMap[1], smColsMap[columns]].join(' ');
    }

    const { default: base = 1, sm, md, lg, xl } = columns;
    const classes: string[] = [colsMap[base]];

    if (sm) classes.push(smColsMap[sm]);
    if (md) classes.push(mdColsMap[md]);
    if (lg) classes.push(lgColsMap[lg]);
    if (xl) classes.push(xlColsMap[xl]);

    return classes.join(' ');
}