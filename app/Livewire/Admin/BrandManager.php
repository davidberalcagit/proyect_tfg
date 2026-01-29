<?php

namespace App\Livewire\Admin;

use App\Models\Brands;
use Livewire\Component;
use Livewire\WithPagination;

class BrandManager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    // Variables para Crear (Modal)
    public $nombre;
    public $isModalOpen = false;

    // Variables para Edición en Línea
    public $editingId = null;
    public $editingNombre = '';

    protected $rules = [
        'nombre' => 'required|string|max:255|unique:brands,nombre',
    ];

    public function render()
    {
        return view('livewire.admin.brand-manager', [
            'brands' => Brands::orderBy('id', 'desc')->paginate(10),
        ]);
    }

    // --- Lógica de Creación (Modal) ---

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function openModal()
    {
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->nombre = '';
        $this->resetErrorBag();
    }

    public function store()
    {
        $this->validate();

        Brands::create([
            'nombre' => $this->nombre
        ]);

        session()->flash('message', 'Marca creada correctamente.');
        $this->closeModal();
    }

    // --- Lógica de Edición en Línea ---

    public function edit($id)
    {
        $this->editingId = $id;
        $this->editingNombre = Brands::find($id)->nombre;
    }

    public function cancelEdit()
    {
        $this->editingId = null;
        $this->editingNombre = '';
    }

    public function update()
    {
        $this->validate([
            'editingNombre' => 'required|string|max:255|unique:brands,nombre,' . $this->editingId,
        ]);

        if ($this->editingId) {
            $brand = Brands::find($this->editingId);
            $brand->update([
                'nombre' => $this->editingNombre
            ]);

            session()->flash('message', 'Marca actualizada correctamente.');
            $this->cancelEdit();
        }
    }

    public function delete($id)
    {
        Brands::find($id)->delete();
        session()->flash('message', 'Marca eliminada correctamente.');
    }
}
