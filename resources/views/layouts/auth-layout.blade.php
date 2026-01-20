@extends('layouts.general-layout')

@section('content')
<main class="flex items-start gap-3 w-full min-h-screen">
    <x-navigation />
    <section class="overflow-y-auto overflow-hidden w-full max-h-screen">
        @yield('main-content')
    </section>
</main>
@endsection