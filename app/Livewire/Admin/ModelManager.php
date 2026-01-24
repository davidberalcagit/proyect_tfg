<?php

namespace App\Livewire\Admin;

use App\Models\CarModels;
use App\Models\Brands;
use Livewire\Component;
use Livewire\WithPagination;

class ModelManager extends Component
{
    use WithPagination;

    public $nombre, $id_marca, $model_id;
    public $isModalOpen = false;

    protected $rules = [
        'nombre' => 'required|string|max:255',
        'id_marca' => 'required|exists:brands,id',
    ];

    public function render()
    {
        return view('livewire.admin.model-manager', [
            'models' => CarModels::with('marca')->orderBy('id', 'desc')->paginate(10),
            'brands' => Brands::orderBy('nombre')->get(),
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
        $this->id_marca = '';
        $this->model_id = null;
        $this->resetErrorBag();
    }

    public function store()
    {
        $this->validate();

        CarModels::updateOrCreate(['id' => $this->model_id], [
            'nombre' => $this->nombre,
            'id_marca' => $this->id_marca
        ]);

        session()->flash('message', $this->model_id ? 'Modelo actualizado.' : 'Modelo creado.');
        $this->closeModal();
    }

    public function edit($id)
    {
        $model = CarModels::findOrFail($id);
        $this->model_id = $id;
        $this->nombre = $model->nombre;
        $this->id_marca = $model->id_marca;
        $this->openModal();
    }

    public function delete($id)
    {
        CarModels::find($id)->delete();
        session()->flash('message', 'Modelo eliminado.');
    }
}
