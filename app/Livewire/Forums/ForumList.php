<?php

namespace App\Livewire\Forums;

use Livewire\Component;
use App\Models\Forum;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class ForumList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $search = '';
    public $sortBy = 'latest';

    protected $queryString = [
        'search' => ['except' => ''],
        'sortBy' => ['except' => 'latest']
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedSortBy()
    {
        $this->resetPage();
    }

    public function render()
    {
        $forums = Forum::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->sortBy === 'latest', function ($query) {
                $query->latest('created_at');
            })
            ->when($this->sortBy === 'oldest', function ($query) {
                $query->oldest('created_at');
            })
            ->withCount('replies')
            ->with('user')
            ->paginate(10);

        return view('livewire.forums.forum-list', [
            'forums' => $forums
        ]);
    }
}
