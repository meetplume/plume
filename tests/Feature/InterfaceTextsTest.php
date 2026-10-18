<?php

declare(strict_types=1);

use Meetplume\Plume\InterfaceTexts;

it('ships every interface text in each language', function (string $locale): void {
    /** @var array<string, string> $translations */
    $translations = json_decode((string) file_get_contents(__DIR__.'/../../lang/'.$locale.'.json'), true);

    expect(array_diff(array_keys(InterfaceTexts::for('en')), array_keys($translations)))->toBe([]);
})->with(['fr', 'es', 'pt']);
