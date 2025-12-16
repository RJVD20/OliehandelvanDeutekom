<nav class="bg-white border-b border-gray-200">
  <div class="max-w-7xl mx-auto px-4">
    <div class="flex items-center justify-between h-16">

<a href="/" class="flex items-center space-x-2">
<img src="/images/logovd.png" alt="Logo" class="h-14 w-auto">
  <!-- <span class="text-xl font-bold text-gray-900">
    Webshop
  </span>
</a> -->


      <!-- Center: Menu -->
      <div class="hidden md:flex absolute left-1/2 -translate-x-1/2 space-x-8">
        <a href="#" class="text-gray-700 hover:text-green-600 font-medium">Home</a>
        <a href="#" class="text-gray-700 hover:text-green-600 font-medium">Informatie</a>
        <a href="#" class="text-gray-700 hover:text-green-600 font-medium">Vloeistoffen</a>
        <a href="#" class="text-gray-700 hover:text-green-600 font-medium">Kachels</a>
        <a href="#" class="text-gray-700 hover:text-green-600 font-medium">Locaties</a>
      </div>

      <!-- Right: Actions -->
      <div class="flex items-center space-x-4">
        <a href="{{ route('cart.index') }}" class="text-gray-700 hover:text-green-700">
            🛒 Winkelmand
        </a>
        <a
          href="#"
          class="hidden sm:inline-block px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700"
        >
          Inloggen
        </a>

        <!-- Mobile menu button -->
        <button
          data-collapse-toggle="mobile-menu"
          type="button"
          class="md:hidden inline-flex items-center p-2 text-gray-500 rounded-lg hover:bg-gray-100"
        >
          ☰
        </button>
      </div>
    </div>
  </div>

  <!-- Mobile menu -->
  <div id="mobile-menu" class="hidden md:hidden border-t">
    <div class="px-4 py-3 space-y-2">
      <a href="#" class="block text-gray-700">Home</a>
      <a href="#" class="block text-gray-700">Shop</a>
      <a href="#" class="block text-gray-700">Categorieën</a>
      <a href="#" class="block text-gray-700">Contact</a>
    </div>
  </div>
</nav>
