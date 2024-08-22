<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User as UserModel;

class User extends Component
{
    use WithPagination;

    public $user; 
    public $name;
    public $email;
    public $password;
    public $status;
    public $gender;
    public $profile;
    public $selectedUserId;
    public $searchTerm = '';
    public $isEditing = false; 
    public $isCreating = false; 
    public $viewingUser = false;
    public $musicToDelete;
    public $showDeleteModal = false;	

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:8',
        'status' => 'required|string|max:50',
        'gender' => 'required|string|max:10',
        'profile' => 'required|string|max:255',
    ];

    protected $paginationTheme = 'bootstrap';


    public function view($userId)
    {
        $this->viewingUser = UserModel::findOrFail($userId);
    }

    public function closeView()
    {
        $this->viewingUser = null;
    }


    public function updatingSearchTerm()
    {
        $this->resetPage();
    }

    public function render()
    {
        $users = UserModel::where('name', 'like', '%' . $this->searchTerm . '%')
                          ->paginate(6);

        $start = max($users->currentPage() - 2, 1);
        $end = min($users->currentPage() + 2, $users->lastPage());

        return view('livewire.user', compact('users', 'start', 'end'));
    }

    public function edit($userId)
    {
        $user = UserModel::findOrFail($userId);
        $this->selectedUserId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->status = $user->status;
        $this->gender = $user->gender;
        $this->profile = $user->profile;
        $this->isEditing = true; 
        $this->isCreating = false; 
    }

    public function create()
    {
        $this->resetInputFields();
        $this->isEditing = false; 
        $this->isCreating = true;
        $this->profile = "user";
    }

    public function save()
    {
        if ($this->isEditing) {
            $this->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $this->selectedUserId,
                'password' => 'nullable|string|min:8',
                'status' => 'nullable|string|max:50',
                'gender' => 'nullable|string|max:10',
                'profile' => 'nullable|string|max:255',
            ]);
            
            $user = UserModel::find($this->selectedUserId);
            $user->update([
                'name' => $this->name,
                'email' => $this->email,
                'status' => $this->status,
                'gender' => $this->gender,
                'profile' => $this->profile,
                'password' => $this->password ? bcrypt($this->password) : $user->password,
            ]);
            session()->flash('message', 'User updated successfully.');
        } elseif ($this->isCreating) {
            $this->validate();
            
            UserModel::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => bcrypt($this->password),
                'status' => $this->status,
                'gender' => $this->gender,
                'profile' => $this->profile,
            ]);
            session()->flash('message', 'User created successfully.');
        }

        $this->resetInputFields();
    }

    public function resetInputFields()
    {
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->status = '';
        $this->gender = '';
        $this->profile = '';
        $this->selectedUserId = null;
        $this->isEditing = false; 
        $this->isCreating = false; 
    }
    public function delete()
    {
        try {
            $user = UserModel::findOrFail($this->musicToDelete);
    
            if ($user->playlists()->exists()) {
                session()->flash('message-deleted', 'Não é possível deletar o usuário, pois ele possui playlists.');
                return;
            }
    
            if ($user->favoriteMusics()->exists()) {
                session()->flash('message-deleted', 'Não é possível deletar o usuário, pois ele tem músicas favoritas.');
                return;
            }
    
            $user->delete();
            session()->flash('message-deleted', 'Usuário deletado com sucesso.');
            
        } catch (\Exception $e) {
            session()->flash('message-deleted', 'Erro ao deletar o usuário.');
        }finally{
            $this->musicToDelete = null;
            $this->showDeleteModal = false;
        }
    }
    public function confirmDelete($musicId)
    {
        $this->musicToDelete = $musicId;
        $this->showDeleteModal = true;
    }
    public function closeDeleteModal()
    {
            $this->showDeleteModal = false;
            $this->musicToDelete = null;
    }
}
