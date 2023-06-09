<nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand text-secondary" href="#">
            <img src="{{ asset('images/logo.svg') }}" class="logo" alt="Majestic Education"/>
            <label style="font-size: 18px;">MED - Majestic Education Digital</label>
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Left Side Of Navbar -->
            <ul class="navbar-nav mr-auto"></ul>

            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ml-auto">
                <!-- Authentication Links -->
                @auth
                    @include('partials.navigation.users.'.\App\User::navigation())
                    @include('partials.navigation.logout')
                @endauth
            </ul>
        </div>
    </div>
</nav>