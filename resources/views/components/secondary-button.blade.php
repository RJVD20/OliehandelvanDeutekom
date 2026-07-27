<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2 bg-white border border-turbo-blue/25 rounded-md font-semibold text-xs text-turbo-ink uppercase tracking-widest shadow-sm hover:border-turbo-gold hover:text-turbo-dark focus:outline-none focus:ring-2 focus:ring-turbo-gold focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
