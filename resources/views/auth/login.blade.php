<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Modalidad Dual - Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
</head>
<body class="min-h-screen">
    <div class="flex min-h-screen">
        <!-- Panel izquierdo (visible solo en md y superiores) -->
        <div class="hidden md:flex md:w-3/5 bg-[#202c54] flex-col p-8">
            <!-- Logo y título en la esquina superior izquierda -->
            <div class="flex items-center space-x-3 mb-20">
                <img src="{{ asset('images/lgo3.png') }}" alt="Logo" class="w-20 h-20 object-contain">
                <h1 class="text-5xl font-['Inter'] font-bold text-white">Modalidad Dual</h1>
            </div>
            <!-- Contenido central -->
            <div class="flex-grow flex items-center justify-center">
                <div class="max-w-lg text-white text-center">
                    <h2 class="text-3xl font-['Inter'] font-bold mb-6">Formando el futuro profesional</h2>
                    <p class="text-2xl font-['Inter']">Conectamos la educación con la experiencia práctica para crear profesionales excepcionales</p>
                </div>
            </div>
        </div>

        <!-- Barra superior móvil (visible solo en sm) -->
        <div class="md:hidden w-full fixed top-0 left-0 bg-[#202c54] p-4 z-10">
            <div class="flex items-center space-x-4">
                <img src="{{ asset('images/lgo3.png') }}" alt="Logo" class="w-8 h-8 object-contain">
                <h1 class="text-2xl font-['Inter'] font-bold text-white">Modalidad Dual</h1>
            </div>
        </div>

        <!-- Panel derecho (formulario) -->
        <div class="w-full md:w-2/5 flex items-center justify-center p-4 md:p-8 bg-white">
            <!-- Ajuste de margen superior para móviles debido a la barra fija -->
            <div class="w-full max-w-md mt-16 md:mt-0">
                <!-- Logo SVG -->
                <div class="flex justify-center mb-8">
                    <img src="{{ asset('images/lgo.png') }}" alt="Logo" class="w-24 h-24 object-contain">
                </div>
                <div class="text-center mb-6">
                    <h2 class="text-5xl font-['Inter'] font-bold text-gray-900">Bienvenido</h2>
                    <p class="text-gray-500 mt-2 text-lg font-['Inter']">Ingresa tus datos</p>
                </div>

                <form action="{{ route('login') }}" method="POST" class="space-y-6">
                    @csrf

                    @if ($errors->any())
                        <div class="text-base text-red-600 mb-4" role="alert">
                            <p>{{ $errors->first('email') }}</p>
                        </div>
                    @endif

                    <div class="space-y-8">
                        <div class="relative">
                            <input type="email" name="email" id="email" required placeholder="Correo"
                                   class="peer w-full border-b-2 border-gray-900 bg-transparent pt-2 pb-1.5 text-lg text-gray-900 placeholder-transparent focus:border-gray-900 focus:outline-none transition-all duration-300" />
                            <label for="email" class="absolute left-0 -top-6 text-base text-gray-500 transition-all duration-300 peer-placeholder-shown:top-2 peer-placeholder-shown:text-lg peer-placeholder-shown:text-gray-400 peer-focus:-top-6 peer-focus:text-base peer-focus:text-gray-500">Correo</label>
                        </div>

                        <div class="relative">
                            <input type="password" name="password" id="password" required placeholder="Contraseña"
                                   class="peer w-full border-b-2 border-gray-900 bg-transparent pt-2 pb-1.5 text-lg text-gray-900 placeholder-transparent focus:border-gray-900 focus:outline-none transition-all duration-300" />
                            <button type="button" onclick="togglePassword()" class="absolute right-0 top-2 text-gray-500 hover:text-gray-700 focus:outline-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" id="passwordIcon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                            <label for="password" class="absolute left-0 -top-6 text-base text-gray-500 transition-all duration-300 peer-placeholder-shown:top-2 peer-placeholder-shown:text-lg peer-placeholder-shown:text-gray-400 peer-focus:-top-6 peer-focus:text-base peer-focus:text-gray-500">Contraseña</label>
                        </div>
                    </div>

                    <button type="submit"
                            class="mt-4 w-full bg-[#e62d21] py-3 text-base font-semibold text-white hover:bg-[#d41f14] focus:outline-none focus:ring-2 focus:ring-[#e62d21] focus:ring-offset-2 transition duration-200 rounded-lg">
                        Ingresar
                    </button>

                    <div class="mt-0 text-center">
                        <button type="button" 
                                class="px-16 py-2 text-base bg-gray-100 text-gray-700 hover:bg-gray-200 font-medium transition duration-200 rounded-lg">
                            Registrarse
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @vite('resources/js/app.js')
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const passwordIcon = document.getElementById('passwordIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                `;
            } else {
                passwordInput.type = 'password';
                passwordIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                `;
            }
        }
    </script>
</body>
</html>