<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Categoria as CategoriaModel; 
use Illuminate\Support\Facades\Storage;

class Categorias extends Component
{
    use WithFileUploads;
    use WithPagination;


    public $nome;
    public $descricao;
    public $imagem_url;
    public $selectedCategoriaId;
    public $searchTerm = '';
    public $isEditing = false;
    public $isCreating = false;
    public $categoriaToDelete;
    public $showDeleteModal = false;    
    public $viewingCategoria;

    protected $rules = [
        'nome' => 'required|string|max:255',
        'descricao' => 'nullable|string',
        'imagem_url' => 'nullable|image|max:5024',
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
    
    public function view($categoriaId)
    {
        $this->viewingCategoria = CategoriaModel::findOrFail($categoriaId);
    }

    public function closeView()
    {
        $this->viewingCategoria = null; 
    }
    
    public function render()
    {
        $categorias = CategoriaModel::where('nome', 'like', '%' . $this->searchTerm . '%')
            ->paginate(6);

        $start = max($categorias->currentPage() - 2, 1);
        $end = min($categorias->currentPage() + 2, $categorias->lastPage());

        return view('livewire.categorias', [
            'categorias' => $categorias,
            'start' => $start,
            'end' => $end,
        ]);
    }
    
    public function edit($categoriaId)
    {
        $categoria = CategoriaModel::findOrFail($categoriaId);
        $this->selectedCategoriaId = $categoria->id;
        $this->nome = $categoria->nome;
        $this->descricao = $categoria->descricao;
        $this->imagem_url = $categoria->imagem_url;
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
            $imageUrl = $this->imagem_url->store('categoria_images', 'public');
        }

        if ($this->isEditing) {
            $categoria = CategoriaModel::find($this->selectedCategoriaId);

            if ($categoria->imagem_url && $imageUrl && $categoria->imagem_url !== $imageUrl) {
                Storage::disk('public')->delete($categoria->imagem_url);
            }

            $categoria->update([
                'nome' => $this->nome,
                'descricao' => $this->descricao,
                'imagem_url' => $imageUrl ?? $categoria->imagem_url,
            ]);

            session()->flash('message', 'Categoria atualizada com sucesso.');

        } elseif ($this->isCreating) {
            CategoriaModel::create([
                'nome' => $this->nome,
                'descricao' => $this->descricao,
                'imagem_url' => $imageUrl,
            ]);

            session()->flash('message', 'Categoria criada com sucesso.');

        }

        $this->resetInputFields();
        $this->resetPage(); 

    }
    
    public function resetInputFields()
    {
        $this->nome = '';
        $this->descricao = '';
        $this->imagem_url = null;
        $this->selectedCategoriaId = null;
        $this->searchTerm = '';
        $this->isEditing = false;
        $this->isCreating = false;
    }

    public function delete()
    {
        $categoria = CategoriaModel::findOrFail($this->categoriaToDelete);

    
        if ($categoria->imagem_url) {
            Storage::disk('public')->delete($categoria->imagem_url);
        }

        $categoria->delete();
        session()->flash('message-deleted', 'Categoria deletada com sucesso.');
        

        $this->categoriaToDelete = null;
        $this->showDeleteModal = false;
        $this->resetPage();
    }

    public function confirmDelete($categoriaId)
    {
        $this->categoriaToDelete = $categoriaId;
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->categoriaToDelete = null;
    }
}
