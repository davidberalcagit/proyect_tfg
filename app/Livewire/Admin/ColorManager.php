<?php
namespace App\Livewire\Admin;
use App\Models\Color;
use Livewire\Component;
use Livewire\WithPagination;

class ColorManager extends Component {
    use WithPagination;
    public $nombre, $color_id, $isModalOpen = false;
    protected $rules = ['nombre' => 'required|string|max:255|unique:colors,nombre'];

    public function render() { return view('livewire.admin.color-manager', ['colors' => Color::paginate(10)]); }
    public function create() { $this->reset(['nombre', 'color_id']); $this->isModalOpen = true; }
    public function store() {
        $this->validate();
        Color::updateOrCreate(['id' => $this->color_id], ['nombre' => $this->nombre]);
        $this->isModalOpen = false;
        session()->flash('message', 'Color guardado.');
    }
    public function edit($id) {
        $c = Color::findOrFail($id); $this->color_id = $id; $this->nombre = $c->nombre; $this->isModalOpen = true;
    }
    public function delete($id) { Color::find($id)->delete(); }
    public function closeModal() { $this->isModalOpen = false; }
}
