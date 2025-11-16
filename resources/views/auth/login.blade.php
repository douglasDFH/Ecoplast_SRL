<x-guest-layout>
    <div class="mb-8 text-center">
        <h2 class="text-3xl font-bold mb-2" style="background: linear-gradient(135deg, #455A64, #263238); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">Iniciar Sesión</h2>
        <p class="text-sm" style="color: #607D8B;">Ingrese sus credenciales</p>
    </div>

    <!-- Session Status -->
    @if (session('status'))
        <div class="mb-4 p-4 rounded-2xl" style="background: linear-gradient(145deg, #e8f5e9, #c8e6c9); box-shadow: inset 5px 5px 10px #b3d7b5, inset -5px -5px 10px #ffffff;">
            <p class="text-sm text-center" style="color: #2E7D32;">{{ session('status') }}</p>
        </div>
    @endif

    <!-- Validation Errors -->
    @if ($errors->any())
        <div class="mb-4 p-4 rounded-2xl" style="background: linear-gradient(145deg, #ffebee, #ffcdd2); box-shadow: inset 5px 5px 10px #e0b3b8, inset -5px -5px 10px #ffffff;">
            <div class="font-medium text-sm mb-2 text-center" style="color: #c62828;">
                {{ __('Whoops! Something went wrong.') }}
            </div>
            <ul class="text-xs space-y-1" style="color: #d32f2f;">
                @foreach ($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block font-semibold text-sm mb-3" style="color: #455A64;">Correo Electrónico</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5" style="color: #607D8B;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                    </svg>
                </div>
                <input id="email" class="block w-full pl-12 pr-4 py-4 rounded-2xl border-0 focus:outline-none transition-all" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="admin@ecoplast.com" style="background: linear-gradient(145deg, #e3f2fd, #bbdefb); box-shadow: inset 8px 8px 16px #b3d4f1, inset -8px -8px 16px #f3ffff; color: #263238;" />
            </div>
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block font-semibold text-sm mb-3" style="color: #455A64;">Contraseña</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5" style="color: #607D8B;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <input id="password" class="block w-full pl-12 pr-4 py-4 rounded-2xl border-0 focus:outline-none transition-all" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" style="background: linear-gradient(145deg, #e3f2fd, #bbdefb); box-shadow: inset 8px 8px 16px #b3d4f1, inset -8px -8px 16px #f3ffff; color: #263238;" />
            </div>
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between pt-2">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" class="w-5 h-5 rounded border-0 cursor-pointer" name="remember" style="background: linear-gradient(145deg, #e3f2fd, #bbdefb); box-shadow: inset 3px 3px 6px #b3d4f1, inset -3px -3px 6px #f3ffff;">
                <span class="ml-3 text-sm font-medium" style="color: #455A64;">Recordarme</span>
            </label>
        </div>

        <div class="pt-4">
            <button type="submit" class="w-full flex items-center justify-center px-6 py-4 border-0 rounded-2xl font-bold text-sm text-white uppercase tracking-wide transition-all duration-200 active:scale-95" style="background: linear-gradient(145deg, #2E7D32, #4CAF50); box-shadow: 12px 12px 24px #b3d4f1, -12px -12px 24px #f3ffff;" onmouseover="this.style.boxShadow='8px 8px 16px #b3d4f1, -8px -8px 16px #f3ffff'" onmouseout="this.style.boxShadow='12px 12px 24px #b3d4f1, -12px -12px 24px #f3ffff'">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
                Iniciar Sesión
            </button>
        </div>
    </form>
</x-guest-layout>

