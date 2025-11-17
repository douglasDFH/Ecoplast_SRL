<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Ecoplast SRL</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gradient-to-br from-blue-50 to-blue-100 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-2xl p-8">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800">Ecoplast SRL</h1>
                <p class="text-gray-600 mt-2">Sistema de Gestión de Producción</p>
            </div>

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="/login">
                @csrf

                <div class="mb-4">
                    <label for="email" class="block text-gray-700 text-sm font-bold mb-2">
                        Email
                    </label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email', 'admin@ecoplast.com') }}"
                        required
                        autofocus
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                </div>

                <div class="mb-6">
                    <label for="password" class="block text-gray-700 text-sm font-bold mb-2">
                        Contraseña
                    </label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="123456"
                    >
                </div>

                <div class="flex items-center justify-between mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" class="h-4 w-4 text-blue-600">
                        <span class="ml-2 text-sm text-gray-600">Recordarme</span>
                    </label>
                </div>

                <button
                    type="submit"
                    class="w-full bg-gradient-to-r from-blue-500 to-blue-600 text-white font-bold py-3 px-4 rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200"
                >
                    Iniciar Sesión
                </button>
            </form>

            <div class="mt-6 text-center text-sm text-gray-600">
                <p>Credenciales por defecto:</p>
                <p class="font-mono bg-gray-100 p-2 rounded mt-2">
                    admin@ecoplast.com / 123456
                </p>
            </div>
        </div>
    </div>
</body>
</html>
