@props(['review'])

<div class="bg-[#FAF6F0] rounded-2xl p-6 border border-gray-100 shadow-sm flex flex-col justify-between space-y-6">
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <div class="text-amber-400 text-sm tracking-wider">{{ str_repeat('★', $review->rating) }}</div>
            @if($review->verified)
                <span class="text-[10px] font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full flex items-center gap-1">
                    <span>✔</span> ĐÃ MUA TẠI GAO
                </span>
            @endif
        </div>
        <p class="text-xs sm:text-sm font-medium text-gray-700 leading-relaxed italic">
            "{{ $review->content }}"
        </p>
    </div>
    <div class="flex items-center gap-3 pt-2 border-t border-gray-200/60">
        <div class="w-10 h-10 rounded-full {{ $review->avatar_bg ?? 'bg-rose-200 text-rose-800' }} font-black text-xs flex items-center justify-center">
            {{ $review->avatar ?? mb_substr($review->customer_name, 0, 2) }}
        </div>
        <div>
            <div class="font-bold text-xs text-gray-900">{{ $review->customer_name }}</div>
            <div class="text-[11px] text-gray-500">{{ $review->location }}</div>
        </div>
    </div>
</div>
