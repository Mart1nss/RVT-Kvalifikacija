<?php

namespace App\Livewire\Forums;

use Livewire\Component;
use App\Models\Forum;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class ForumView extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public Forum $forum;
    public $newReply = '';
    public $sortRepliesBy = 'latest';

    protected $rules = [
        'newReply' => 'required|min:3|max:250'
    ];

    public function mount(Forum $forum)
    {
        $this->forum = $forum;
    }

    public function addReply()
    {
        try {
            $validatedData = $this->validate();

            $reply = $this->forum->replies()->create([
                'content' => $this->newReply,
                'user_id' => auth()->id()
            ]);

            if ($reply) {
                $this->reset('newReply');
                $this->dispatch('reply-added');
            } else {
                $this->dispatch('show-alert', type: 'error', message: 'Failed to add reply. Please try again.');
            }
        } catch (\Exception $e) {
            logger()->error('Forum reply error: ' . $e->getMessage());
            $this->dispatch('show-alert', type: 'error', message: 'An error occurred while adding your reply.');
        }
    }

    #[On('reply-added')]
    public function handleNewReply()
    {
        // Force a refresh of the replies
        $this->render();
    }

    public function deleteReply($replyId)
    {
        try {
            $reply = $this->forum->replies()->findOrFail($replyId);

            if (!$this->canDeleteReply($reply)) {
                $this->dispatch('show-alert', type: 'error', message: 'You are not authorized to delete this reply.');
                return;
            }

            if ($reply->delete()) {
                $this->dispatch('show-alert', type: 'success', message: 'Reply deleted successfully.');
            } else {
                $this->dispatch('show-alert', type: 'error', message: 'Failed to delete reply.');
            }
        } catch (\Exception $e) {
            logger()->error('Forum reply deletion error: ' . $e->getMessage());
            $this->dispatch('show-alert', type: 'error', message: 'An error occurred while deleting the reply.');
        }
    }

    protected function canDeleteReply($reply)
    {
        $user = auth()->user();
        return $user && ($user->id === $reply->user_id || $user->usertype === 'admin');
    }

    public function render()
    {
        return view('livewire.forums.forum-view', [
            'replies' => $this->forum->replies()
                ->when($this->sortRepliesBy === 'latest', fn($query) => $query->latest())
                ->when($this->sortRepliesBy === 'oldest', fn($query) => $query->oldest())
                ->with('user')
                ->paginate(10)
        ]);
    }
}
