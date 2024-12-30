<!-- resources/views/components/room-card.blade.php -->
<div class="relative flex flex-col p-4 rounded-lg {{ $statusColor }} transition-all hover:scale-105 gap-2">
    <div class="flex items-center justify-between">
        <span class="text-xl font-bold {{ $textColor }}">{{ $number }}</span>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 {{ $textColor }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />

        </svg>
        
    </div>
    <div class="flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 {{ $textColor }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
        </svg>
        <div class="flex flex-col">
            <span class="text-sm font-medium {{ $textColor }}">Rp {{ $price }}{{ $payment_category }}</span>
        </div>
    </div>
    <div class="flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 {{ $textColor }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
        </svg>
        
    </div>
</div>