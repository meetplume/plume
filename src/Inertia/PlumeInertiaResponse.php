<?php

declare(strict_types=1);

namespace Meetplume\Plume\Inertia;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class PlumeInertiaResponse implements Responsable
{
    /**
     * @param  array<string, mixed>  $props
     */
    public function __construct(
        private readonly string $component,
        private readonly array $props,
        private readonly string $rootView,
    ) {}

    /**
     * @return array{component: string, props: array<string, mixed>, url: string, version: string}
     */
    public function toPageObject(Request $request): array
    {
        return [
            'component' => $this->component,
            'props' => $this->props,
            'url' => $request->getRequestUri(),
            'version' => '',
        ];
    }

    public function toResponse($request): SymfonyResponse
    {
        $page = $this->toPageObject($request);

        if ($request->header('X-Inertia')) {
            return new JsonResponse($page, 200, [
                'X-Inertia' => 'true',
                'Vary' => 'X-Inertia',
            ]);
        }

        return new Response(
            view($this->rootView, ['page' => $page]),
            200,
            ['Vary' => 'X-Inertia'],
        );
    }
}
