<form action="{{ route('auth.login') }}" method="POST" class="w-1/4 p-5 border border-black/20 rounded-lg flex flex-col gap-4 bg-background">
    @csrf
    <article>
        <h1 class="text-xl font-semibold">Welcome to Paeta Budget Hub</h1>
        <p class="text-sm">Please enter your credentials to access your account.</p>
    </article>
    <x-input-fields label="Email Address" name="email" type="email" value="{{ old('email') }}" />
    <x-input-fields label="Password" name="password" type="password" value="{{ old('password') }}" />
    <x-errors/>
    <x-button>Sign In</x-button>
</form>