<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Categoria as CategoriaModel;
use App\Models\Venda as VendaModel; 
use App\Models\User as UserModel;
use App\Models\Produto as ProdutosModel;
use Illuminate\Support\Facades\DB; 

class Home extends Component
{
    use WithPagination;

    public $selectedCategory = null;
    public $showCategories = false;
    public $categories = [];
    public $searchCategory = '';
    public $searchProduct = '';
    public $perPage = 8; 
    public $cart = [];
    public $cartCount = 0; 

    public $showConfirmPurchaseModal = false;


    protected $updatesQueryString = ['searchProduct'];

    public function addToCart($productId)
    {
        $product = ProdutosModel::findOrFail($productId);

        $cart = session()->get('cart', []);
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity']++;
        } else {
            $cart[$productId] = [
                'name' => $product->nome,
                'price' => $product->valor,
                'quantity' => 1,
                'image_url' => $product->imagem_url,
            ];
        }

        session()->forget('error');
        session()->put('cart', $cart);
        $this->cart = $cart; 
        $this->updateCartCount(); 
        $this->emit('cartUpdated'); 
        return;
    }
    public function updateQuantity($productId, $quantity)
    {   
        if($quantity == null || $quantity == 0){
            session()->flash('error', 'Quantidade não pode ser nula.');
            $this->cart[$productId]['quantity'] = 1;
            $this->emit('cartUpdated');
            return;
        }

        $product = ProdutosModel::findOrFail($productId);
        $cart = session()->get('cart', []);
    
        $cart[$productId]['quantity'] = $quantity;
        $this->cart = $cart;
        session()->put('cart', $cart);

        if ($quantity > $product->quantidade) {
            session()->flash('error', 'Quantidade do produo '.$product->nome.' excede o limite disponível de '.$product->quantidade.' unidades.');
            $this->emit('cartUpdated');
            return;
        }
    
        if ($quantity <= 0) {
            $this->removeFromCart($productId);
            $this->emit('cartUpdated');
            return;
        }

        session()->forget('error');
        session()->flash('success', 'Quantidade atualizada com sucesso.');
        $this->updateCartCount(); 
        $this->emit('cartUpdated');
    }
    

    public function removeFromCart($productId)
    {
        $cart = session()->get('cart', []);
        unset($cart[$productId]);

        session()->put('cart', $cart);
        $this->cart = $cart;

        session()->forget('error');
        $this->emit('cartUpdated');
        $this->updateCartCount(); 

    }
    public function finalizePurchase()
    {   
        DB::beginTransaction();
    
        try {
            $cart = session()->get('cart', []);
            if (empty($cart)) {
                session()->flash('error-venda', 'O carrinho está vazio.');
                $this->hidePurchaseModal(); 
                return;
            }
    
            foreach ($cart as $productId => $details) {
                $product = ProdutosModel::find($productId);
    
                if ($product->quantidade < $details['quantity']) {
                    session()->flash('error-venda', 'Quantidade insuficiente no estoque para o produto: ' . $product->nome);
                    $this->hidePurchaseModal(); 
                    DB::rollBack();
                    return;
                }
            }
    
            $venda = VendaModel::create([
                'user_id' => auth()->id(), 
                'quantidade_total' => array_sum(array_column($cart, 'quantity')),
                'valor_total' => array_sum(array_map(function($details) {
                    return $details['quantity'] * $details['price'];
                }, $cart)),
            ]);
    
            foreach ($cart as $productId => $details) {
                $product = ProdutosModel::find($productId);
    
                $product->quantidade -= $details['quantity'];
                $product->save();
    
                $venda->produtos()->attach($productId, [
                    'quantidade' => $details['quantity'],
                    'valor_unitario' => $details['price'],
                ]);
            }
    
            DB::commit();
    
            $this->reset('cart');
            session()->forget('cart');
            $this->cart = [];
            
            $this->resetCardCount();

            $this->hidePurchaseModal(); 
            session()->flash('sucess-venda', 'Compra finalizada com sucesso.');
            $this->emit('cartUpdated');
            
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error-venda', 'Ocorreu um erro ao processar a compra. Por favor, tente novamente.');
        }
    }
    public function clearFilters()
    {
        $this->selectedCategory = null;
    }

    public function resetCardCount(){
        $this->cartCount = 0;
    }

    public function activateFilter($categoryId)
    {
        $this->selectedCategory = $categoryId;
        $this->resetPage();
    }
    protected function updateCartCount()
    {
        $this->cartCount = array_sum(array_column($this->cart, 'quantity'));
    }

    public function mount()
    {
        $this->perPage;
        $this->cart = session()->get('cart', []);
        $this->updateCartCount(); 

    }
    public function toggleCategories()
    {
        $this->showCategories = true;
        $this->loadCategories();
    }

    public function updatedSearchCategory()
    {
        $this->resetPage(); 
        $this->loadCategories();
    }

    public function updatedSearchProduct()
    {
        $this->resetPage(); 
    }

    public function loadMore()
    {
        $this->perPage += 8; 
    }

    protected function loadCategories()
    {
        $query = CategoriaModel::query();

        if ($this->searchCategory) {
            $query->where('nome', 'like', '%' . $this->searchCategory . '%');
        }

        $this->categories = $query->get();
    }

    public function render()
    {
        $query = ProdutosModel::query();
    
        if ($this->selectedCategory) {
            $query->where('categoria_id', $this->selectedCategory);
        }
    
        $products = $query
            ->where('nome', 'like', '%' . $this->searchProduct . '%')
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);
    
        $this->loadCategories();
    

        return view('livewire.home', [
            'categories' => $this->categories,
            'products' => $products,
            'cartCount' => $this->cartCount, 
            'showConfirmPurchaseModal' => $this->showConfirmPurchaseModal,
            'hasMorePages' => $products->hasMorePages(),  
        ]);
    }
    public function showPurchaseModal()
    {
        $this->showConfirmPurchaseModal = true;
    }

    public function hidePurchaseModal()
    {
        $this->showConfirmPurchaseModal = false;
    }
    public function removeFinishItem($productId)
    {   
        
        if (isset($this->cart[$productId])) {
            unset($this->cart[$productId]);
            session()->put('cart', $this->cart);
            $this->updateCartCount(); 
        }
    }
}
