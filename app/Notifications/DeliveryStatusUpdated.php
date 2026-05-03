<?php

namespace App\Notifications;

use App\Models\Delivery;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DeliveryStatusUpdated extends Notification
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
        $statusLabels = [
            'on_delivery' => 'Sedang Diantar',
            'delivered' => 'Telah Sampai/Diterima',
            'failed' => 'Gagal Diantar',
        ];

        return [
            'type' => 'delivery_status_updated',
            'delivery_id' => $this->delivery->id,
            'title' => 'Update Pengantaran #' . $this->delivery->id,
            'message' => 'Status pengantaran ' . $this->delivery->patient->name . ' kini: ' . ($statusLabels[$this->delivery->status] ?? $this->delivery->status),
            'icon' => $this->delivery->status === 'delivered' ? 'fas fa-check-circle' : 'fas fa-info-circle',
            'color' => $this->delivery->status === 'delivered' ? 'green' : 'blue',
            'link' => route('deliveries.show', $this->delivery),
        ];
    }
}
