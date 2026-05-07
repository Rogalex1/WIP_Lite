<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Employee; 

class EmployeeCreatedNotification extends Notification
{
    use Queueable;
    // 1. Déclarer la propriété publique ou protégée
    public $employee;

    // 2. L'injecter via le constructeur
    public function __construct(Employee $employee)
    {
        $this->employee = $employee;
    }


public function via($notifiable) {
    return ['database']; 
}

public function toDatabase($notifiable)
{
    return [
        'employee_id' => $this->employee->id,
        'message' => 'Un nouvel employé a été créé.',
        'created_by' => auth()->user()->name,
    ];
}

public function toArray($notifiable) {
    return [
        'message' => "Un nouvel employé a été ajouté au système.",
        'type' => 'creation',
        'icon' => 'pi pi-user-plus'
    ];
}

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
}
