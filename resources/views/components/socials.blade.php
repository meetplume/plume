@php
    use App\Enums\SiteSettings;
    $links = SiteSettings::SOCIALS->get();
    $fillIcon = SiteSettings::SOCIALS_ICON_FILL->get();
@endphp

@if(!empty($links))
    <div class="socials flex gap-3 items-center">
        @foreach ($links as $link)
            @php
                /** @var \App\Enums\Socials $network */
                $network = $link['social_network'];
            @endphp

            <a href="{{ $link['url'] }}"
               target="_blank"
               rel="noopener noreferrer"
               class="hover:opacity-75 transition"
               title="{{ $network->getLabel() }}">
                <x-icon
                    :name="$fillIcon ? $network->fill() : $network->getIcon()"
                    class="socials-icon w-6 h-6"
                />
            </a>
        @endforeach
    </div>
@endif
