<?php

namespace App\Livewire;

use App\Models\Brands;
use App\Models\Cars;
use Livewire\Component;
use Livewire\WithPagination;

class CarFilter extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $search = '';
    public $brand = '';
    public $min_price = '';
    public $max_price = '';
    public $sort = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'brand' => ['except' => ''],
        'min_price' => ['except' => ''],
        'max_price' => ['except' => ''],
        'sort' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Cars::with(['marca', 'modelo', 'status'])
            ->whereIn('id_estado', [1, 3]); // Mostrar En Venta (1) y En Alquiler (3)

        if ($this->search) {
            $query->where(function($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhereHas('marca', function($q) {
                      $q->where('nombre', 'like', '%' . $this->search . '%');
                  })
                  ->orWhereHas('modelo', function($q) {
                      $q->where('nombre', 'like', '%' . $this->search . '%');
                  });
            });
        }

        if ($this->brand) {
            $query->where('id_marca', $this->brand);
        }

        if ($this->min_price) {
            $query->where('precio', '>=', $this->min_price);
        }

        if ($this->max_price) {
            $query->where('precio', '<=', $this->max_price);
        }

        if ($this->sort === 'recent') {
            $query->orderBy('created_at', 'desc');
        } elseif ($this->sort === 'cheap') {
            $query->orderBy('precio', 'asc');
        } elseif ($this->sort === 'expensive') {
            $query->orderBy('precio', 'desc');
        } else {
            $query->inRandomOrder();
        }

        return view('livewire.car-filter', [
            'cars' => $query->paginate(12),
            'brands' => Brands::orderBy('nombre')->get(),
        ]);
    }
}
