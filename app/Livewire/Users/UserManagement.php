<?php

namespace App\Livewire\Users;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Lietotāju pārvaldības komponente
 * 
 * Šī Livewire komponente nodrošina lietotāju pārvaldības funkcionalitāti, tajā skaitā:
 * - Lietotāju saraksta attēlošanu ar lapošanu
 * - Lietotāju meklēšanu pēc vārda vai e-pasta
 * - Lietotāju filtrēšanu pēc tipa (administrators/lietotājs) un statusa (aktīvs/bloķēts)
 * - Lietotāju kārtošanu pēc dažādiem kritērijiem
 */
class UserManagement extends Component
{
    use WithPagination;

    public $searchQuery = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $filterUserType = '';
    public $filterBanStatus = '';
    public $sortOption = 'newest';

    protected $queryString = [
        'searchQuery' => ['except' => ''],
        'sortOption' => ['except' => 'newest'],
        'filterUserType' => ['except' => ''],
        'filterBanStatus' => ['except' => ''],
    ];

    /**
     * Inicializē komponenti
     * Šī metode tiek izsaukta, kad komponente tiek pirmoreiz ielādēta
     */
    public function mount()
    {
        $this->updateSortFieldAndDirection();
    }

    /**
     * Renderē komponentes skatu
     * Šī metode tiek izsaukta, kad komponenti nepieciešams pārrenderēt
     */
    public function render()
    {
        $users = $this->getUsers();
        return view('livewire.users.user-management', [
            'users' => $users,
            'totalUsers' => $this->getTotalUsers(),
        ]);
    }

    /**
     * Iegūst filtrētus un kārtotus lietotājus ar lapošanu
     * Šī metode pielieto visus aktīvos filtrus un kārtošanu lietotāju vaicājumam
     */
    public function getUsers()
    {
        $query = User::query();

        if (!empty($this->searchQuery)) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->searchQuery}%")
                    ->orWhere('email', 'like', "%{$this->searchQuery}%");
            });
        }

        if (!empty($this->filterUserType) && in_array($this->filterUserType, ['admin', 'user'])) {
            $query->where('usertype', $this->filterUserType);
        }

        if ($this->filterBanStatus === 'banned') {
            $query->whereHas('activeBan');
        } elseif ($this->filterBanStatus === 'active') {
            $query->whereDoesntHave('activeBan');
        }

        $query->orderBy($this->sortField, $this->sortDirection);

        return $query->paginate(10);
    }

    /**
     * Iegūst kopējo filtrēto lietotāju skaitu
     * Šī metode pielieto tos pašus filtrus, ko getUsers(), bet atgriež skaitu
     */
    public function getTotalUsers()
    {
        $query = User::query();

        if (!empty($this->searchQuery)) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->searchQuery}%")
                    ->orWhere('email', 'like', "%{$this->searchQuery}%");
            });
        }

        if (!empty($this->filterUserType) && in_array($this->filterUserType, ['admin', 'user'])) {
            $query->where('usertype', $this->filterUserType);
        }

        if ($this->filterBanStatus === 'banned') {
            $query->whereHas('activeBan');
        } elseif ($this->filterBanStatus === 'active') {
            $query->whereDoesntHave('activeBan');
        }

        return $query->count();
    }

    /**
     * Atiestata lapošanu, kad mainās meklēšanas vaicājums
     * Tas nodrošina, ka sākam no 1. lapas, kad tiek pielietots jauns meklējums
     */
    public function updatedSearchQuery()
    {
        $this->resetPage();
    }

    /**
     * Atiestata lapošanu, kad mainās lietotāja tipa filtrs
     */
    public function updatedFilterUserType()
    {
        $this->resetPage();
    }

    /**
     * Atiestata lapošanu, kad mainās bloķēšanas statusa filtrs
     */
    public function updatedFilterBanStatus()
    {
        $this->resetPage();
    }

    /**
     * Atjaunina kārtošanas iestatījumus un atiestata lapošanu, kad mainās kārtošanas opcija
     */
    public function updatedSortOption()
    {
        $this->updateSortFieldAndDirection();
        $this->resetPage();
    }

    /**
     * Pārveido lietotājam draudzīgu kārtošanas opciju par faktisko lauku un virzienu
     * Šī metode pārvērš opcijas, piemēram, "newest" uz atbilstošiem datu bāzes laukiem
     */
    private function updateSortFieldAndDirection()
    {
        switch ($this->sortOption) {
            case 'oldest':
                $this->sortField = 'created_at';
                $this->sortDirection = 'asc';
                break;
            case 'nameAZ':
                $this->sortField = 'name';
                $this->sortDirection = 'asc';
                break;
            case 'nameZA':
                $this->sortField = 'name';
                $this->sortDirection = 'desc';
                break;
            default:
                $this->sortField = 'created_at';
                $this->sortDirection = 'desc';
                break;
        }
    }

    /**
     * Notīra visus filtrus un atiestata uz noklusējuma vērtībām
     */
    public function clearFilters()
    {
        $this->searchQuery = '';
        $this->sortOption = 'newest';
        $this->updateSortFieldAndDirection();
        $this->filterUserType = '';
        $this->filterBanStatus = '';
        $this->resetPage();
        
        $this->dispatch('filtersCleared');
    }

    /**
     * Pārbauda, vai ir kādi aktīvi filtri
     */
    public function hasActiveFilters()
    {
        return !empty($this->searchQuery) ||
            !empty($this->filterUserType) ||
            !empty($this->filterBanStatus) ||
            $this->sortOption !== 'newest';
    }

    /**
     * Pārbauda, vai jāattēlo filtru informācijas rinda
     */
    public function showFilterInfo()
    {
        return $this->hasActiveFilters() || $this->getTotalUsers() > 0;
    }
}
