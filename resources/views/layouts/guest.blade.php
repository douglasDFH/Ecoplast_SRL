<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Ecoplast SRL') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0" style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);">
            <div class="mb-8 text-center">
                <div class="w-28 h-28 mx-auto mb-6 rounded-full flex items-center justify-center" style="background: linear-gradient(145deg, #e3f2fd, #bbdefb); box-shadow: 20px 20px 60px #b3d4f1, -20px -20px 60px #f3ffff;">
                    <div class="w-20 h-20 rounded-full flex items-center justify-center" style="background: linear-gradient(145deg, #2E7D32, #4CAF50); box-shadow: inset 5px 5px 10px #1B5E20, inset -5px -5px 10px #66BB6A;">
                        <svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"/>
                        </svg>
                    </div>
                </div>
                <h1 class="text-4xl font-bold mb-2" style="background: linear-gradient(135deg, #1565C0, #0D47A1); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; text-shadow: 2px 2px 4px rgba(0,0,0,0.1);">ECOPLAST SRL</h1>
                <p class="text-sm" style="color: #1565C0;">Sistema de Gestión Industrial</p>
            </div>

            <div class="w-full sm:max-w-md px-10 py-10 overflow-hidden rounded-3xl" style="background: linear-gradient(145deg, #e3f2fd, #bbdefb); box-shadow: 20px 20px 60px #b3d4f1, -20px -20px 60px #f3ffff;">
                {{ $slot }}
            </div>
            
            <div class="mt-8 text-center text-sm" style="color: #1565C0; text-shadow: 1px 1px 2px rgba(255,255,255,0.5);">
                <p>&copy; {{ date('Y') }} Ecoplast SRL. Todos los derechos reservados.</p>
            </div>
        </div>
    </body>
</html>
