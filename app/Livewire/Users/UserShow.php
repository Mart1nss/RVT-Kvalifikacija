<?php

namespace App\Livewire\Users;

use App\Models\User;
use App\Models\Ban;
use App\Models\Ticket;
use App\Services\AuditLogService;
use Illuminate\Support\Facades\Notification;
use App\Notifications\TicketUnassignedNotification;
use App\Notifications\UserRoleChangedNotification;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

/**
 * Lietotāja profila skatīšanas komponente
 * 
 * Šī Livewire komponente nodrošina atsevišķa lietotāja detaļu attēlošanu un pārvaldību.
 */
class UserShow extends Component
{
    public $user;
    public $userId;
    public $showDeleteModal = false;
    public $showBanModal = false;
    public $banReason = '';

    /**
     * Inicializē komponenti
     * Šī metode tiek izsaukta, kad komponente tiek pirmoreiz ielādēta
     * 
     * @param int
     */
    public function mount($userId)
    {
        $this->userId = $userId;
        
        // Novērš administratoru iespēju rediģēt pašiem savu kontu
        if (auth()->id() == $userId) {
            $this->dispatch('alert', [
                [
                    'type' => 'error',
                    'message' => 'You cannot edit your own account for security reasons.'
                ]
            ]);
            
            return redirect()->route('user.management.livewire');
        }
        
        $this->loadUser();
    }

    /**
     * Ielādē lietotāja datus no datu bāzes
     * Šī metode iegūst lietotāju pēc ID vai parāda 404 kļūdu, ja netiek atrasts
     */
    public function loadUser()
    {
        $this->user = User::findOrFail($this->userId);
    }

    /**
     * Renderē komponentes skatu
     */
    public function render()
    {
        return view('livewire.users.user-show');
    }

    /**
     * Livewire dzīves cikla āķis, kas darbojas, kad `showBanModal` īpašība tiek atjaunināta
     * Nosūta pārlūkprogrammas notikumu, kad modālais logs tiek atvērts
     */
    public function updatedShowBanModal($value)
    {
        if ($value === true) {
            $this->dispatchBrowserEvent('banModalOpened');
        }
    }

    /**
     * Atjaunina lietotāja lomu (administrators/lietotājs)
     * 
     * @param string
     */
    public function updateUserType($userType)
    {
        if ($this->user->id === auth()->id()) {
            $this->dispatch('alert', [
                [
                    'type' => 'error',
                    'message' => 'You cannot change your own role for security reasons.'
                ]
            ]);
            return;
        }
        
        $oldRole = $this->user->usertype;
        
        $this->user->usertype = $userType;
        $this->user->save();

        // Ja lietotājs bija administrators un vairs nav administrators, noņem viņa piešķirtos pieteikumus
        if ($oldRole === 'admin' && $userType !== 'admin') {
            $adminUser = $this->user;
            $ticketsToUnassign = Ticket::where('assigned_admin_id', $adminUser->id)->get();
            foreach ($ticketsToUnassign as $ticket) {
                $ticket->assigned_admin_id = null;
                $ticket->status = Ticket::STATUS_OPEN;
                $ticket->save();
            }

            // Paziņo citiem administratoriem
            $otherAdmins = User::where('usertype', 'admin')->where('id', '!=', $adminUser->id)->get();
            if ($otherAdmins->isNotEmpty()) {
                event(new \App\Events\AdminTicketsUnassigned($adminUser, count($ticketsToUnassign), 'role_changed'));
                $this->dispatch('alert', [
                    [
                        'type' => 'info',
                        'message' => "Tickets previously assigned to {$adminUser->name} have been unassigned due to role change."
                    ]
                ]);
            }
        }

        // Paziņo lietotājam, kura loma tika mainīta
        if ($oldRole !== $userType) { 
            $this->user->notify(new UserRoleChangedNotification($this->user, $oldRole, $userType));
        }

        // Reģistrē lomas maiņu audita žurnālā pārskatatbildības nolūkos
        AuditLogService::log(
            "Changed user role for",
            "user",
            "Changed {$this->user->name}'s role from '{$oldRole}' to '{$userType}'",
            $this->user->id,
            $this->user->name
        );

        $this->dispatch('alert', [
            [
                'type' => 'success',
                'message' => "User type for {$this->user->name} updated successfully."
            ]
        ]);
    }

    /**
     * Parāda dzēšanas apstiprinājuma modālo logu
     * Šī metode tiek izsaukta, kad administrators noklikšķina uz pogas "Dzēst lietotāju"
     */
    public function confirmDelete()
    {
        if ($this->user->id === auth()->id()) {
            $this->dispatch('alert', [
                [
                    'type' => 'error',
                    'message' => 'You cannot delete your own account for security reasons.'
                ]
            ]);
            return;
        }
        
        $this->showDeleteModal = true;
    }

    /**
     * Atceļ dzēšanas procesu un aizver modālo logu
     * Šī metode tiek izsaukta, kad administrators noklikšķina uz "Atcelt" dzēšanas modālajā logā
     */
    public function cancelDelete()
    {
        $this->showDeleteModal = false;
    }

    /**
     * Dzēš lietotāju no datu bāzes
     * Šī metode tiek izsaukta, kad administrators apstiprina dzēšanu modālajā logā
     */
    public function deleteUser()
    {
        if (!auth()->user()->isAdmin()) {
            $this->dispatch('alert', [
                [
                    'type' => 'error',
                    'message' => 'Unauthorized action.'
                ]
            ]);
            return;
        }

        if ($this->user->id === auth()->id()) {
            $this->dispatch('alert', [
                [
                    'type' => 'error',
                    'message' => 'You cannot delete your own account for security reasons.'
                ]
            ]);
            return;
        }

        AuditLogService::log(
            "Deleted user",
            "user",
            "Deleted user account",
            $this->user->id,
            $this->user->name
        );

        $userName = $this->user->name;
        $this->user->delete();
        
        $this->showDeleteModal = false;

        // Ievieto veiksmīgu ziņojumu sesijā
        session()->flash('message', "User {$userName} deleted successfully.");

        return redirect()->route('user.management.livewire');
    }

    /**
     * Parāda bloķēšanas apstiprinājuma modālo logu
     */
    public function confirmBan()
    {
        if ($this->user->id === auth()->id()) {
            $this->dispatch('alert', [
                [
                    'type' => 'error',
                    'message' => 'You cannot ban your own account for security reasons.'
                ]
            ]);
            return;
        }
        
        $this->showBanModal = true;
    }

    /**
     * Atceļ bloķēšanas procesu un aizver modālo logu
     */
    public function cancelBan()
    {
        $this->showBanModal = false;
        $this->banReason = '';
    }

    /**
     * Bloķē lietotāju
     */
    public function banUser()
    {
        if (!auth()->user()->isAdmin()) {
            $this->dispatch('alert', [
                [
                    'type' => 'error',
                    'message' => 'Unauthorized action.'
                ]
            ]);
            return;
        }

        if ($this->user->id === auth()->id()) {
            $this->dispatch('alert', [
                [
                    'type' => 'error',
                    'message' => 'You cannot ban your own account for security reasons.'
                ]
            ]);
            return;
        }
        
        if (empty($this->banReason)) {
            $this->addError('banReason', 'A reason for the ban is required.');
            return;
        }

        Ban::create([
            'user_id' => $this->user->id,
            'reason' => $this->banReason,
            'banned_by' => auth()->id(),
            'is_active' => true
        ]);

        AuditLogService::log(
            "Banned user",
            "user",
            "Banned user account for: {$this->banReason}",
            $this->user->id,
            $this->user->name
        );

        $this->showBanModal = false;
        $this->banReason = '';

        $this->dispatch('alert', [
            [
                'type' => 'success',
                'message' => "User {$this->user->name} has been banned."
            ]
        ]);
        
        $this->loadUser();
    }

    /**
     * Atbloķē lietotāju
     */
    public function unbanUser()
    {
        if (!auth()->user()->isAdmin()) {
            $this->dispatch('alert', [
                [
                    'type' => 'error',
                    'message' => 'Unauthorized action.'
                ]
            ]);
            return;
        }

        Ban::where('user_id', $this->user->id)->delete();

        AuditLogService::log(
            "Unbanned user",
            "user",
            "Unbanned user account",
            $this->user->id,
            $this->user->name
        );

        $this->dispatch('alert', [
            [
                'type' => 'success',
                'message' => "User {$this->user->name} has been unbanned."
            ]
        ]);
        
        $this->loadUser();
    }
}
