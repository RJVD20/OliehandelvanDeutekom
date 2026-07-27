@props([
    'detailed' => false,
])

@php
    use App\Models\Setting;

    $cmsValue = function (string $key, string $default) {
        $value = Setting::get($key, null);

        return is_string($value) && trim($value) !== ''
            ? trim($value)
            : $default;
    };

    $compactText = $cmsValue('delivery_compact_text', 'Bezorging binnen 4–8 werkdagen');
    $deliveryTitle = $cmsValue('delivery_title', 'Bezorging via onze eigen bezorgdienst');
    $deliveryRules = [
        $cmsValue('delivery_rule_1', 'Verwachte levering binnen 4–8 werkdagen.'),
        $cmsValue('delivery_rule_2', 'Vanaf 3 jerrycans van 20 liter bezorgen we gratis.'),
        $cmsValue('delivery_rule_3', 'Onder 3 jerrycans geldt €5 bezorgvergoeding per bestelling.'),
        $cmsValue('delivery_rule_4', 'Express Premium: voor 12.00 uur besteld, de volgende dag geleverd voor €7,50 extra per jerrycan.'),
    ];
@endphp

@if($detailed)
    <aside {{ $attributes->class(['delivery-notice delivery-notice--detailed']) }} aria-label="Informatie over bezorging">
        <span class="delivery-notice__icon delivery-notice__icon--large" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 6h11v11H3z" />
                <path d="M14 9h3l4 4v4h-7z" />
                <circle cx="7" cy="18" r="2" />
                <circle cx="17" cy="18" r="2" />
            </svg>
        </span>

        <div>
            <h2 class="delivery-notice__title">{{ $deliveryTitle }}</h2>
            <ul class="delivery-notice__rules">
                @foreach($deliveryRules as $rule)
                    <li>{{ $rule }}</li>
                @endforeach
            </ul>
        </div>
    </aside>
@else
    <div {{ $attributes->class(['delivery-notice delivery-notice--compact']) }}>
        <span class="delivery-notice__icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 7h11v10H3z" />
                <path d="M14 10h3l4 4v3h-7z" />
                <circle cx="7" cy="18" r="1.7" />
                <circle cx="17" cy="18" r="1.7" />
            </svg>
        </span>
        <span>{{ $compactText }}</span>
    </div>
@endif
