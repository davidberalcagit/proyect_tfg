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
    public $sortField = 'id';
    public $sortDirection = 'asc';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function delete($id)
    {
        if ($id === auth()->id()) {
            session()->flash('error', 'No puedes eliminar tu propia cuenta.');
            return;
        }

        User::find($id)->delete();
        session()->flash('message', 'Usuario eliminado correctamente.');
    }

    public function render()
    {
        $query = User::query()
            ->with('customer')
            ->select('users.*');

        if ($this->search) {
            $query->where(function($q) {
                $q->where('users.name', 'like', '%' . $this->search . '%')
                  ->orWhere('users.email', 'like', '%' . $this->search . '%')
                  ->orWhereHas('customer', function($q2) {
                      $q2->where('nombre_contacto', 'like', '%' . $this->search . '%');
                  });
            });
        }

        if ($this->sortField === 'seller_name') {
            $query->leftJoin('customers', 'users.id', '=', 'customers.id_usuario')
                  ->orderBy('customers.nombre_contacto', $this->sortDirection);
        } else {
            $query->orderBy($this->sortField, $this->sortDirection);
        }

        $users = $query->paginate(10);

        return view('livewire.admin.user-manager', [
            'users' => $users,
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
        ]);
    }
}
