<!-- FLOATING CHAT & SUPPORT WIDGET (MESSENGER & ZALO) -->
<div class="fixed bottom-24 md:bottom-7 left-3.5 md:left-6 z-40 flex flex-col items-center gap-3 select-none">
    
    <!-- 1. MESSENGER BUTTON -->
    <div class="relative group flex items-center">
        <!-- Ripple Pulse Effect -->
        <span class="absolute -inset-1 rounded-full bg-[#0084FF] opacity-40 animate-ping pointer-events-none"></span>
        
        <a 
            href="{{ $settings['contact_messenger_url'] ?? 'https://m.me/luuhoang.it' }}" 
            target="_blank" 
            rel="noopener noreferrer"
            class="relative z-10 w-[54px] h-[54px] md:w-[58px] md:h-[58px] flex items-center justify-center filter drop-shadow-xl hover:scale-110 active:scale-95 transition-transform duration-200 cursor-pointer shrink-0"
            aria-label="Chat qua Facebook Messenger"
        >
            <img 
                src="{{ asset('images/icons/messenger.svg') }}" 
                alt="Messenger" 
                class="w-full h-full object-contain pointer-events-none"
                width="64"
                height="64"
            />
        </a>

        <!-- Tooltip Label on Hover (Desktop) -->
        <div class="absolute left-full ml-3 hidden md:group-hover:flex items-center pointer-events-none z-20">
            <span class="bg-gray-900/95 text-white text-xs font-bold px-3.5 py-1.5 rounded-xl whitespace-nowrap shadow-xl border border-gray-700/80 flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-[#0084FF] animate-pulse"></span>
                <span>Chat Messenger</span>
            </span>
        </div>
    </div>

    <!-- 2. ZALO BUTTON -->
    <div class="relative group flex items-center">
        <!-- Ripple Pulse Effect -->
        <span class="absolute -inset-1 rounded-full bg-[#0068FF] opacity-40 animate-ping pointer-events-none" style="animation-delay: 300ms;"></span>
        
        <a 
            href="{{ $settings['contact_zalo_url'] ?? 'https://zalo.me/0973797151' }}" 
            target="_blank" 
            rel="noopener noreferrer"
            class="relative z-10 w-[54px] h-[54px] md:w-[58px] md:h-[58px] flex items-center justify-center filter drop-shadow-xl hover:scale-110 active:scale-95 transition-transform duration-200 cursor-pointer shrink-0"
            aria-label="Chat qua Zalo: 0973.797.151"
        >
            <img 
                src="{{ asset('images/icons/zalo.svg') }}" 
                alt="Zalo" 
                class="w-full h-full object-contain pointer-events-none"
                width="64"
                height="64"
            />
        </a>

        <!-- Tooltip Label on Hover (Desktop) -->
        <div class="absolute left-full ml-3 hidden md:group-hover:flex items-center pointer-events-none z-20">
            <span class="bg-gray-900/95 text-white text-xs font-bold px-3.5 py-1.5 rounded-xl whitespace-nowrap shadow-xl border border-gray-700/80 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-[#0068FF] animate-pulse"></span>
                <span>Chat Zalo:</span>
                <span class="text-blue-400 font-mono font-black">0973.797.151</span>
            </span>
        </div>
    </div>

</div>
