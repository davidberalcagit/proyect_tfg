<?php
namespace App\Livewire\Admin;
use App\Models\Fuels;
use Livewire\Component;
use Livewire\WithPagination;

class FuelManager extends Component {
    use WithPagination;
    protected $paginationTheme = 'tailwind';

    public $editingFuelId = null;
    public $editingFuelName;

    public $newFuelName;

    protected $listeners = ['refreshComponent' => '$refresh'];

    protected function rules() {
        return [
            'editingFuelName' => 'required|string|max:255|unique:fuels,nombre,' . $this->editingFuelId,
            'newFuelName' => 'required|string|max:255|unique:fuels,nombre',
        ];
    }

    public function render() {
        return view('livewire.admin.fuel-manager', [
            'fuels' => Fuels::orderBy('id', 'desc')->paginate(10)
        ]);
    }

    public function edit($id) {
        $fuel = Fuels::findOrFail($id);
        $this->editingFuelId = $id;
        $this->editingFuelName = $fuel->nombre;
    }

    public function cancelEdit() {
        $this->editingFuelId = null;
        $this->editingFuelName = '';
    }

    public function update() {
        $this->validate([
            'editingFuelName' => 'required|string|max:255|unique:fuels,nombre,' . $this->editingFuelId,
        ]);
        Fuels::find($this->editingFuelId)->update(['nombre' => $this->editingFuelName]);
        $this->cancelEdit();
        session()->flash('message', 'Combustible actualizado.');
        $this->dispatch('refreshComponent');
    }

    public function store() {
        $this->validate([
            'newFuelName' => 'required|string|max:255|unique:fuels,nombre',
        ]);
        Fuels::create(['nombre' => $this->newFuelName]);
        $this->newFuelName = '';
        session()->flash('message', 'Combustible guardado.');
        $this->dispatch('refreshComponent');
    }

    public function delete($id) {
        Fuels::find($id)->delete();
        session()->flash('message', 'Combustible eliminado.');
        $this->dispatch('refreshComponent');
    }
}
