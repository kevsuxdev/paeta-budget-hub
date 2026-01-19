@props(["name" => "","type" => "text", "label" => "", "value" => ""])
<article>
    <label class="text-sm font-medium">{{ $label }}</label>
    <input type="{{ $type }}" name="{{ $name }}" value="{{ $value }}" class="w-full border border-black/20 rounded-md p-2 text-sm" />
</article>