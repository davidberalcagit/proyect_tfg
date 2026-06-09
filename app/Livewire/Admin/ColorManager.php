<?php
namespace App\Livewire\Admin;
use App\Models\Color;
use Livewire\Component;
use Livewire\WithPagination;

class ColorManager extends Component {
    use WithPagination;
    protected $paginationTheme = 'tailwind';

    public $editingColorId = null;
    public $editingColorName;
    public $editingHexCode;

    public $newColorName;
    public $newHexCode;

    protected $listeners = ['refreshComponent' => '$refresh'];

    protected function rules() {
        return [
            'editingColorName' => 'required|string|max:255|unique:colors,nombre,' . $this->editingColorId,
            'editingHexCode' => 'nullable|string|max:7|starts_with:#',
            'newColorName' => 'required|string|max:255|unique:colors,nombre',
            'newHexCode' => 'nullable|string|max:7|starts_with:#',
        ];
    }

    public function render() {
        return view('livewire.admin.color-manager', [
            'colors' => Color::orderBy('id', 'desc')->paginate(10)
        ]);
    }

    public function edit($colorId) {
        $color = Color::findOrFail($colorId);
        $this->editingColorId = $color->id;
        $this->editingColorName = $color->nombre;
        $this->editingHexCode = ''; // Not stored, so we don't load it
    }

    public function cancelEdit() {
        $this->editingColorId = null;
        $this->editingColorName = '';
        $this->editingHexCode = '';
    }

    public function update() {
        $this->validate([
            'editingColorName' => 'required|string|max:255|unique:colors,nombre,' . $this->editingColorId,
            'editingHexCode' => 'nullable|string|max:7|starts_with:#',
        ]);

        $color = Color::find($this->editingColorId);
        $color->update(['nombre' => $this->editingColorName]);

        $this->cancelEdit();
        session()->flash('message', 'Color actualizado.');
        $this->dispatch('refreshComponent');
    }

    public function store() {
        $this->validate([
            'newColorName' => 'required|string|max:255|unique:colors,nombre',
            'newHexCode' => 'nullable|string|max:7|starts_with:#',
        ]);

        Color::create(['nombre' => $this->newColorName]);

        $this->newColorName = '';
        $this->newHexCode = '';
        session()->flash('message', 'Color creado.');
        $this->dispatch('refreshComponent');
    }

    public function delete($id) {
        Color::find($id)->delete();
        session()->flash('message', 'Color eliminado.');
        $this->dispatch('refreshComponent');
    }
}
