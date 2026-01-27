@props(["name" => "","type" => "text", "label" => "", "value" => "", "class" => "", "placeholder" => "", 'required' => true])
<article>
    <label class="text-sm font-medium">{{ $label }}</label>
    <input type="{{ $type }}" name="{{ $name }}" value="{{ $value }}" placeholder="{{ $placeholder }}" {{ $required ? 'required' : '' }} {{ $attributes->merge(['class' => 'w-full border border-white/50 rounded-xl p-2 text-sm px-3 ' . $class]) }} />
</article>