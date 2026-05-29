@props([
    'size'    => 260,
    'opacity' => 0.07,
    'class'   => '',
    'color'   => '#EAB308',
])

<svg
    width="{{ $size }}"
    height="{{ $size }}"
    viewBox="0 0 200 200"
    fill="none"
    xmlns="http://www.w3.org/2000/svg"
    class="sunflower-bg {{ $class }}"
    style="opacity: {{ $opacity }};"
    aria-hidden="true"
>
    {{-- Outer petals ring --}}
    @for ($i = 0; $i < 16; $i++)
        @php $angle = $i * 22.5; @endphp
        <ellipse
            cx="100" cy="100"
            rx="10" ry="28"
            fill="{{ $color }}"
            transform="rotate({{ $angle }}, 100, 100) translate(0, -52)"
        />
    @endfor

    {{-- Inner petals ring --}}
    @for ($i = 0; $i < 12; $i++)
        @php $angle = $i * 30 + 11.25; @endphp
        <ellipse
            cx="100" cy="100"
            rx="7" ry="18"
            fill="#7C3AED"
            transform="rotate({{ $angle }}, 100, 100) translate(0, -38)"
        />
    @endfor

    {{-- Center disc --}}
    <circle cx="100" cy="100" r="28" fill="{{ $color }}" />
    <circle cx="100" cy="100" r="22" fill="#7C3AED" opacity="0.6"/>
    <circle cx="100" cy="100" r="14" fill="{{ $color }}" opacity="0.9"/>

    {{-- Seed dots on center --}}
    @foreach ([
        [93,93],[100,90],[107,93],[110,100],[107,107],[100,110],[93,107],[90,100],
        [96,96],[104,96],[104,104],[96,104],[100,100]
    ] as $dot)
        <circle cx="{{ $dot[0] }}" cy="{{ $dot[1] }}" r="2.5" fill="#1C1917" opacity="0.25"/>
    @endforeach

    {{-- Leaf left --}}
    <path d="M100 130 Q70 145 65 170 Q85 155 100 145 Z" fill="#16A34A" opacity="0.5"/>
    {{-- Leaf right --}}
    <path d="M100 130 Q130 145 135 170 Q115 155 100 145 Z" fill="#16A34A" opacity="0.5"/>
    {{-- Stem --}}
    <rect x="98" y="128" width="4" height="46" rx="2" fill="#16A34A" opacity="0.4"/>
</svg>
