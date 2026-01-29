<?php
namespace App\Livewire\Admin;
use App\Models\Gears;
use Livewire\Component;
use Livewire\WithPagination;

class GearManager extends Component {
    use WithPagination;
    protected $paginationTheme = 'tailwind';
    public $tipo, $gear_id, $isModalOpen = false;
    protected $rules = ['tipo' => 'required|string|max:255|unique:gears,tipo'];

    public function render() { return view('livewire.admin.gear-manager', ['gears' => Gears::paginate(10)]); }
    public function create() { $this->reset(['tipo', 'gear_id']); $this->isModalOpen = true; }
    public function store() {
        $this->validate();
        Gears::updateOrCreate(['id' => $this->gear_id], ['tipo' => $this->tipo]);
        $this->isModalOpen = false;
        session()->flash('message', 'Caja de cambios guardada.');
    }
    public function edit($id) {
        $g = Gears::findOrFail($id); $this->gear_id = $id; $this->tipo = $g->tipo; $this->isModalOpen = true;
    }
    public function delete($id) { Gears::find($id)->delete(); }
    public function closeModal() { $this->isModalOpen = false; }
}
