<?php

namespace App\Filament\Forms\Components;

use Closure;
use Filament\Forms\Components\Field;
use Illuminate\Contracts\Support\Arrayable;
use Filament\Forms\Components\Concerns\HasOptions;

class ImageRadioButton extends Field
{
    use HasOptions;

    protected string $view = 'filament.forms.components.image-radio-button';

    protected array | Arrayable | string | Closure | null $images = [];

    protected bool | Closure $inline = false;

    public function images(array | Arrayable | string | Closure | null $images): static
    {
        $this->images = $images;

        return $this;
    }

    public function getImages(): array
    {
        return $this->evaluate($this->images);
    }

    public function inline(bool | Closure $inline = true): static
    {
        $this->inline = $inline;

        return $this;
    }

    public function isInline(): bool
    {
        return $this->evaluate($this->inline);
    }
}
