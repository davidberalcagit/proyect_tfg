<?php

namespace App\Livewire\Admin;

use App\Models\Brands;
use Livewire\Component;
use Livewire\WithPagination;

class BrandManager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $editingBrandId = null;
    public $editingBrandName;

    public $newBrandName;

    protected $listeners = ['refreshComponent' => '$refresh'];

    protected function rules()
    {
        return [
            'editingBrandName' => 'required|string|max:255|unique:brands,nombre,' . $this->editingBrandId,
            'newBrandName' => 'required|string|max:255|unique:brands,nombre',
        ];
    }

    public function render()
    {
        return view('livewire.admin.brand-manager', [
            'brands' => Brands::withCount('models')->orderBy('id', 'desc')->paginate(10),
        ]);
    }

    public function edit($id)
    {
        $brand = Brands::findOrFail($id);
        $this->editingBrandId = $id;
        $this->editingBrandName = $brand->nombre;
    }

    public function cancelEdit()
    {
        $this->editingBrandId = null;
        $this->editingBrandName = '';
    }

    public function update()
    {
        $this->validate([
            'editingBrandName' => 'required|string|max:255|unique:brands,nombre,' . $this->editingBrandId,
        ]);

        Brands::find($this->editingBrandId)->update(['nombre' => $this->editingBrandName]);

        $this->cancelEdit();
        session()->flash('message', 'Marca actualizada.');
        $this->dispatch('refreshComponent');
    }

    public function store()
    {
        $this->validate([
            'newBrandName' => 'required|string|max:255|unique:brands,nombre',
        ]);

        Brands::create(['nombre' => $this->newBrandName]);

        $this->newBrandName = '';
        session()->flash('message', 'Marca creada.');
        $this->dispatch('refreshComponent');
    }

    public function delete($id)
    {
        Brands::find($id)->delete();
        session()->flash('message', 'Marca eliminada.');
        $this->dispatch('refreshComponent');
    }
}
