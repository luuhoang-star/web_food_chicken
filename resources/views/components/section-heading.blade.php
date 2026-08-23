@props([
    'badge' => null,
    'badgeIcon' => null,
    'title' => '',
    'subtitle' => null,
    'align' => 'center',
    'actionUrl' => null,
    'actionText' => null,
])

@if($align === 'left' && ($actionUrl || $actionText))
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-10">
        <div>
            @if($badge)
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-red-50 text-red-600 text-xs font-black uppercase tracking-wider mb-2">
                    @if($badgeIcon)
                        <span>{{ $badgeIcon }}</span>
                    @endif
                    <span>{{ $badge }}</span>
                </div>
            @endif
            <h2 class="text-3xl sm:text-4xl font-black text-gray-900 tracking-tight">
                {{ $title }}
            </h2>
            @if($subtitle)
                <p class="text-gray-500 text-sm sm:text-base font-medium mt-1">
                    {{ $subtitle }}
                </p>
            @endif
        </div>
        @if($actionUrl && $actionText)
            <div>
                <a 
                    href="{{ $actionUrl }}" 
                    class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-red-50 hover:bg-red-100 text-red-600 font-extrabold text-sm border border-red-200 transition-all hover:gap-3 group shadow-xs cursor-pointer"
                >
                    <span>{{ $actionText }}</span>
                    <span class="group-hover:translate-x-0.5 transition-transform">→</span>
                </a>
            </div>
        @endif
    </div>
@else
    <div class="text-center space-y-2 {{ $attributes->get('class', 'mb-12') }}">
        @if($badge)
            <div class="inline-flex items-center gap-2 text-red-600 font-extrabold text-sm uppercase tracking-widest">
                @if($badgeIcon)
                    <span>{{ $badgeIcon }}</span>
                @endif
                <span>{{ $badge }}</span>
            </div>
        @endif
        <h2 class="text-3xl sm:text-4xl font-black text-gray-900 tracking-tight uppercase">
            {{ $title }}
        </h2>
        @if($subtitle)
            <p class="text-gray-500 text-sm sm:text-base font-medium max-w-xl mx-auto">
                {{ $subtitle }}
            </p>
        @endif
    </div>
@endif
