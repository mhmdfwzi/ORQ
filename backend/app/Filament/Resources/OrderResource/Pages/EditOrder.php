<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return DB::transaction(function () use ($record, $data) {
            $order = Order::query()->lockForUpdate()->findOrFail($record->getKey());
            if ($order->user_id !== null) {
                unset($data['user_id']);
            } elseif (filled($data['user_id'] ?? null) && ! User::whereKey($data['user_id'])
                ->where('role', 'customer')->where('status', 'active')->exists()) {
                throw ValidationException::withMessages(['data.user_id' => 'اختار حساب عميل نشط.']);
            }

            $order->fill(Arr::except($data, 'status'));
            if (isset($data['status']) && $order->status !== $data['status']) {
                $order->updateStatus($data['status']);
            } else {
                $order->save();
            }

            return $order;
        });
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
