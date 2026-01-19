@extends('layouts.general-layout')

@section('content')
<main class="flex items-start gap-3 w-full min-h-screen">
    <x-navigation />
    @yield('main-content')
</main>
@endsection