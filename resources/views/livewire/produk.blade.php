<?php

use Livewire\Volt\Component;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;

new #[Layout('layouts.app')] class extends Component
{
    public $products;
    public array $cart = [];
    public array $quantities = [];

    public function mount() {
        $this->products = Product::where('is_active', true)->get();

        foreach ($this->products as $product) {
            $this->quantities[$product->id] = 1;
        }
    }

    public function addToCart($productId): void {
        $quantity = max(1, (int) ($this->quantities[$productId] ?? 1));

        if(isset($this->cart[$productId])){
            $this->cart[$productId] += $quantity;
        } else {
            $this->cart[$productId] = $quantity;
        }
    }

    public function removeFromCart($productId): void
    {
        unset($this->cart[$productId]);
    }

    public function getCartItemsProperty()
    {
        return collect($this->cart)->map(function ($quantity, $productId) {
            $product = $this->products->firstWhere('id', $productId);

            return [
                'product' => $product,
                'quantity' => $quantity,
                'subtotal' => $product->price * $quantity,
            ];
        });
    }

    public function getCartTotalProperty()
    {
        return $this->cartItems->sum('subtotal');
    }

    public function placeOrder(): void
    {
        if (empty($this->cart)) {
            $this->addError('cart', 'Keranjang masih kosong, pilih produk dulu.');
            return;
        }

        DB::transaction(function () {
            $order = Order::create([
                'customer_id' => Auth::id(),
                'status' => 'pending',
                'order_date' => now(),
            ]);

            foreach ($this->cart as $productId => $quantity) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                ]);
            }
        });
        $this->cart = [];
        session()->flash('success', 'Pesanan berhasil dibuat! Terima kasih.');
    }
}; ?>

<div class="max-w-4xl mx-auto py-8 px-4">
    <h1 class="text-2xl font-bold mb-6">Pesan Air Isi Ulang</h1>

    @if (session('success'))
        <div class="bg-green-100 text-green-800 p-4 rounded-lg mb-6">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid gap-4 mb-8">
        @foreach ($products as $product)
            <div class="flex items-center justify-between bg-white p-4 rounded-lg shadow">
                <div>
                    <h3 class="font-semibold">{{ $product->name }}</h3>
                    <p class="text-gray-500">Rp {{ number_format($product->price, 0, ',', '.') }} / {{ $product->unit }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <input
                        type="number"
                        min="1"
                        wire:model="quantities.{{ $product->id }}"
                        class="w-16 border rounded px-2 py-1"
                    >
                    <button
                        wire:click="addToCart({{ $product->id }})"
                        class="bg-primary bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
                    >
                        Tambah
                    </button>
                </div>
            </div>
        @endforeach
    </div>

    <div class="bg-gray-50 p-6 rounded-lg">
        <h2 class="text-lg font-semibold mb-4">Keranjang Kamu</h2>

        @error('cart')
            <p class="text-red-600 mb-4">{{ $message }}</p>
        @enderror

        @forelse ($this->cartItems as $productId => $item)
            <div class="flex items-center justify-between border-b py-2">
                <div>
                    <p class="font-medium">{{ $item['product']->name }}</p>
                    <p class="text-sm text-gray-500">{{ $item['quantity'] }} x Rp {{ number_format($item['product']->price, 0, ',', '.') }}</p>
                </div>
                <div class="flex items-center gap-4">
                    <span class="font-semibold">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</span>
                    <button wire:click="removeFromCart({{ $productId }})" class="text-red-600 text-sm">Hapus</button>
                </div>
            </div>
        @empty
            <p class="text-gray-400">Keranjang masih kosong.</p>
        @endforelse

        @if ($this->cartItems->isNotEmpty())
            <div class="flex items-center justify-between mt-4 pt-4 border-t">
                <span class="font-bold text-lg">Total: Rp {{ number_format($this->cartTotal, 0, ',', '.') }}</span>
                <button
                    wire:click="placeOrder"
                    class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700"
                >
                    Buat Pesanan
                </button>
            </div>
        @endif
    </div>
</div>
