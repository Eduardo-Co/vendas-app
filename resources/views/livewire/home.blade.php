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
                <li class="nav-item d-flex align-items-center ml-3">
                    <a class="nav-link d-flex align-items-center" href="#">
                        <i class="fas fa-user fa-lg"></i>
                        <span class="ml-2 h5 mb-0">Perfil</span>
                    </a>
                </li>
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
                                            return $details['quantity'] * $details['price'];
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
                                                    <input type="number" id="quantity-{{ $productId }}" class="form-control form-control-sm" wire:model.debounce.500ms="cart.{{ $productId }}.quantity" min="1" style="width: 80px;" 
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
            </ul>
        </div>
    </nav>

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
                                    <tr class="total-row">
                                        <th colspan="4" class="text-end">Total</th>
                                        <th class="total-amount">R$ {{ number_format(array_sum(array_map(function($details) {
                                            return $details['quantity'] * $details['price'];
                                        }, $cart)), 2, ',', '.') }}</th>
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



    <main class="mx-5 my-3">
        <div class="fixed inset-0 d-flex align-items-center justify-content-center bg-dark bg-opacity-50 z-50" wire:click.self="$set('showCategories', false)">
            <div class="bg-light p-4 rounded-lg w-100 position-relative shadow-lg">
                @if(empty($categories))
                    <div class="d-flex flex-column align-items-center text-center">
                        <span class="h1 mb-2">😞</span>
                        <p class="text-muted">Não há nenhuma categoria disponível.</p>
                    </div>
                @else
                    <form class="form-inline mb-3">
                        <input class="form-control form-control-lg w-50 mr-2" type="search" placeholder="Buscar categorias" aria-label="Search" wire:model="searchCategory">
                        <div class="d-flex align-items-center">
                            @if($selectedCategory)
                                <div class="ml-3">
                                    <button type="button" wire:click="clearFilters" class="btn btn-sm btn-outline-secondary d-flex align-items-center">
                                        <i class="fas fa-eraser mr-2"></i> Limpar Filtros
                                    </button>
                                </div>
                            @else
                                <h6 class="font-weight-bold text-primary ml-3">Clique na categoria para filtrar</h6>
                            @endif
                        </div>
                                                
                    </form>
                    <div id="categoryCarousel" class="carousel slide" data-ride="carousel">
                        <div class="carousel-inner">
                            @foreach ($categories->chunk(6) as $chunk)
                            <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                                <div class="row no-gutters">
                                    @foreach ($chunk as $category)
                                        <div class="col-md-2">
                                            <div class="card bg-light border position-relative shadow-sm mb-3 category-card">
                                                <img src="{{ asset('storage/' . $category->imagem_url) }}" alt="{{ $category->nome }}" class="card-img-top" style="height: 200px; object-fit: cover;">
                                                <div class="card-body bg-color p-3">
                                                    <h5 class="card-title">{{ $category->nome }}</h5>
                                                    <p class="card-text">{{ $category->descricao }}</p>
                                                </div>
                                                <div class="filter-overlay d-flex justify-content-center align-items-center">
                                                    <button class="btn btn-primary" wire:click="activateFilter({{ $category->id }})">Filtrar</button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            
                            @endforeach
                        </div>
                        <a class="carousel-control-prev" href="#categoryCarousel" role="button" data-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true">
                                <i class="fas fa-chevron-left"></i>
                            </span>
                            <span class="sr-only">Anterior</span>
                        </a>
                        <a class="carousel-control-next" href="#categoryCarousel" role="button" data-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true">
                                <i class="fas fa-chevron-right"></i>
                            </span>
                            <span class="sr-only">Próximo</span>
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <div class="my-4">
            <div class="card shadow-lg border-light">
                <div class="card-body p-4">
                    <form class="form-inline mb-3">
                        <input class="form-control form-control-lg w-50 mr-2" type="search" placeholder="Buscar produtos" aria-label="Search" wire:model="searchProduct">
                    </form>
    
                    <div class="row">
                        @foreach ($products as $product)
                            <div class="col-md-3 mb-4">
                                <div class="card bg-light border shadow-sm">
                                    <img src="{{ asset('storage/' . $product->imagem_url) }}" alt="{{ $product->nome }}" class="card-img-top" style="height: 200px; object-fit: cover;">
                                    <div class="card-body">
                                        <h5 class="card-title">Nome: {{ $product->nome }}</h5>
                                        <p class="card-text">
                                            <strong>R$ {{ number_format($product->valor, 2, ',', '.') }}</strong>
                                            @if($product->quantidade <= 0)
                                                <span class="badge badge-danger ml-2">Esgotado</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="card-footer bg-white border-top-0 d-flex justify-content-between">
                                        @if($product->quantidade > 0)
                                            <button wire:loading.attr="disabled" wire:click="addToCart({{ $product->id }})" class="btn btn-outline-success btn-sm">
                                                Adicionar ao Carrinho
                                            </button>
                                            <button wire:loading.attr="disabled" wire:click="buyNow({{ $product->id }})" class="btn btn-primary btn-sm">
                                                Comprar
                                            </button>
                                        @else
                                            <button class="btn btn-secondary btn-sm" disabled>
                                                Esgotado
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>                    
                    <div class="text-center mt-3">
                        <button wire:click="loadMore" class="btn btn-secondary" wire:loading.class="d-none">
                            Carregar mais
                        </button>
                        <div wire:loading wire:target="loadMore" class="text-center mt-2">
                            <i class="fas fa-spinner fa-spin fa-2x"></i>
                        </div>                                               
                    </div>                                 
                </div>
            </div>
        </div>
    </main>

    <button onclick="scrollToTop()" class="fixar-botao btn btn-dark rounded-circle shadow btn-scroll-top">
        <i class="fas fa-chevron-up"></i>
    </button>

    <style>
        .modal-body-scroll {
            max-height: 650px;
            overflow-y: auto;
        }
        .cart-badge {
            position: absolute;
            top: -12px;
            right: -5px;
            background-color: #ff0000;
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
        .carousel-control-prev,
        .carousel-control-next {
            width: 5%;
            top: 50%; 
            transform: translateY(-50%);
            z-index: 10;
        }
        .carousel-control-prev {
            left: -3%;
        }
        .carousel-control-next {
            right: -3%; 
        }
        .carousel-control-prev-icon,
        .carousel-control-next-icon {
            background: none; 
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .carousel-control-prev-icon i,
        .carousel-control-next-icon i {
            font-size: 2rem; 
            color: black; 
        }
        .bg-color {
            background-color: #eeeeee;
        }
        .fixar-botao {
            position: fixed;
            bottom: 1rem;
            right: 1rem;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            z-index: 1050;
        }
        .category-card {
            position: relative;
            overflow: hidden;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
        }
        .category-card:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }
        .filter-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }
        .category-card:hover .filter-overlay {
            opacity: 1;
        }
        .filter-overlay button {
            opacity: 1;
            background-color: #007bff;
            border: none;
            color: #fff;
            padding: 10px 20px;
            border-radius: 20px;
            transition: background-color 0.3s ease-in-out;
        }
        .filter-overlay button:hover {
            background-color: #0056b3;
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
            background-color: #fff; 
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); 
            padding: 0.5rem 1rem; 
            height: 100px; 
        }
        .navbar-custom .navbar-nav .nav-link {
            color: #333; 
            font-size: 1.1rem;
            font-weight: 500;
        }
        .navbar-custom .dropdown-item {
            color: #333; 
        }
        .navbar-custom .dropdown-item:hover {
            background-color: #f8f9fa; 
        }
        .custom-dropdown-menu {
            width: 500px; 
            padding: 15px;
            border-radius: 10px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
        }
        .custom-dropdown-menu h6 {
            font-size: 1rem;
            font-weight: bold;
            color: #007bff;
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
            color: #333;
        }
        .total-amount {
            font-size: 1.25rem;
            font-weight: bold;
            color: #007bff;
        }
    </style>    

    <script>
        const scrollToTop = () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        };

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
