# Anatomy of a Mingle

A Mingle is composed by a Livewire component class and a JS file that loads a JS component.

## Livewire Component

Let's take a look at a component's class:

```php
<?php

namespace App\Livewire;

use Ijpatricio\Mingle\Concerns\InteractsWithMingles;
use Ijpatricio\Mingle\Contracts\HasMingles;
use Livewire\Component;

class ChatApp extends Component implements HasMingles
{
    use InteractsWithMingles;

    public function component(): string
    {
        return 'resources/js/ChatApp/index.js';
    }

    public function mingleData()
    {
        return [
            'message' => 'Message in a bottle 🍾',
        ];
    }

    public function doubleIt($amount)
    {
        return $amount * 2;
    }
}
```

- Method `component()`
  - Returns the path to the JS file that will load the JS component.
- Method `mingleData()`
  - Returns an array of data that will be passed to the JS component on initialization.
- Method `doubleIt()`
  - It's a method that can be called from the JS component, like so: `$wire.doubleIt(5)`

## Depending on the framework flavor, the mechanisms vary.

Check the specific sections for more details.

- [Anatomy - Vue specifics](./anatomy-vue-specifics) 
- [Anatomy - React specifics](./anatomy-react-specifics) 
