<?php

namespace App\Http\Livewire;

use App\Models\Categoria;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Produto as ProdutoModel; 
use App\Models\Categoria as CategoriaModel;
use Illuminate\Support\Facades\Storage;

class Produtos extends Component
{
    use WithFileUploads;
    use WithPagination;


    public $nome;
    public $valor;
    public $imagem_url;
    public $quantidade;
    public $categoria_id;
    public $selectedProdutoId;
    public $searchTerm = '';
    public $searchCategoria = '';
    public $isEditing = false;
    public $isCreating = false;
    public $produtoToDelete;
    public $showDeleteModal = false;    
    public $viewingProduto;

    protected $rules = [
        'nome' => 'required|string|max:255',
        'valor' => 'required|numeric|min:0|max:999999.99', 
        'imagem_url' => 'nullable|image|max:5024',
        'quantidade' => 'required|integer|min:0',
        'categoria_id' => 'required|exists:categorias,id',
    ];
    protected $paginationTheme = 'bootstrap';

    public function updatedImagemUrl()
    {
        $this->validateOnly('imagem_url');
    }

    public function updatingSearchTerm()
    {
        $this->resetPage();
    }
    
    public function view($produtoId)
    {
        $this->viewingProduto = ProdutoModel::findOrFail($produtoId);
    }

    public function closeView()
    {
        $this->viewingProduto = null; 
    }
    
    public function render()
    {
        $produtos = ProdutoModel::where('nome', 'like', '%' . $this->searchTerm . '%')
            ->paginate(6);

        $categorias = CategoriaModel::where('nome', 'like', '%' . $this->searchCategoria . '%')
            ->get();

        $start = max($produtos->currentPage() - 2, 1);
        $end = min($produtos->currentPage() + 2, $produtos->lastPage());

        return view('livewire.produtos', [
            'produtos' => $produtos,
            'categorias' => $categorias,
            'start' => $start,
            'end' => $end,
        ]);
    }
    
    public function edit($produtoId)
    {
        $produto = ProdutoModel::findOrFail($produtoId);
        $this->selectedProdutoId = $produto->id;
        $this->nome = $produto->nome;
        $this->valor = $produto->valor;
        $this->quantidade = $produto->quantidade;
        $this->categoria_id = $produto->categoria_id;
        $this->imagem_url = $produto->imagem_url;
        $this->isEditing = true;
        $this->isCreating = false;
    }
    

    public function create()
    {
        $this->resetInputFields();
        $this->isEditing = false;
        $this->isCreating = true;
    }

    public function save()
    {
        $this->validate();
    
        $imageUrl = null;
    
        if ($this->imagem_url) {
            $imageUrl = $this->imagem_url->store('produto_images', 'public');
        }
    
        if ($this->isEditing) {
            $produto = ProdutoModel::find($this->selectedProdutoId);
    
            if ($produto->imagem_url && $imageUrl && $produto->imagem_url !== $imageUrl) {
                Storage::disk('public')->delete($produto->imagem_url);
            }
    
            $produto->update([
                'nome' => $this->nome,
                'valor' => $this->valor,
                'quantidade' => $this->quantidade,
                'categoria_id' => $this->categoria_id,
                'imagem_url' => $imageUrl ?? $produto->imagem_url,
            ]);
    
            session()->flash('message', 'Produto atualizado com sucesso.');
    
        } elseif ($this->isCreating) {
            ProdutoModel::create([
                'nome' => $this->nome,
                'valor' => $this->valor,
                'quantidade' => $this->quantidade,
                'categoria_id' => $this->categoria_id,
                'imagem_url' => $imageUrl,
            ]);
    
            session()->flash('message', 'Produto criado com sucesso.');
        }
    
        $this->resetInputFields();
        $this->resetPage(); 
    }    
    
    public function resetInputFields()
    {
        $this->nome = '';
        $this->valor = '';
        $this->quantidade = '';
        $this->categoria_id = null;
        $this->imagem_url = null;
        $this->selectedProdutoId = null;
        $this->searchTerm = '';
        $this->isEditing = false;
        $this->isCreating = false;
    }
    
    public function delete()
    {
        $produto = ProdutoModel::findOrFail($this->produtoToDelete);

    
        if ($produto->imagem_url) {
            Storage::disk('public')->delete($produto->imagem_url);
        }

        $produto->delete();
        session()->flash('message-deleted', 'Produto deletado com sucesso.');
        

        $this->produtoToDelete = null;
        $this->showDeleteModal = false;
        $this->resetPage();
    }

    public function confirmDelete($produtoId)
    {
        $this->produtoToDelete = $produtoId;
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->produtoToDelete = null;
    }
}
