<?php

namespace App\Livewire\MyCollection;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

/**
 * Lietotāja kolekcijas lapa, kas attēlo izlases un "Lasīt vēlāk" sarakstus
 * Šī komponente nodrošina pārslēgšanos starp dažādiem kolekcijas skatiem
 */
class CollectionPage extends Component
{
    public $tab = 'favorites';
    public $favorites = [];
    public $readLater = [];

    protected $listeners = [
        'refreshBooks' => '$refresh'
    ];

    /**
     * Inicializē komponenti ar sākotnējiem datiem
     * @param string
     */
    public function mount($tab = 'favorites')
    {
        $this->tab = $tab;
        $this->loadCollectionData();
    }

    /**
     * Ielādē lietotāja kolekcijas datus atkarībā no aktīvās cilnes
     */
    public function loadCollectionData()
    {
        $user = Auth::user();

        if ($this->tab === 'favorites') {
            $this->favorites = $user->favorites()->with('product.category')->get();
            $this->readLater = collect();
        } else {
            $this->favorites = collect();
            $this->readLater = $user->readLater()->with('product.category')->get();
        }
    }

    /**
     * Pārslēdz aktīvo cilni un ielādē atbilstošos datus
     * @param string
     */
    public function switchTab($tab)
    {
        $this->tab = $tab;
        $this->loadCollectionData();
    }

    /**
     * Renderē komponentes skatu
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.my-collection.collection-page');
    }
}
