<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Venda as VendaModel; 
use App\Models\User as UserModel;
use App\Models\Produto as ProdutoModel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB; 

class Vendas extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $selectedProdutoId;
    public $user_id;
    public $selectedVendaId;
    public $searchTerm = '';
    public $searchProduto = '';
    public $searchUser = '';
    public $isEditing = false;
    public $isCreating = false;
    public $vendaToDelete;
    public $showDeleteModal = false;    
    public $viewingVenda;
    public $selectedProducts = [];
    public $itemsPerPage = 4; 
    public $currentPage = 1;

    protected $rules = [
        'user_id' => 'required|exists:users,id', 
        'selectedProducts.*.produto.id' => 'required|exists:produtos,id', 
        'selectedProducts.*.quantidade' => 'required|integer|min:1', 
        'selectedProducts.*.produto.valor' => 'required|numeric|min:0', 
    ];
    
    protected $paginationTheme = 'bootstrap';

    public function getCurrentPageProducts()
    {
        $start = ($this->currentPage - 1) * $this->itemsPerPage;

    return collect($this->selectedProducts)->slice($start, $this->itemsPerPage);
    }

    public function nextPage()
    {
        if (($this->currentPage * $this->itemsPerPage) < count($this->selectedProducts)) {
            $this->currentPage++;
        }
    }

    public function previousPage()
    {
        if ($this->currentPage > 1) {
            $this->currentPage--;
        }
    }
    public function addProduto()
    {
        if ($this->selectedProdutoId) {
            $produto = ProdutoModel::find($this->selectedProdutoId);
    
            if ($produto) {
                if (!array_key_exists($this->selectedProdutoId, $this->selectedProducts)) {
                    $this->selectedProducts[$this->selectedProdutoId] = [
                        'produto' => $produto,
                        'quantidade' => 1, 
                    ];
                }
            }
        }
    }
    public function updateProdutoQuantity($produtoId, $quantidade)
    {
        if (array_key_exists($produtoId, $this->selectedProducts)) {
            $this->selectedProducts[$produtoId]['quantidade'] = $quantidade;
        }
    }

    public function removeProduto($produtoId)
    {
        if (array_key_exists($produtoId, $this->selectedProducts)) {
            unset($this->selectedProducts[$produtoId]);
        }
    }
    public function updatingSearchTerm()
    {
        $this->resetPage();
    }
    
    public function view($vendaId)
    {
        $this->viewingVenda = VendaModel::findOrFail($vendaId);


        $this->selectedProducts = $this->viewingVenda->produtos->mapWithKeys(function($produto) {
            return [
                $produto->id => [
                    'produto' => $produto,
                    'quantidade' => $produto->pivot->quantidade,

                ],
            ];
        })->toArray();
    }

    public function closeView() 
    {
        $this->viewingVenda = null; 
    }
    
    public function render()
    {
        $userIds = UserModel::where('name', 'like', '%' . $this->searchTerm . '%')->pluck('id');

        $vendas = VendaModel::whereHas('produtos', function($query) use ($userIds) {
            $query->whereIn('user_id', $userIds);
        })->paginate(6);

        $produtos = ProdutoModel::where('nome', 'like', '%' . $this->searchProduto . '%')->get();

        $users = UserModel::where('name', 'like', '%' . $this->searchUser . '%')->get();


        $start = max($vendas->currentPage() - 2, 1);
        $end = min($vendas->currentPage() + 2, $vendas->lastPage());

        return view('livewire.vendas', [
            'vendas' => $vendas,
            'users' => $users,
            'produtos' => $produtos,
            'start' => $start,
            'end' => $end,
            'selectedProductPerPage' => $this->getCurrentPageProducts(),
            'totalProducts' => count($this->selectedProducts),
        ]);
    }
    
    public function edit($vendaId)
    {
        $venda = VendaModel::findOrFail($vendaId);
        $this->selectedVendaId = $venda->id;
        $this->user_id = $venda->user_id;   
        $this->isEditing = true;
        $this->isCreating = false;
    
        $this->selectedProducts = $venda->produtos->mapWithKeys(function($produto) {
            return [
                $produto->id => [
                    'produto' => $produto,
                    'quantidade' => $produto->pivot->quantidade,

                ],
            ];
        })->toArray();
    
        return view('livewire.vendas', [
            'venda' => $venda,
            'user' => $venda->user, 
        ]);
    }
    
    

    public function create()
    {
        $this->resetInputFields();
        $this->isEditing = false;
        $this->isCreating = true;
    }

    
    public function save()
    {   
        if (count($this->selectedProducts) < 1) {
            $this->addError('selectedProducts', 'Você deve selecionar ao menos um produto.');
            return;
        }
    
        $this->validate();
    
        DB::beginTransaction();
    
        try {
            foreach ($this->selectedProducts as $produtoId => $details) {
                $product = ProdutoModel::find($produtoId);
    
                if ($product->quantidade < $details['quantidade']) {
                    $this->addError('selectedProducts', 'Quantidade insuficiente no estoque para o produto: ' . $product->nome);
                    DB::rollBack();
                    return;
                }
            }
    
            if ($this->isEditing) {
                $venda = VendaModel::findOrFail($this->selectedVendaId);
    
                $venda->update([
                    'user_id' => $this->user_id,
                    'quantidade_total' => array_sum(array_column($this->selectedProducts, 'quantidade')),
                    'valor_total' => array_sum(array_map(function($details) {
                        return $details['quantidade'] * $details['produto']['valor'];
                    }, $this->selectedProducts)),
                ]);
    
                $syncData = [];
                foreach ($this->selectedProducts as $produtoId => $details) {
                    $syncData[$produtoId] = [
                        'quantidade' => $details['quantidade'],
                        'valor_unitario' => $details['produto']['valor'],
                    ];
                    
                    $product = ProdutoModel::find($produtoId);
                    $product->quantidade -= $details['quantidade'];
                    $product->save();
                }
    
                $venda->produtos()->sync($syncData);
    
            } elseif ($this->isCreating) {
                $venda = VendaModel::create([
                    'user_id' => $this->user_id,
                    'quantidade_total' => array_sum(array_column($this->selectedProducts, 'quantidade')),
                    'valor_total' => array_sum(array_map(function($details) {
                        return $details['quantidade'] * $details['produto']['valor'];
                    }, $this->selectedProducts)),
                ]);
    
                foreach ($this->selectedProducts as $produtoId => $details) {
                    $venda->produtos()->attach($produtoId, [
                        'quantidade' => $details['quantidade'],
                        'valor_unitario' => $details['produto']['valor'],
                    ]);
    
                    $product = ProdutoModel::find($produtoId);
                    $product->quantidade -= $details['quantidade'];
                    $product->save();
                }
            }
    
            DB::commit();
    
            $this->resetInputFields();
            $this->resetPage();
    
            session()->flash('message', $this->isEditing ? 'Venda atualizada com sucesso.' : 'Venda criada com sucesso.');
    
        } catch (\Exception $e) {
            DB::rollBack();
    
            session()->flash('message-deleted', 'Ocorreu um erro ao salvar a venda. Por favor, tente novamente.');
        }
    }
    
    

    
    public function getTotalValue()
    {
        $total = 0;
    
        foreach ($this->selectedProducts as $produtoId => $details) {
            $quantidade = (int) $details['quantidade'];
            $valor = (float) $details['produto']['valor'];
    
            $total += $quantidade * $valor;
        }
    
        return $total;
    }
    

   public function resetInputFields()
    {
        $this->selectedProdutoId = null;
        $this->user_id = null;
        $this->selectedVendaId = null;
        $this->searchTerm = '';
        $this->searchProduto = '';
        $this->searchUser = '';
        $this->isEditing = false;
        $this->isCreating = false;
        $this->selectedProducts = []; 
        $this->vendaToDelete = null;
        $this->showDeleteModal = false;
        $this->viewingVenda = null;
    }

    
    public function delete()
    {
        $venda = VendaModel::findOrFail($this->vendaToDelete);

    
        if ($venda->imagem_url) {
            Storage::disk('public')->delete($venda->imagem_url);
        }

        $venda->delete();
        session()->flash('message-deleted', 'Venda deletada com sucesso.');
        

        $this->vendaToDelete = null;
        $this->showDeleteModal = false;
        $this->resetPage();
    }

    public function confirmDelete($vendaId)
    {
        $this->vendaToDelete = $vendaId;
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->vendaToDelete = null;
    }
}
