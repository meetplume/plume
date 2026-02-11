<?php

declare(strict_types=1);

namespace Meetplume\Plume\Types;

enum Type: string
{
    case Documentation = 'documentation';
    case Pages = 'pages';

    public function driver(): CollectionType
    {
        return match ($this) {
            self::Documentation => new DocumentationType,
            self::Pages => new PagesType,
        };
    }
}
