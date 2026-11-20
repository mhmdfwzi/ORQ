<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ContactApiController extends Controller
{
    public function store(Request $request): Response
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:254'],
            'message' => ['required', 'string', 'min:10', 'max:2000'],
        ]);

        $admins = User::where('role', 'admin')->get();
        abort_if($admins->isEmpty(), 503, 'تعذر استقبال الرسالة حالياً. حاول مرة أخرى لاحقاً.');

        $sender = e($data['name']) . ' · ' . e($data['email']);
        $message = nl2br(e($data['message']));
        $notification = Notification::make()
            ->title('رسالة جديدة من صفحة تواصل معنا')
            ->body($sender . '<br><br>' . $message)
            ->icon('heroicon-o-envelope')
            ->toDatabase();

        DB::transaction(function () use ($admins, $notification) {
            foreach ($admins as $admin) {
                $admin->notifyNow($notification);
            }
        });

        return response()->noContent();
    }
}
