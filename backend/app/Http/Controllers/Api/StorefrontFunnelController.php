<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StorefrontFunnelController
{
    public const STAGES = ['visit', 'cart', 'checkout'];

    public function __invoke(Request $request): Response
    {
        $data = $request->validate([
            'session_id' => ['required', 'uuid'],
            'stage' => ['required', Rule::in(self::STAGES)],
        ]);

        DB::table('storefront_funnel_events')->insertOrIgnore([
            'session_id' => strtolower($data['session_id']),
            'stage' => $data['stage'],
            'day' => now('Africa/Cairo')->toDateString(),
        ]);

        return response()->noContent();
    }
}
