@php
    $rawNumber = config('contact.whatsapp_number');
    $message    = trim((string) config('contact.whatsapp_message'));
    $number     = $rawNumber ? preg_replace('/\D+/', '', $rawNumber) : null;
@endphp

@if ($number)
    @php
        $encodedMessage = $message !== '' ? rawurlencode($message) : null;
        $waLink = $encodedMessage ? "https://wa.me/{$number}?text={$encodedMessage}" : "https://wa.me/{$number}";
    @endphp
    <div class="fixed bottom-4 right-4 sm:bottom-6 sm:right-6 z-[60] pointer-events-none">
        <a
            href="{{ $waLink }}"
            target="_blank"
            rel="noopener noreferrer"
            aria-label="Open WhatsApp chat"
            class="pointer-events-auto inline-flex items-center justify-center rounded-full bg-[#25D366] text-white shadow-lg shadow-emerald-900/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-[#128C7E] focus-visible:ring-offset-transparent transition duration-200 ease-out motion-reduce:transition-none hover:bg-[#1EB858] motion-safe:hover:-translate-y-0.5"
        >
            <span class="sr-only">Chat via WhatsApp</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path d="M12.04 2a9.9 9.9 0 0 0-8.45 15.05L2 22l5.15-1.52A9.95 9.95 0 1 0 12.04 2Zm0 18.1a8.15 8.15 0 0 1-4.15-1.14l-.3-.18-3.06.9.92-2.97-.2-.31a8.15 8.15 0 1 1 6.78 3.7Zm4.73-6.1c-.26-.13-1.54-.76-1.78-.85s-.41-.13-.59.13-.68.85-.83 1-.31.19-.57.06a6.68 6.68 0 0 1-1.97-1.21 7.41 7.41 0 0 1-1.37-1.7c-.14-.25 0-.39.11-.52.1-.1.25-.26.37-.39s.16-.19.24-.32a.47.47 0 0 0 0-.45c-.06-.13-.59-1.43-.81-1.96s-.44-.45-.6-.46h-.52a1 1 0 0 0-.72.34 3 3 0 0 0-.94 2.22 5.32 5.32 0 0 0 1.09 2.76 12.14 12.14 0 0 0 4.68 4 16 16 0 0 0 1.6.59 3.86 3.86 0 0 0 1.78.11 2.9 2.9 0 0 0 1.9-1.36 2.36 2.36 0 0 0 .16-1.36c-.06-.09-.24-.15-.5-.28Z" />
            </svg>
        </a>
    </div>
@endif
