<?php

namespace App\Livewire;

use App\Models\Cars;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ToggleFavorite extends Component
{
    public $car;
    public $isFavorite;

    public function mount(Cars $car)
    {
        $this->car = $car;
        $this->isFavorite = Auth::user() ? Auth::user()->favorites->contains($car->id) : false;
    }

    public function toggle()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if ($this->isFavorite) {
            $user->favorites()->detach($this->car->id);
            $this->isFavorite = false;
        } else {
            $user->favorites()->attach($this->car->id);
            $this->isFavorite = true;
        }
    }

    public function render()
    {
        return view('livewire.toggle-favorite');
    }
}
