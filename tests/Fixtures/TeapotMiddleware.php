<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use Symfony\Component\HttpFoundation\Response;

class TeapotMiddleware
{
    public function handle(): Response
    {
        return response('', 418);
    }
}
