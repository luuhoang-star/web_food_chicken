@props(['benefit'])

<div class="flex items-center gap-4 px-4 pt-4 md:pt-0">
    <div class="w-12 h-12 rounded-2xl {{ $benefit->color_class ?? 'bg-red-50 text-red-600' }} flex items-center justify-center text-2xl shrink-0 shadow-xs">
        {{ $benefit->icon }}
    </div>
    <div>
        <h4 class="font-extrabold text-gray-900 text-base">{{ $benefit->title }}</h4>
        <p class="text-xs text-gray-500 font-medium">{{ $benefit->description }}</p>
    </div>
</div>
