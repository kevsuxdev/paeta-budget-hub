<form action="{{ route('auth.login') }}" method="POST" class="w-1/3 p-5 border border-black/20 rounded-lg flex flex-col gap-4 bg-accent text-white">
    @csrf
    <article  class="flex items-start justify-between">
        <div  class="space-y-2">
            <h1 class="text-2xl font-semibold">Welcome to Paeta Budget Hub</h1>
            <p class="text-sm">Please enter your credentials to access your account.</p>
        </div>
        <img src="{{ asset('assets/logo.png') }}" alt="Paeta Logo" class="w-12 h-12 object-contain"/>
    </article>
    <x-input-fields label="Email Address" name="email" type="email" value="{{ old('email') }}" placeholder="ex. johndoe@paeta.gov.ph"/>
    <x-input-fields label="Password" name="password" type="password" value="{{ old('password') }}" placeholder="***************" />
    <x-errors/>
    <x-button>Sign In</x-button>
</form>