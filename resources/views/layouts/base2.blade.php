<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0" />
  <title>@yield('title')</title>

  {{-- sección opcional para inyectar CSS/JS extra en cada layout --}}
  @stack('vite')
</head>
<body class="bg-gray-100 min-h-screen flex flex-col overflow-x-hidden">

  <!-- Header -->
  <header class="bg-[#202c54] shadow-md py-4 px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col sm:flex-row items-center justify-start space-y-4 sm:space-y-0 sm:space-x-4">
      <img src="{{ asset('images/lgo3.png') }}" alt="Logo" class="w-10 h-10 object-contain">
      <h1 class="text-2xl font-bold text-white">
        @yield('title')
      </h1>
    </div>
  </header>

  <!-- Contenido principal -->
  @yield('main')

</body>
</html>
