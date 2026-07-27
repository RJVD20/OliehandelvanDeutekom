@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-turbo-blue/25 focus:border-turbo-gold focus:ring-turbo-gold rounded-md shadow-sm']) }}>
