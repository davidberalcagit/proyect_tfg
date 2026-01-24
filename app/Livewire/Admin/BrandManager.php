<?php

namespace App\Livewire\Admin;

use App\Models\Brands;
use Livewire\Component;
use Livewire\WithPagination;

class BrandManager extends Component
{
    use WithPagination;

    public $nombre;
    public $brand_id;
    public $isModalOpen = false;
    public $confirmingDeletion = false;

    protected $rules = [
        'nombre' => 'required|string|max:255|unique:brands,nombre',
    ];

    public function render()
    {
        return view('livewire.admin.brand-manager', [
            'brands' => Brands::orderBy('id', 'desc')->paginate(10),
        ]);
    }

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
        $this->brand_id = null;
        $this->resetErrorBag();
    }

    public function store()
    {
        $this->validate();

        Brands::updateOrCreate(['id' => $this->brand_id], [
            'nombre' => $this->nombre
        ]);

        session()->flash('message', $this->brand_id ? 'Marca actualizada.' : 'Marca creada.');

        $this->closeModal();
    }

    public function edit($id)
    {
        $brand = Brands::findOrFail($id);
        $this->brand_id = $id;
        $this->nombre = $brand->nombre;

        $this->openModal();
    }

    public function delete($id)
    {
        Brands::find($id)->delete();
        session()->flash('message', 'Marca eliminada correctamente.');
    }
}
