<div class="container-fluid">
    <nav class="navbar-expand-lg navbar-light bg-white shadow-sm d-flex align-items-center navbar-custom">
        <a class="navbar-brand" href="#">
            <img src="{{ asset('images/Logo.png') }}" alt="Logo" class="d-inline-block align-middle" style="height: 40px;">
            <span class="brand-text ml-3">
                <span class="brand-vendas" style="font-size: 1.5rem; font-weight: bold; color: #333;">Vendas</span>
                <span class="brand-app" style="font-size: 1.5rem; font-weight: normal; color: #007bff;">App</span>
            </span>
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse d-flex justify-content-end" id="navbarNav">
            <ul class="navbar-nav d-flex align-items-center">
                <li class="nav-item dropdown d-flex align-items-center ml-3">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-shopping-cart fa-lg"></i>
                        <span class="cart-badge">{{ $cartCount }}</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right p-4 custom-dropdown-menu" id="dropdownMenu" aria-labelledby="navbarDropdown">
                        @if(count($cart) > 0)
                        <div class="dropdown-header">
                            <h6 class="font-weight-bold">Itens no Carrinho</h6>
                        </div>
                        <div class="table-responsive">
                            @if (session()->has('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            @endif
                            
                            @if (session()->has('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            @endif
                            <table class="table table-sm mb-0">
                                <tbody>
                                    <label colspan="4" class="text-end">Total</label>
                                    <span>R$ {{ number_format(array_sum(array_map(function($details) {
                                        if ($details['quantity'] > 0) {
                                            return $details['quantity'] * $details['price'];
                                        }
                                        return 0;
                                    }, $cart)), 2, ',', '.') }}</span>
                                        
                                        @foreach($cart as $productId => $details)
                                        
                                        <tr>
                                            <td class="align-middle p-2">
                                                <img src="{{ asset('storage/' . $details['image_url']) }}" alt="{{ $details['name'] }}" class="img-fluid rounded" style="height: 50px; width: 50px; object-fit: cover;">
                                            </td>
                                            <td class="align-middle p-2">
                                                <div class="d-flex flex-row align-items-center">
                                                    <span class="font-weight-bold m-1">{{ $details['name'] }}</span>
                                                    <small class="text-muted">R$ {{ number_format($details['price'], 2, ',', '.') }}</small>
                                                </div>
                                            </td>
                                            <td class="align-middle p-2 m-1">
                                                <label for="quantity-{{ $productId }}" class="mb-0 mr-2">Qnt.</label>
                                                <input type="number" id="quantity-{{ $productId }}" class="form-control form-control-sm" wire:model.debounce.1000ms="cart.{{ $productId }}.quantity" min="1" style="width: 80px;" 
                                                wire:change="updateQuantity('{{ $productId }}', $event.target.value)">
                                            </td>
                                            <td class="align-middle p-2">
                                                <button type="button" class="btn btn-danger btn-sm" data-action="stop" wire:click="removeFromCart('{{ $productId }}')">
                                                    <i class="fas fa-times"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            @endforeach
                                    </tbody>
                                </table>                                                               
                            </div>
                            <div class="dropdown-divider"></div>
                            <button class="dropdown-item text-center" wire:loading.attr="disabled" wire:click="showPurchaseModal">Finalizar Compra</button>
                        @else
                        <a class="dropdown-item text-center" href="#">Carrinho vazio</a>
                        @endif
                    </div>
                </li>
                <li class="nav-item dropdown d-flex align-items-center ml-3">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="profileDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-user fa-lg"></i>
                        <span class="ml-2 h5 mb-0">Olá, {{ auth()->user()->name }}</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="profileDropdown">
                        <a class="dropdown-item" href="{{route('user.home')}}">
                            <i class="fas fa-home mr-2"></i> Home
                        <a class="dropdown-item" href="{{ route('user.historico') }}">
                            <i class="fas fa-box mr-2"></i> Minhas Compras
                        </a>
                        <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt mr-2"></i> Sair
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </li>
            </ul>
        </div>
    </nav>

    @if(session()->has('sucess-venda'))
    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            {{ session('sucess-venda') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session()->has('error-venda'))
        <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
            {{ session('error-venda') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    
    <br>
    <h1 class="section-title text-center">Minhas Compras</h1>

    @if($showConfirmPurchaseModal)
        <div class="modal-overlay"></div>
        <div class="modal fade show" tabindex="-1" role="dialog" style="display: block;" aria-modal="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmPurchaseModalLabel">Resumo da Compra</h5>
                        <button type="button" class="btn btn-secondary" wire:click="hidePurchaseModal" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body modal-body-scroll">
                        @if (!empty($cart))
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Produto</th>
                                        <th>Quantidade</th>
                                        <th>Preço Unitário</th>
                                        <th>Total</th>
                                        <th>Ação</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($cart as $productId => $details)
                                        <tr>
                                            <td>{{ $details['name'] }}</td>
                                            <td>{{ $details['quantity'] }}</td>
                                            <td>R$ {{ number_format($details['price'], 2, ',', '.') }}</td>
                                            <td>R$ {{ number_format($details['quantity'] * $details['price'], 2, ',', '.') }}</td>
                                            <td>
                                                <button type="button" class="btn btn-danger btn-sm" data-action="stop" wire:click="removeFinishItem('{{ $productId }}')">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="total-amount">
                                        @if($details['quantity'] > 0)
                                            R$ {{ number_format(array_sum(array_map(function($item) {
                                                return $item['quantity'] * $item['price'];
                                            }, $cart)), 2, ',', '.') }}
                                        @else
                                            R$ 0,00
                                        @endif
                                    </tr>  
                                </tfoot>
                            </table>
                        @else
                            <p class="text-center">O carrinho está vazio.</p>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" wire:loading.attr="disabled" wire:click="finalizePurchase" class="btn btn-primary">Confirmar Compra</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <div class="container">
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Valor Total</th>
                        <th>Quantidade total</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vendas as $venda)
                        <tr>
                            <td>{{ number_format($venda->valor_total, 2, ',', '.') }} R$</td> 
                            <td>{{ $venda->quantidade_total }}</td>
                            <td class="text-center actions-column">
                                <button 
                                    wire:click="view({{ $venda->id }})" 
                                    class="btn btn-info btn-sm"
                                    wire:loading.attr="disabled"
                                >
                                    <i class="fas fa-eye"></i> Visualizar
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



    <button onclick="scrollToTop()" class="fixar-botao btn btn-dark rounded-circle shadow btn-scroll-top">
        <i class="fas fa-chevron-up"></i>
    </button>

    <style>
        .actions-column{
            width: 200px;
        }
        h1 {
            font-size: 2rem;
            font-weight: bold;
            color: var(--color-primary-dark);
            margin-bottom: 20px;
        }

        :root {
            --color-primary-dark: #171133;
            --color-primary: #581e44; 
            --color-accent: #c5485a; 
            --color-light: #d4be99; 
            --color-background: #e0ffcc; 
        }
    
        .modal-body-scroll {
            max-height: 650px;
            overflow-y: auto;
        }
    
        .cart-badge {
            position: absolute;
            top: -12px;
            right: -5px;
            background-color: var(--color-accent);
            color: #fff;
            font-size: 0.75rem;
            font-weight: bold;
            padding: 2px 6px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 20px;
            min-height: 20px;
        }

    
        .bg-color {
            background-color: var(--color-light);
        }
    
        .fixar-botao {
            position: fixed;
            bottom: 1rem;
            right: 1rem;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: var(--color-accent);
            color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            z-index: 1050;
        }

        .dropdown-menu {
            z-index: 1059 !important;
            position: absolute;
            top: 100%;
            margin-top: 1.5rem;
            min-width: 200px;
            max-height: 500px;
            overflow-y: auto;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
    
        .navbar-custom {
            background-color: var(--color-light);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 0.5rem 1rem;
            height: 100px;
        }
    
        .navbar-custom .navbar-nav .nav-link {
            color: var(--color-primary-dark);
            font-size: 1.1rem;
            font-weight: 500;
        }
    
        .navbar-custom .dropdown-item {
            color: var(--color-primary-dark);
        }
    
        .navbar-custom .dropdown-item:hover {
            background-color: var(--color-background);
        }
    
        .custom-dropdown-menu {
            width: 500px;
            padding: 15px;
            border-radius: 10px;
            background-color: var(--color-background);
            border: 1px solid #dee2e6;
        }
    
        .custom-dropdown-menu h6 {
            font-size: 1rem;
            font-weight: bold;
            color: var(--color-primary);
        }
    
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1040;
        }
    
        .total-row th {
            font-size: 1.25rem;
            font-weight: bold;
            color: var(--color-primary-dark);
        }
    
        .total-amount {
            font-size: 1.25rem;
            font-weight: bold;
            color: var(--color-primary);
        }
    </style>

    <script>
        function adjustDropdownPosition() {
            const dropdownMenu = document.querySelector('#dropdownMenu');
            const rect = dropdownMenu.getBoundingClientRect();

            if (rect.bottom > window.innerHeight) {
                dropdownMenu.style.top = 'auto';
                dropdownMenu.style.bottom = '100%';
            }
        }

        function toggleDropdown() {
            const dropdownButton = document.querySelector('#navbarDropdown');
            const dropdownMenu = document.querySelector('#dropdownMenu');
            
            if (dropdownButton && dropdownMenu) {
                const isOpen = dropdownMenu.classList.contains('show');
                
                if (isOpen) {
                    dropdownMenu.classList.remove('show');
                } else {
                    dropdownMenu.classList.add('show');
                    adjustDropdownPosition(); 
                }
            }
        }

        function closeDropdown(event) {
            const dropdownButton = document.querySelector('#navbarDropdown');
            const dropdownMenu = document.querySelector('#dropdownMenu');
            
            if (dropdownButton && dropdownMenu && !dropdownButton.contains(event.target) && !dropdownMenu.contains(event.target)) {
                dropdownMenu.classList.remove('show');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            Livewire.on('cartUpdated', function() {
                const dropdownMenu = document.querySelector('#dropdownMenu');
                const isOpen = dropdownMenu && dropdownMenu.classList.contains('show');
                
                if (!isOpen) {
                    toggleDropdown();
                }
            });

            const dropdownButton = document.querySelector('#navbarDropdown');
            if (dropdownButton) {
                dropdownButton.addEventListener('click', toggleDropdown);
            }

            document.addEventListener('click', closeDropdown);
        });
    </script>


</div>
