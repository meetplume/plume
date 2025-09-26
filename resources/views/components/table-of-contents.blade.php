@props(['toc' => []])

@if (!empty($toc))
    <div class="toc-wrapper">
        <h3 class="toc-title text-lg font-medium text-gray-900 dark:text-gray-200 mb-3">
            {{ __('Summary') }}
        </h3>
        <ul {{ $attributes->merge(['class' => 'space-y-2 toc-list']) }}>
            @foreach ($toc as $item)
                <li class="{{ match($item['level']){
                        2 => 'pl-4',
                        3 => 'pl-8',
                        4 => 'pl-12',
                        default => ''
                    } }} toc-list-item">
                    <a href="#{{ $item['id'] }}"
                       class="toc-list-link text-gray-500 dark:text-gray-500 hover:text-primary-600 dark:hover:text-primary-400 transition-all duration-200 flex items-center transform hover:translate-x-1">
                        <span class="">{{ $item['text'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
@endif
