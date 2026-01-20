@props(['href' => "", 'active' => false])

<a href="{{ $href }}" class="block py-2 px-4 rounded text-sm {{ $active ? 'bg-secondary text-primary' : 'text-background hover:bg-secondary hover:text-primary' }} transition-colors">{{ $slot }}</a>