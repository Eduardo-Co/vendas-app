<div class="container py-5">
    @if(session()->has('message') || session()->has('message-deleted'))
        <div id="toastrMsg" class="alert @if(session()->has('message')) alert-success @elseif(session()->has('message-deleted')) alert-danger @endif alert-dismissible fade show" role="alert">
            @if(session()->has('message'))
                <strong>{{ session('message') }}</strong>
            @endif
            
            @if(session()->has('message-deleted'))
                <strong>{{ session('message-deleted') }}</strong>
            @endif
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($isEditing || $isCreating)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0, 0, 0, 0.5);" wire:click.self="resetInputFields">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $isCreating ? 'Create User' : 'Edit User' }}</h5>
                        <button type="button" class="close" wire:click="resetInputFields" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form wire:submit.prevent="save">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input 
                                        type="text" 
                                        id="name" 
                                        wire:model="name" 
                                        class="form-control"
                                    />
                                    @error('name') <div class="text-danger">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input 
                                        type="email" 
                                        id="email" 
                                        wire:model="email" 
                                        class="form-control"
                                    />
                                    @error('email') <div class="text-danger">{{ $message }}</div> @enderror
                                </div>
                                @if ($isCreating)
                                    <div class="col-md-6 mb-3">
                                        <label for="password" class="form-label">Password</label>
                                        <input 
                                            type="password" 
                                            id="password" 
                                            wire:model="password" 
                                            class="form-control"
                                        />
                                        @error('password') <div class="text-danger">{{ $message }}</div> @enderror
                                    </div>
                                @endif
                                @if($profile != "administrator")
                                    <div class="col-md-6 mb-3">
                                        <label for="status" class="form-label">Status</label>
                                        <select 
                                            id="status" 
                                            wire:model="status" 
                                            class="w-100 px-3 py-2 border border-secondary rounded shadow-sm focus:outline-none focus:ring-0"
                                            >
                                            <option value="">Select Status</option>
                                            <option value="actived">Actived</option>
                                            <option value="inactived">Inactived</option>
                                            <option value="pre_registred">Pre Registered</option>
                                        </select>
                                        @error('status') <div class="text-danger">{{ $message }}</div> @enderror
                                    </div>
                                @endif
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Gender</label>
                                    <div class="form-check">
                                        <input 
                                            type="radio" 
                                            name="gender" 
                                            value="male" 
                                            wire:model="gender" 
                                            class="form-check-input"
                                        />
                                        <label class="form-check-label">Male</label>
                                    </div>
                                    <div class="form-check">
                                        <input 
                                            type="radio" 
                                            name="gender" 
                                            value="female" 
                                            wire:model="gender" 
                                            class="form-check-input"
                                        />
                                        <label class="form-check-label">Female</label>
                                    </div>
                                    @error('gender') <div class="text-danger">{{ $message }}</div> @enderror
                                </div>   
                                <div class="col-md-6 mb-3">
                                    <label for="profile" class="form-label">Profile</label>
                                    <input 
                                        type="text" 
                                        id="profile" 
                                        wire:model="profile" 
                                        value="{{ $isCreating ? 'user' : $profile }}" 
                                        class="form-control"
                                        disabled
                                    />
                                    @error('profile') <div class="text-danger">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button 
                                type="submit" 
                                class="btn btn-success"
                                wire:loading.attr="disabled"
                            >
                                {{ $isCreating ? 'Create User' : 'Update User' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    @if($viewingUser)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0, 0, 0, 0.5);" wire:click.self="closeView">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">View User</h5>
                        <button type="button" class="close" wire:click="closeView" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Name</label>
                                <input 
                                    type="text" 
                                    value="{{ $viewingUser->name }}" 
                                    class="form-control bg-light" 
                                    disabled
                                />
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input 
                                    type="text" 
                                    value="{{ $viewingUser->email }}" 
                                    class="form-control bg-light" 
                                    disabled
                                />
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Gender</label>
                                <input 
                                    type="text" 
                                    value="{{ $viewingUser->gender }}" 
                                    class="form-control bg-light" 
                                    disabled
                                />
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Profile</label>
                                <input 
                                    type="text" 
                                    value="{{ $viewingUser->profile }}" 
                                    class="form-control bg-light" 
                                    disabled
                                />
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status</label>
                                <input 
                                    type="text" 
                                    value="{{ $viewingUser->status }}" 
                                    class="form-control bg-light" 
                                    disabled
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($showDeleteModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0, 0, 0, 0.5);" wire:click.self="closeDeleteModal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirm Deletion</h5>
                    </div>
                    <form wire:submit.prevent="delete({{ $musicToDelete }})">
                        <div class="modal-body">
                            <p>Are you sure you want to delete this music?</p>
                        </div>
                        <div class="modal-footer">
                            <button 
                                type="button" 
                                wire:click="closeDeleteModal" 
                                class="btn btn-secondary"
                            >
                                Cancel
                            </button>
                            <button 
                                type="submit" 
                                class="btn btn-danger"
                            >
                                Delete
                            </button>
                        </div>
                    </form>
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
                Add User
            </button>
        </div>
    @endunless


    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead class="table-light">
                <tr>             
                    <th>Name</th>
                    <th>Email</th>
                    <th>Gender</th>
                    <th>Profile</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr class="text-center">
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->gender }}</td>
                        <td>{{ $user->profile }}</td>
                        <td>{{ $user->status }}</td>
                        <td>
                            <button 
                                wire:click="edit({{ $user->id }})" 
                                class="btn btn-warning btn-sm"
                                wire:loading.attr="disabled"
                            >
                                Edit
                            </button>
                            <button 
                                wire:click="confirmDelete({{ $user->id }})" 
                                class="btn btn-danger btn-sm"
                                wire:loading.attr="disabled"
                            >
                                Delete
                            </button>
                            <button 
                                wire:click="view({{ $user->id }})" 
                                class="btn btn-info btn-sm"
                                wire:loading.attr="disabled"
                            >
                                View
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">No users found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="flex flex-row mt-2">
        {{ $users->links() }}
    </div>
    
</div>
