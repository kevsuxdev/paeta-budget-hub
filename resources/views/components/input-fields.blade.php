@props(["name" => "","type" => "text", "label" => "", "value" => "", "class" => "", "placeholder" => ""])
<article>
    <label class="text-sm font-medium">{{ $label }}</label>
    <input type="{{ $type }}" name="{{ $name }}" value="{{ $value }}" placeholder="{{ $placeholder }}" class="w-full border border-white/50 rounded-xl p-2 text-sm px-3 {{ $class }}" />
</article>