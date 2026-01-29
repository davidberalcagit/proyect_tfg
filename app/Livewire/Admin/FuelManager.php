<?php
namespace App\Livewire\Admin;
use App\Models\Fuels;
use Livewire\Component;
use Livewire\WithPagination;

class FuelManager extends Component {
    use WithPagination;
    protected $paginationTheme = 'tailwind';
    public $nombre, $fuel_id, $isModalOpen = false;
    protected $rules = ['nombre' => 'required|string|max:255|unique:fuels,nombre'];

    public function render() { return view('livewire.admin.fuel-manager', ['fuels' => Fuels::paginate(10)]); }
    public function create() { $this->reset(['nombre', 'fuel_id']); $this->isModalOpen = true; }
    public function store() {
        $this->validate();
        Fuels::updateOrCreate(['id' => $this->fuel_id], ['nombre' => $this->nombre]);
        $this->isModalOpen = false;
        session()->flash('message', 'Combustible guardado.');
    }
    public function edit($id) {
        $fuel = Fuels::findOrFail($id);
        $this->fuel_id = $id; $this->nombre = $fuel->nombre;
        $this->isModalOpen = true;
    }
    public function delete($id) { Fuels::find($id)->delete(); }
    public function closeModal() { $this->isModalOpen = false; }
}
