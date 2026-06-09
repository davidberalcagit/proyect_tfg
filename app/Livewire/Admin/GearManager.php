<?php
namespace App\Livewire\Admin;
use App\Models\Gears;
use Livewire\Component;
use Livewire\WithPagination;

class GearManager extends Component {
    use WithPagination;
    protected $paginationTheme = 'tailwind';

    public $editingGearId = null;
    public $editingGearType;

    public $newGearType;

    protected $listeners = ['refreshComponent' => '$refresh'];

    protected function rules() {
        return [
            'editingGearType' => 'required|string|max:255|unique:gears,tipo,' . $this->editingGearId,
            'newGearType' => 'required|string|max:255|unique:gears,tipo',
        ];
    }

    public function render() {
        return view('livewire.admin.gear-manager', [
            'gears' => Gears::orderBy('id', 'desc')->paginate(10)
        ]);
    }

    public function edit($id) {
        $gear = Gears::findOrFail($id);
        $this->editingGearId = $id;
        $this->editingGearType = $gear->tipo;
    }

    public function cancelEdit() {
        $this->editingGearId = null;
        $this->editingGearType = '';
    }

    public function update() {
        $this->validate([
            'editingGearType' => 'required|string|max:255|unique:gears,tipo,' . $this->editingGearId,
        ]);
        Gears::find($this->editingGearId)->update(['tipo' => $this->editingGearType]);
        $this->cancelEdit();
        session()->flash('message', 'Caja de cambios actualizada.');
        $this->dispatch('refreshComponent');
    }

    public function store() {
        $this->validate([
            'newGearType' => 'required|string|max:255|unique:gears,tipo',
        ]);
        Gears::create(['tipo' => $this->newGearType]);
        $this->newGearType = '';
        session()->flash('message', 'Caja de cambios creada.');
        $this->dispatch('refreshComponent');
    }

    public function delete($id) {
        Gears::find($id)->delete();
        session()->flash('message', 'Caja de cambios eliminada.');
        $this->dispatch('refreshComponent');
    }
}
