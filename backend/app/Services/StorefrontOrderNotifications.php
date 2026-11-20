<?php

namespace App\Services;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class StorefrontOrderNotifications
{
    public function __construct(private readonly WhatsAppOrderConfirmation $confirmation) {}

    public function send(Order $order, bool $paymentNeedsReview = false): void
    {
        if (!$paymentNeedsReview && $order->whatsapp_confirmation_requested && $this->confirmation->isConfigured()) {
            defer(fn () => $this->confirmation->send($order));
        }

        try {
            $title = $paymentNeedsReview ? 'دفعة Paymob تحتاج مراجعة للطلب ' : 'طلب جديد ';
            $body = $paymentNeedsReview
                ? "راجع إمكان تنفيذ الطلب أو رد المبلغ. معرّف العملية {$order->paymob_transaction_id}."
                : e($order->customer_display_name) . ' · ' . $order->customer_display_phone . ' · ' . $order->total . ' ج.م';

            $notification = Notification::make()
                ->title($title . $order->order_number)
                ->body($body)
                ->icon('heroicon-o-shopping-bag')
                ->iconColor('warning');

            if (!$order->trashed()) {
                $notification->actions([
                    Action::make('view')
                        ->label('عرض الطلب')
                        ->url(OrderResource::getUrl('edit', ['record' => $order])),
                ]);
            }

            $databaseNotification = $notification->toDatabase();
            foreach (User::where('role', 'admin')->get() as $admin) {
                $admin->notifyNow($databaseNotification);
            }
        } catch (\Throwable $e) {
            Log::error('In-panel order notification failed: ' . $e->getMessage(), ['order' => $order->order_number]);
        }

        if (!$paymentNeedsReview) {
            defer(function () use ($order) {
                try {
                    app(WhatsAppService::class)->notifyAdminNewOrder($order);
                } catch (\Throwable $e) {
                    Log::error('Guest order notification failed: ' . $e->getMessage(), ['order' => $order->order_number]);
                }
            });
        }
    }
}
