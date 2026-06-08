@php
    use App\Services\ExpiryValidationService;

    if (!isset($expiryDate)) {
        $expiryInfo = [
            'status' => 'no-expiry',
            'label' => translate('No Expiry'),
            'color' => 'gray',
            'icon' => 'fa-infinity'
        ];
    } else {
        $expiryInfo = ExpiryValidationService::getExpiryStatusInfo($expiryDate);
    }

    $colorClasses = [
        'green' => 'bg-green-100 text-green-800 border-green-200',
        'orange' => 'bg-orange-100 text-orange-800 border-orange-200',
        'yellow' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
        'red' => 'bg-red-100 text-red-800 border-red-200',
        'gray' => 'bg-gray-100 text-gray-800 border-gray-200'
    ];

    $sizeClasses = [
        'sm' => 'px-2 py-1 text-xs',
        'md' => 'px-3 py-1 text-sm',
        'lg' => 'px-4 py-2 text-base'
    ];

    $size = $size ?? 'sm';
    $showIcon = $showIcon ?? true;
@endphp

<span class="inline-flex items-center gap-1 rounded-full border {{ $colorClasses[$expiryInfo['color']] ?? $colorClasses['gray'] }} {{ $sizeClasses[$size] ?? $sizeClasses['sm'] }}">
    @if ($showIcon)
        <i class="fas {{ $expiryInfo['icon'] }} text-xs"></i>
    @endif
    {{ $expiryInfo['label'] }}
</span>