<?php

namespace App\Filament\Widgets;

use Filament\Support\RawJs;
use App\Models\PanAnalytics;
use Filament\Widgets\ChartWidget;
use Filament\Support\Colors\Color;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Support\Facades\FilamentColor;

class TopViewedPostsChart extends ChartWidget
{
    protected int | string | array $columnSpan = 'full';

    public function getHeading(): string|Htmlable|null
    {
        return __('Top 10 most viewed Posts');
    }

    protected function getOptions(): array|RawJs|null
    {
        return [
            'indexAxis' => 'y',
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'x' => [
                    'grid' => [
                        'display' => false,
                    ],
                ],
                'y' => [
                    'grid' => [
                        'display' => false,
                    ],
                ]
            ]
        ];
    }

    protected function getData(): array
    {
        $topPosts = PanAnalytics::where('name', 'LIKE', 'posts-%')
            ->orderBy('impressions', 'desc')
            ->limit(10)
            ->get();

        $labels = [];
        $data = [];

        foreach ($topPosts as $post) {
            $postName = str_replace('posts-', '', $post->name);
            $labels[] = str(ucfirst(str_replace(['-', '_'], ' ', $postName)))->limit(30);
            $data[] = $post->impressions;
        }

        $this->maxHeight = count($data) * 55 . 'px';

        $color = FilamentColor::getColor('primary')['600'];

        $colors = collect(range(100, 10, -10))
            ->map(fn ($percent) => str_replace(')', "/{$percent}%)", $color))
            ->all();

        return [
            'datasets' => [
                [
                    'label' => 'Views',
                    'data' => $data,
                    'backgroundColor' => $colors,
                    'borderWidth' => 0,
                    'borderRadius' => 100,
                    'borderSkipped' => false,
                    'barThickness' => 15,
                    'barPercentage' => 0.8,
                    'categoryPercentage' => 0.8,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
