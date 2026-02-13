<?php

declare(strict_types=1);

namespace Meetplume\Plume\Enums;

use Meetplume\Plume\Types\PagesType;
use Meetplume\Plume\Types\CollectionType;
use Meetplume\Plume\Types\DocumentationType;

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
