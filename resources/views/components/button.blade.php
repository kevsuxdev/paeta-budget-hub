@props(['type' => 'submit'])
<button type="{{ $type }}" class="bg-primary text-background p-2 rounded-lg text-sm cursor-pointer active:scale-98 duration-200">{{$slot}}</button>