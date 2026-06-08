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
            'pending' => 'Pending / Dibatalkan',
            'on_delivery' => 'Sedang Diantar',
            'delivered' => 'Telah Sampai/Diterima',
            'failed' => 'Gagal Diantar',
        ];

        $icons = [
            'pending' => 'fas fa-clock',
            'on_delivery' => 'fas fa-truck-fast',
            'delivered' => 'fas fa-check-circle',
            'failed' => 'fas fa-times-circle',
        ];

        $colors = [
            'pending' => 'orange',
            'on_delivery' => 'blue',
            'delivered' => 'green',
            'failed' => 'red',
        ];

        return [
            'type' => 'delivery_status_updated',
            'delivery_id' => $this->delivery->id,
            'title' => 'Update Pengantaran #' . $this->delivery->id,
            'message' => 'Status pengantaran ' . $this->delivery->patient->name . ' kini: ' . ($statusLabels[$this->delivery->status] ?? $this->delivery->status),
            'icon' => $icons[$this->delivery->status] ?? 'fas fa-info-circle',
            'color' => $colors[$this->delivery->status] ?? 'blue',
            'link' => route('deliveries.show', $this->delivery),
        ];
    }
}
