<?php

namespace App\Livewire\MyCollection;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class CollectionPage extends Component
{
    public $tab = 'favorites';
    public $favorites = [];
    public $readLater = [];

    protected $listeners = [
        'refreshBooks' => '$refresh'
    ];

    public function mount($tab = 'favorites')
    {
        $this->tab = $tab;
        $this->loadCollectionData();
    }

    public function loadCollectionData()
    {
        $user = Auth::user();

        // Load only the data needed for the active tab to optimize performance
        if ($this->tab === 'favorites') {
            $this->favorites = $user->favorites()->with('product.category')->get();
            $this->readLater = collect();
        } else {
            $this->favorites = collect();
            $this->readLater = $user->readLater()->with('product.category')->get();
        }
    }

    public function switchTab($tab)
    {
        $this->tab = $tab;
        $this->loadCollectionData();
    }

    public function render()
    {
        return view('livewire.my-collection.collection-page');
    }
}
