<?php

namespace App\Livewire\Admin;

use App\Models\CarModels;
use App\Models\Brands;
use Livewire\Component;
use Livewire\WithPagination;

class ModelManager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $editingModelId = null;
    public $editingModelName;
    public $editingModelBrandId;

    public $newModelName;
    public $newModelBrandId;

    protected $listeners = ['refreshComponent' => '$refresh'];

    protected function rules()
    {
        return [
            'editingModelName' => 'required|string|max:255',
            'editingModelBrandId' => 'required|exists:brands,id',
            'newModelName' => 'required|string|max:255',
            'newModelBrandId' => 'required|exists:brands,id',
        ];
    }

    public function render()
    {
        return view('livewire.admin.model-manager', [
            'models' => CarModels::with('marca')->orderBy('id', 'desc')->paginate(10),
            'brands' => Brands::orderBy('nombre')->get(),
        ]);
    }

    public function edit($id)
    {
        $model = CarModels::findOrFail($id);
        $this->editingModelId = $id;
        $this->editingModelName = $model->nombre;
        $this->editingModelBrandId = $model->id_marca;
    }

    public function cancelEdit()
    {
        $this->editingModelId = null;
        $this->editingModelName = '';
        $this->editingModelBrandId = '';
    }

    public function update()
    {
        $this->validate([
            'editingModelName' => 'required|string|max:255',
            'editingModelBrandId' => 'required|exists:brands,id',
        ]);

        CarModels::find($this->editingModelId)->update([
            'nombre' => $this->editingModelName,
            'id_marca' => $this->editingModelBrandId,
        ]);

        $this->cancelEdit();
        session()->flash('message', 'Modelo actualizado.');
        $this->dispatch('refreshComponent');
    }

    public function store()
    {
        $this->validate([
            'newModelName' => 'required|string|max:255',
            'newModelBrandId' => 'required|exists:brands,id',
        ]);

        CarModels::create([
            'nombre' => $this->newModelName,
            'id_marca' => $this->newModelBrandId,
        ]);

        $this->newModelName = '';
        $this->newModelBrandId = '';
        session()->flash('message', 'Modelo creado.');
        $this->dispatch('refreshComponent');
    }

    public function delete($id)
    {
        CarModels::find($id)->delete();
        session()->flash('message', 'Modelo eliminado.');
        $this->dispatch('refreshComponent');
    }
}
