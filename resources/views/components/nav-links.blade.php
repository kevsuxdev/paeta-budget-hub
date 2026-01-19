@props(['href' => "", 'active' => false])

<a href="{{ $href }}" class=" {{ $active ? 'active' : '' }}">{{ $slot }}</a>