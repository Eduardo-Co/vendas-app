<div class="container py-5">
    @if(session()->has('message') || session()->has('message-deleted'))
        <div id="toastrMsg" class="alert @if(session()->has('message')) alert-success @elseif(session()->has('message-deleted')) alert-danger @endif alert-dismissible fade show" role="alert">
            @if(session()->has('message'))
                <strong>{{ session('message') }}</strong>
            @endif
            
            @if(session()->has('message-deleted'))
                <strong>{{ session('message-deleted') }}</strong>
            @endif
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if($showDeleteModal)
        <div class="modal fade show" tabindex="-1" role="dialog" style="display: block;" aria-modal="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirm Deletion</h5>
                    </div>
                    <form wire:submit.prevent="delete({{ $produtoToDelete }})">
                        <div class="modal-body">
                            <p>Are you sure you want to delete this music?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="closeDeleteModal">Cancel</button>
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    @if($isEditing || $isCreating)
        <div class="modal fade show" tabindex="-1" role="dialog" style="display: block;" aria-modal="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $isCreating ? 'Create produto' : 'Edit produto' }}</h5>
                        <button type="button" class="close" wire:click="resetInputFields" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form wire:submit.prevent="save" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label for="nome">Nome</label>
                                    <input 
                                        type="text" 
                                        id="nome" 
                                        wire:model="nome" 
                                        class="form-control"
                                        placeholder="Enter product name"
                                    />
                                    @error('nome') <div class="text-danger">{{ $message }}</div> @enderror
                                </div>
                                <div class="form-group col-md-12 max-w-lg">
                                    <label for="imagem_url">Imagem</label>
                                    <input 
                                        type="file" 
                                        id="imagem_url" 
                                        wire:model="imagem_url" 
                                        class="form-control p-1"
                                    />
                                    @error('imagem_url') 
                                        <div class="text-danger">{{ $message }}</div> 
                                    @enderror
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="valor">Valor</label>
                                    <input 
                                        type="number" 
                                        id="valor" 
                                        wire:model="valor" 
                                        class="form-control"
                                        placeholder="Enter product value" 
                                        step="0.01" 
                                        min="1"
                                    />
                                    @error('valor') <div class="text-danger">{{ $message }}</div> @enderror
                                </div>
                                
                                <div class="form-group col-md-12">
                                    <label for="categoria_search">Buscar Categoria</label>
                                    <input 
                                        type="text" 
                                        id="categoria_search" 
                                        wire:model="searchCategoria" 
                                        class="form-control"
                                        placeholder="Search category"
                                    />
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="categoria_id">Categoria</label>
                                    <select 
                                        id="categoria_id" 
                                        wire:model="categoria_id" 
                                        class="form-control"
                                    >
                                        <option value="">Select category</option>
                                        @foreach($categorias as $categoria)
                                            <option value="{{ $categoria->id }}">{{ $categoria->nome }}</option>
                                        @endforeach
                                    </select>
                                    @error('categoria_id') <div class="text-danger">{{ $message }}</div> @enderror
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="quantidade">Quantidade</label>
                                    <input 
                                        type="number" 
                                        id="quantidade" 
                                        wire:model="quantidade" 
                                        class="form-control"
                                        placeholder="Enter product quantity"
                                    />
                                    @error('quantidade') <div class="text-danger">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button 
                                type="submit" 
                                class="btn btn-success"
                                wire:loading.attr="disabled"
                            >
                                {{ $isCreating ? 'Create produto' : 'Update produto' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif



    @if($viewingProduto)
        <div class="modal fade show" tabindex="-1" role="dialog" style="display: block;" aria-modal="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">View Produto</h5>
                        <button type="button" class="close" wire:click="closeView" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Nome</label>
                                <input 
                                    type="text" 
                                    value="{{ $viewingProduto->nome }}" 
                                    class="form-control" 
                                    disabled
                                />
                            </div>
                            <div class="form-group col-md-6">
                                <label>Valor</label>
                                <input 
                                    type="text" 
                                    value="{{ $viewingProduto->valor }}" 
                                    class="form-control" 
                                    disabled
                                />
                            </div>
                            <div class="form-group col-md-6">
                                <label>Categoria</label>
                                <input 
                                    type="text" 
                                    value="{{ $viewingProduto->categoria->nome }}" 
                                    class="form-control" 
                                    disabled
                                />
                            </div>
                            <div class="form-group col-md-6">
                                <label>Quantidade</label>
                                <input 
                                    type="text" 
                                    value="{{ $viewingProduto->quantidade }}" 
                                    class="form-control" 
                                    disabled
                                />
                            </div>
                            <div class="form-group">
                                <label for="imagem_url">Foto</label>
                                <input 
                                    disabled="disabled"
                                    type="file" 
                                    id="imagem_url" 
                                    value="{{ $viewingProduto->imagem_url }}"
                                    wire:model="imagem_url" 
                                    class="form-control p-1"
                                    />
                                @error('imagem_url') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @unless($isEditing || $isCreating)
        <div class="mb-4 d-flex align-items-center">
            <input 
                type="text" 
                placeholder="Search for name..." 
                class="form-control mr-2"
                wire:model.debounce.300ms="searchTerm"
            />
            <button 
                wire:click="create" 
                class="btn btn-success"
                style="width: 150px;"
                wire:loading.attr="disabled"
            >
                Add Produto
            </button>
        </div>
    @endunless

    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Valor</th>
                    <th>Quantidade</th>
                    <th>Categoria</th>
                    <th>Foto</th>
                    <th class="w-25">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($produtos as $produto)
                    <tr>
                        <td>{{ $produto->nome }}</td>
                        <td>{{ number_format($produto->valor, 2, ',', '.') }}</td> 
                        <td>{{ $produto->quantidade }}</td>
                        <td>{{ $produto->categoria->nome }}</td> 
                        <td>
                            @if($produto->imagem_url)
                                <img src="{{ asset('storage/' . $produto->imagem_url) }}" alt="{{ $produto->nome }}" class="img-thumbnail" style="width: 100px; height: 100px;">
                            @else
                                <span class="text-muted">No image</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <button 
                                wire:click="edit({{ $produto->id }})" 
                                class="btn btn-warning btn-sm"
                                wire:loading.attr="disabled"
                            >
                                Edit
                            </button>
                            <button 
                                wire:click="confirmDelete({{ $produto->id }})" 
                                class="btn btn-danger btn-sm"
                                wire:loading.attr="disabled"
                            >
                                Delete
                            </button>
                            <button 
                                wire:click="view({{ $produto->id }})" 
                                class="btn btn-info btn-sm"
                                wire:loading.attr="disabled"
                            >
                                View
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">Nenhum produto encontrado</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="flex flex-row mt-2">
        {{ $produtos->links() }}
    </div>
    
</div>
