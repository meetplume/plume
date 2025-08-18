<section {{ $attributes->class('container scroll-mt-4') }}>
    @if (! empty($title))
        <h{{ (string) ($attributes['level'] ?? '2') }} @class([
            'font-bold tracking-widest text-center text-black dark:text-white uppercase text-balance mb-8',
        ])>
            {!! $title !!}
        </h{{ (string) ($attributes['level'] ?? '2') }}>
    @endif

    {{ $slot }}
</section>
