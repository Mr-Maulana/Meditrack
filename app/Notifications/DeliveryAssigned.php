<?php

namespace App\Notifications;

use App\Models\Delivery;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DeliveryAssigned extends Notification
{
    use Queueable;

    protected $delivery;

    public function __construct(Delivery $delivery)
    {
        $this->delivery = $delivery;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'delivery_assigned',
            'delivery_id' => $this->delivery->id,
            'title' => 'Tugas Pengantaran Baru',
            'message' => 'Anda ditugaskan mengantar obat ke ' . $this->delivery->patient->name,
            'icon' => 'fas fa-truck-fast',
            'color' => 'blue',
            'link' => route('delivery-process.index'),
        ];
    }
}
