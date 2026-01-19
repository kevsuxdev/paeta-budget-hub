@if($errors->any())
<div class="w-full p-4 mb-4 text-sm text-red-800 bg-red-200 rounded-lg" role="alert">
    <ul class="list-disc list-inside">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif