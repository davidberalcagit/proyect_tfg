<?php

namespace App\Livewire;

use App\Models\Cars;
use App\Models\Offer;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MakeOffer extends Component
{
    public $car;
    public $cantidad;
    public $isModalOpen = false;

    protected $rules = [
        'cantidad' => 'required|numeric|min:1',
    ];

    public function mount(Cars $car)
    {
        $this->car = $car;
    }

    public function openModal()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        if (Auth::user()->customer && Auth::user()->customer->id === $this->car->id_vendedor) {
            session()->flash('error', 'No puedes ofertar por tu propio coche.');
            return;
        }

        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->reset(['cantidad']);
    }

    public function submitOffer()
    {
        $this->validate();

        if (!Auth::user()->customer) {
            session()->flash('error', 'Necesitas un perfil de cliente.');
            return;
        }

        $buyerId = Auth::user()->customer->id;

        $existingOffer = Offer::where('id_vehiculo', $this->car->id)
            ->where('id_comprador', $buyerId)
            ->pending()
            ->exists();

        if ($existingOffer) {
            session()->flash('error', 'Ya tienes una oferta pendiente para este coche.');
            return;
        }

        Offer::create([
            'id_vehiculo' => $this->car->id,
            'id_comprador' => $buyerId,
            'id_vendedor' => $this->car->id_vendedor,
            'cantidad' => $this->cantidad,
            'estado' => 'pending'
        ]);

        session()->flash('message', 'Oferta enviada correctamente.');
        $this->closeModal();
    }

    public function render()
    {
        return view('livewire.make-offer');
    }
}
