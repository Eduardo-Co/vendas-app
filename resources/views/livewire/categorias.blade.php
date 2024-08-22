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
                    <form wire:submit.prevent="delete({{ $categoriaToDelete }})">
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
                        <h5 class="modal-title">{{ $isCreating ? 'Create Categoria' : 'Edit Categoria' }}</h5>
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
                                        placeholder="Enter category nome"
                                    />
                                    @error('nome') <div class="text-danger">{{ $message }}</div> @enderror
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="descricao">Descrição</label>
                                    <textarea 
                                        id="descricao" 
                                        wire:model="descricao" 
                                        class="form-control"
                                        rows="3"
                                        placeholder="Enter category descricao"
                                    ></textarea>
                                    @error('descricao') <div class="text-danger">{{ $message }}</div> @enderror
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
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button 
                                type="submit" 
                                class="btn btn-success"
                                wire:loading.attr="disabled"
                            >
                                {{ $isCreating ? 'Create Categoria' : 'Update Categoria' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif


    @if($viewingCategoria)
        <div class="modal fade show" tabindex="-1" role="dialog" style="display: block;" aria-modal="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">View Categoria</h5>
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
                                    value="{{ $viewingCategoria->nome }}" 
                                    class="form-control" 
                                    disabled
                                />
                            </div>
                            <div class="form-group col-md-6">
                                <label>Descrição</label>
                                <input 
                                    type="text" 
                                    value="{{ $viewingCategoria->descricao }}" 
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
                                    value="{{ $viewingCategoria->imagem_url }}"
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
                Add categoria
            </button>
        </div>
    @endunless

    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>             
                    <th>Nome</th>
                    <th>Descrição</th>
                    <th>Foto</th>
                    <th class="w-25">Actions</th> 
                </tr>
            </thead>
            <tbody>
                @forelse($categorias as $categoria)
                    <tr>
                        <td>{{ $categoria->nome }}</td>
                        <td>{{ $categoria->descricao }}</td>
                        <td>
                            @if($categoria->imagem_url)
                                <img src="{{ asset('storage/' . $categoria->imagem_url) }}" alt="{{ $categoria->nome }}" class="img-thumbnail" style="width: 100px; height: 100px;">
                            @else
                                <span class="text-muted">No Image</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <button 
                                wire:click="edit({{ $categoria->id }})" 
                                class="btn btn-warning btn-sm"
                                wire:loading.attr="disabled"
                            >
                                Edit
                            </button>
                            <button 
                                wire:click="confirmDelete({{ $categoria->id }})" 
                                class="btn btn-danger btn-sm"
                                wire:loading.attr="disabled"
                            >
                                Delete
                            </button>
                            <button 
                                wire:click="view({{ $categoria->id }})" 
                                class="btn btn-info btn-sm"
                                wire:loading.attr="disabled"
                            >
                                View
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">Não foram encontradas categorias</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>    

    <div class="flex flex-row mt-2">
        {{ $categorias->links() }}
    </div>
    
</div>
