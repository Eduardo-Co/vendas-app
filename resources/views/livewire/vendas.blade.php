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
                    <form wire:submit.prevent="delete({{ $vendaToDelete }})">
                        <div class="modal-body">
                            <p>Are you sure you want to delete this trade?</p>
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
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $isCreating ? 'Create Venda' : 'Edit Venda' }}</h5>
                        <button type="button" class="close" wire:click="resetInputFields" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form wire:submit.prevent="save" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="container">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <div class="form-group">
                                            <label for="search_produto">Search Product</label>
                                            <input 
                                                type="text" 
                                                id="search_produto" 
                                                wire:model.debounce.300ms="searchProduto" 
                                                class="form-control"
                                                placeholder="Search for a product"
                                            />
                                        </div>
                                        <div class="form-group">
                                            <label for="produto_id">Produto</label>
                                            <select id="produto_id" wire:model="selectedProdutoId" wire:change="addProduto" class="form-control">
                                                <option value="">Select Produto</option>
                                                @foreach($produtos as $produto)
                                                    <option value="{{ $produto->id }}">{{ $produto->nome }}</option>
                                                @endforeach
                                            </select>
                                            @error('produto_id') <div class="text-danger">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="search_user">Search User</label>
                                            <input 
                                                type="text" 
                                                id="search_user" 
                                                wire:model.debounce.300ms="searchUser" 
                                                class="form-control"
                                                placeholder="Search for a user"
                                            />
                                        </div>
                                        <div class="form-group">
                                            <label for="user_id">User</label>
                                            <select id="user_id" wire:model="user_id" class="form-control">
                                                <option value="">Select User</option>
                                            
                                                @foreach($users as $user)
                                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('user_id') <div class="text-danger">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-8 mb-3">
                                        <h5>Selected Products</h5>
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Product</th>
                                                    <th>Quantity</th>
                                                    <th>Value</th>
                                                    <th>Action</th>
                                                    <th class="w-25"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($selectedProductPerPage as $produtoId => $details)
                                                    <tr>
                                                        <td>{{ $details['produto']['nome'] }}</td>
                                                        <td>
                                                            <input 
                                                                type="number" 
                                                                min="1" 
                                                                wire:model.debounce.300ms="selectedProducts.{{ $produtoId }}.quantidade" 
                                                                class="form-control"
                                                                value="{{ $details['quantidade'] ?? 1 }}"
                                                                wire:change="updateProdutoQuantity('{{ $produtoId }}', (int)$event.target.value !== '' ? (int)$event.target.value : 1)"
                                                            />
                                                        <td>{{ number_format($details['produto']['valor'], 2, ',', '.') }} R$</td>
                                                        <td>
                                                            <button 
                                                                type="button" 
                                                                wire:click="removeProduto('{{ $produtoId }}')"
                                                                class="btn btn-link text-danger"
                                                                title="Remove"
                                                            >
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </td>
                                                        <td>
                                                            @if($details['produto']['quantidade'] == 0)
                                                                <span class="badge badge-danger ml-2">Esgotado</span>
                                                            @else
                                                                {{$details['produto']['quantidade']}}
                                                            @endif
                                                        </td>
                                                        
                                                        </td>
                                                    </tr>
                                                    
                                                @endforeach 
                                            </tbody>
                                        </table>
                                        <div class="d-flex justify-content-between">
                                            @if ($currentPage > 1)
                                                <button 
                                                    type="button" 
                                                    wire:click="previousPage" 
                                                    class="btn btn-primary"
                                                >
                                                    &laquo; Previous
                                                </button>
                                            @endif
                                    
                                            @if (($currentPage * $itemsPerPage) < $totalProducts)
                                                <button 
                                                    type="button" 
                                                    wire:click="nextPage" 
                                                    class="btn btn-primary"
                                                >
                                                    Next &raquo;
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer d-flex justify-content-between align-items-center bg-light border-top p-3">
                            <div class="total-value">
                                <h5 class="mb-1">Total Value:</h5>
                                <p class="mb-0 text-success fw-bold">{{ number_format($this->getTotalValue(), 2, ',', '.') }} R$</p>
                            </div>
                            @error("selectedProducts") 
                                <span class="text-danger">{{ $message }}</span>
                            @enderror 
                            <button 
                                type="submit" 
                                class="btn btn-success"
                                wire:loading.attr="disabled"
                            >
                                {{ $isCreating ? 'Create Venda' : 'Update Venda' }}
                            </button>
                        </div>                        
                    </form>
                </div>
            </div>
        </div>
    @endif

    @if($viewingVenda)
        <div class="modal fade show" tabindex="-1" role="dialog" style="display: block;" aria-modal="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">View Venda</h5>
                        <button type="button" class="close" wire:click="closeView" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <div class="form-group">
                                        <label for="view_user_id">User</label>
                                        <input type="text" id="view_user_id" value="{{ $viewingVenda->user->name ?? 'N/A' }}" class="form-control" readonly />
                                    </div>
                                    <div class="form-group">
                                        <label for="view_total_value">Total Value</label>
                                        <input type="text" id="view_total_value" value="{{ number_format($viewingVenda->valor_total, 2, ',', '.') ?? 'N/A' }}" class="form-control" readonly />
                                    </div>
                                    <div class="form-group">
                                        <label for="view_total_quantity">Total Quantity</label>
                                        <input type="text" id="view_total_quantity" value="{{ $viewingVenda->quantidade_total ?? 'N/A' }}" class="form-control" readonly />
                                    </div>
                                </div>
                                <div class="col-md-8 mb-3">
                                    <h5>Selected Products</h5>
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Quantity</th>
                                                <th>Value</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($selectedProductPerPage as $produto)
                                                <tr>
                                                    <td>{{ $produto['produto']['nome'] }}</td>
                                                    <td>{{ $produto['quantidade'] }}</td>
                                                    <td>{{ number_format($produto['produto']['valor'], 2, ',', '.') }} R$</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <div class="d-flex justify-content-between">
                                        @if ($currentPage > 1)
                                            <button 
                                                type="button" 
                                                wire:click="previousPage" 
                                                class="btn btn-primary"
                                            >
                                                &laquo; Previous
                                            </button>
                                        @endif
                                    
                                        @if (($currentPage * $itemsPerPage) < $totalProducts)
                                            <button 
                                                type="button" 
                                                wire:click="nextPage" 
                                                class="btn btn-primary"
                                            >
                                                Next &raquo;
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-between align-items-center bg-light border-top p-3">
                        <button type="button" class="btn btn-secondary" wire:click="closeView">
                            Close
                        </button>
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
                Add Venda
            </button>
        </div>
    @endunless

    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Nome do Usuário</th>
                    <th>Valor Total</th>
                    <th>Quantidade total</th>
                    <th class="w-25">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($vendas as $venda)
                    <tr>
                        <td>{{ $venda->user->name }}</td>
                        <td>{{ number_format($venda->valor_total, 2, ',', '.') }} R$</td> 
                        <td>{{ $venda->quantidade_total }}</td>
                        <td class="text-center">
                            <button 
                                wire:click="edit({{ $venda->id }})" 
                                class="btn btn-warning btn-sm"
                                wire:loading.attr="disabled"
                            >
                                Edit
                            </button>
                            <button 
                                wire:click="confirmDelete({{ $venda->id }})" 
                                class="btn btn-danger btn-sm"
                                wire:loading.attr="disabled"
                            >
                                Delete
                            </button>
                            <button 
                                wire:click="view({{ $venda->id }})" 
                                class="btn btn-info btn-sm"
                                wire:loading.attr="disabled"
                            >
                                View
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">Nenhuma venda encontrada</td>
                    </tr>
                @endforelse
            </tbody>            
        </table>
    </div>

    <div class="flex flex-row mt-2">
        {{ $vendas->links() }}
    </div>
    
</div>
