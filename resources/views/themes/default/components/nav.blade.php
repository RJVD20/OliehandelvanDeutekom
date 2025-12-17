<nav class="bg-white border-b border-gray-200">
  <div class="max-w-7xl mx-auto px-4">
    <div class="flex items-center justify-between h-16">

<a href="/" class="flex items-center space-x-2">
<img src="/images/logovd.png" alt="Logo" class="h-12 w-auto">
  <!-- <span class="text-xl font-bold text-gray-900">
    Webshop
  </span>
</a> -->


      <!-- Center: Menu -->
      <div class="hidden md:flex absolute left-1/2 -translate-x-1/2 space-x-8">
        <a href="/" class="text-gray-700 hover:text-green-600 font-medium">Home</a>
        <a href="{{ route('informatie') }}" class="text-gray-700 hover:text-green-600 font-medium">Informatie</a>
        <a href="{{ route('products.index') }}" class="text-gray-700 hover:text-green-600 font-medium">Producten</a>
        <a href="{{ route('locaties') }}" class="text-gray-700 hover:text-green-600 font-medium">Locaties</a>
      </div>

      <!-- Right: Actions -->
      <div class="flex items-center space-x-4">
<div
    x-data="{ count: {{ collect(session('cart', []))->sum('quantity') }} }"
    @cart-updated.window="count = $event.detail"
    class="relative"
>
    <a href="{{ route('cart.index') }}" class="text-gray-700 hover:text-green-700">
        🛒
    </a>

    <template x-if="count > 0">
        <span
            x-text="count"
            class="absolute -top-2 -right-2 bg-green-600 text-white text-xs rounded-full px-2"
        ></span>
    </template>
</div>

@auth
<div
    x-data="{ open: false }"
    class="relative"
>
    <button
        @click="open = !open"
        class="flex items-center gap-2 text-sm font-medium text-gray-700 hover:text-green-700"
    >
        {{ auth()->user()->name }}
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <!-- Dropdown -->
    <div
        x-show="open"
        @click.outside="open = false"
        x-transition
        class="absolute right-0 mt-2 w-44 bg-white border rounded-lg shadow-lg z-50"
    >
        <a
            href="{{ route('account.dashboard') }}"
            class="block px-4 py-2 text-sm hover:bg-gray-100"
        >
            Account
        </a>

        <a
            href="{{ route('account.orders') }}"
            class="block px-4 py-2 text-sm hover:bg-gray-100"
        >
            Bestellingen
        </a>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button
                type="submit"
                class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100"
            >
                Logout
            </button>
        </form>
    </div>
</div>
@else
    <a
        href="{{ route('login') }}"
        class="text-sm font-medium text-gray-700 hover:text-green-700"
    >
        Login
    </a>
@endauth



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
