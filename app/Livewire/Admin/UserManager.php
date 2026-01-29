<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class UserManager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        // Evitar borrarse a sí mismo
        if ($id === auth()->id()) {
            session()->flash('error', 'No puedes eliminar tu propia cuenta.');
            return;
        }

        User::find($id)->delete();
        session()->flash('message', 'Usuario eliminado correctamente.');
    }

    public function render()
    {
        $users = User::where('name', 'like', '%' . $this->search . '%')
            ->orWhere('email', 'like', '%' . $this->search . '%')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin.user-manager', [
            'users' => $users
        ]);
    }
}
