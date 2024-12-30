<?php

namespace App\Http\Livewire;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Livewire\Component;

class ViewProduct extends Component implements HasForms, HasInfolists
{
    use InteractsWithForms;
    use InteractsWithInfolists;

    public $product;

    public function mount($productId)
    {
        // Simulasi data produk (atau ambil dari database)
        $this->product = [
            'name' => 'Produk Contoh',
            'price' => 100000,
            'description' => 'Deskripsi singkat produk.',
            'status' => 'available',
            'category' => 'Kategori A',
        ];
    }

    public function render()
    {
        return view('livewire.view-product', [
            'product' => $this->product,
        ]);
    }
}
