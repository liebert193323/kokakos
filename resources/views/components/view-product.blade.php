<div class="p-4 bg-white shadow rounded">
    <h1 class="text-xl font-semibold">Detail Produk</h1>
    <div class="mt-4">
        <div class="mb-2">
            <span class="font-bold">Nama Produk:</span> {{ $product['name'] }}
        </div>
        <div class="mb-2">
            <span class="font-bold">Harga:</span> Rp {{ number_format($product['price'], 0, ',', '.') }}
        </div>
        <div class="mb-2">
            <span class="font-bold">Deskripsi:</span> {{ $product['description'] }}
        </div>
        <div class="mb-2">
            <span class="font-bold">Status:</span> 
            <span class="{{ $product['status'] === 'available' ? 'text-green-500' : 'text-red-500' }}">
                {{ $product['status'] === 'available' ? 'Tersedia' : 'Habis' }}
            </span>
        </div>
        <div>
            <span class="font-bold">Kategori:</span> {{ $product['category'] }}
        </div>
    </div>
</div>
