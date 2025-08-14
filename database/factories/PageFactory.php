<?php

namespace Database\Factories;

use App\Models\Page;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class PageFactory extends Factory
{
    protected $model = Page::class;

    public function definition(): array
    {
        return [
            'title'        => $this->faker->word(),
            'slug'         => $this->faker->slug(),
            'body'         => $this->faker->words(),
            'excerpt'      => $this->faker->word(),
            'published_at' => Carbon::now(),
        ];
    }
}
