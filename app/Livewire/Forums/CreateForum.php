<?php

namespace App\Livewire\Forums;

use Livewire\Component;
use App\Models\Forum;

class CreateForum extends Component
{
    public $title = '';
    public $description = '';

    protected $rules = [
        'title' => 'required|min:3|max:50',
        'description' => 'required|min:10|max:1000'
    ];

    public function createForum()
    {
        $this->validate();

        $forum = Forum::create([
            'title' => $this->title,
            'description' => $this->description,
            'user_id' => auth()->id()
        ]);

        return redirect()->route('forums.view', $forum)
            ->with('success', 'Forum created successfully!');
    }

    public function render()
    {
        return view('livewire.forums.create-forum');
    }
}
