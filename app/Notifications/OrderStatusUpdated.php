<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Enums\OrderStatus as OrderStatusEnum;

class OrderStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    protected $order;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($order)
    {
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $statusName = $this->getStatusName($this->order->status);
        return [
            'message' => 'Your order #' . $this->order->id . ' has been updated to ' . $statusName . '.',
            'order_id' => $this->order->id,
            'status' => $statusName,
        ];
    }

    /**
     * Get the status name from status code.
     *
     * @param int $status
     * @return string
     */
    protected function getStatusName($status)
    {
        switch ($status) {
            case OrderStatusEnum::PENDING:
                return 'Pending';
            case OrderStatusEnum::ACCEPT:
                return 'Accepted';
            case OrderStatusEnum::CONFIRMED:
                return 'Confirmed';
            case OrderStatusEnum::PREPARING:
                return 'Preparing';
            case OrderStatusEnum::PREPARED:
                return 'Prepared';
            case OrderStatusEnum::OUT_FOR_DELIVERY:
                return 'Out for Delivery';
            case OrderStatusEnum::DELIVERED:
                return 'Delivered';
            case OrderStatusEnum::CANCELED:
                return 'Canceled';
            case OrderStatusEnum::REJECTED:
                return 'Rejected';
            case OrderStatusEnum::RETURNED:
                return 'Returned';
            default:
                return 'Unknown';
        }
    }
}
