<!-- resources/views/components/room-card.blade.php -->
<div class="relative flex flex-col p-4 rounded-lg {{ $statusColor }} transition-all hover:scale-105">
    <div class="flex items-center justify-between">
        <span class="text-xl font-bold {{ $textColor }}">{{ $number }}</span>
        <span class="text-sm font-medium {{ $textColor }}">{{ $status }}</span>
    </div>
    <div class="flex items-center gap-2 mt-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 {{ $textColor }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span class="text-sm {{ $textColor }}">Berakhir: {{ $rent_end_date }}</span>
    </div>
    <div class="mt-2">
        <span class="text-sm font-medium {{ $textColor }}">Rp {{ $price }}{{ $payment_category }}</span>
    </div>
</div>