<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreOrderRequest;
use App\Filament\Resources\OrderResource;
use App\Models\Order;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use App\Services\OrderService;
use App\Services\WhatsAppService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

/**
 * Guest checkout endpoint for the Next.js storefront.
 */
class OrderApiController extends Controller
{
    public function __construct(private readonly OrderService $orders)
    {
    }

    public function store(StoreOrderRequest $request): JsonResponse
    {
        $order = $this->orders->placeGuestOrder($request->validated());

        // Notifications must never fail the order itself.
        $this->notifyAdmins($order);

        try {
            app(WhatsAppService::class)->notifyAdminNewOrder($order);
        } catch (\Throwable $e) {
            Log::error('Guest order notification failed: ' . $e->getMessage(), ['order' => $order->order_number]);
        }

        return response()->json([
            'data' => [
                'orderNumber'   => $order->order_number,
                'subtotal'      => (float) $order->subtotal,
                'shippingCost'  => (float) $order->shipping_cost,
                'total'         => (float) $order->total,
                'paymentMethod' => $order->payment_method,
                'status'        => $order->status,
            ],
        ], 201);
    }

    /**
     * In-panel (Filament bell) notification: independent of any external
     * provider, so a new order is never missed if WhatsApp/SMTP is down.
     */
    private function notifyAdmins(Order $order): void
    {
        try {
            $admins = User::where('role', 'admin')->get();

            // notifyNow: delivered synchronously, no queue worker required.
            $notification = Notification::make()
                ->title('طلب جديد ' . $order->order_number)
                ->body("{$order->customer_display_name} — {$order->customer_display_phone} — {$order->total} ج.م")
                ->icon('heroicon-o-shopping-bag')
                ->iconColor('warning')
                ->actions([
                    Action::make('view')
                        ->label('عرض الطلب')
                        ->url(OrderResource::getUrl('edit', ['record' => $order])),
                ])
                ->toDatabase();

            foreach ($admins as $admin) {
                $admin->notifyNow($notification);
            }
        } catch (\Throwable $e) {
            Log::error('In-panel order notification failed: ' . $e->getMessage(), ['order' => $order->order_number]);
        }
    }
}
