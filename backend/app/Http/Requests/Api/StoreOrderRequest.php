<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prices are intentionally NOT accepted: they are always resolved server-side.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name'            => ['required', 'string', 'min:3', 'max:100'],
            // Egyptian mobile: 01[0125]xxxxxxxx (also accepts +20 / 0020 prefix)
            'phone'           => ['required', 'string', 'regex:/^(?:\+?20|0020|0)?1[0125][0-9]{8}$/'],
            'governorate'     => ['required', 'string', 'max:100'],
            'address'         => ['required', 'string', 'min:8', 'max:500'],
            'notes'           => ['nullable', 'string', 'max:500'],
            'items'           => ['required', 'array', 'min:1', 'max:20'],
            'items.*.slug'    => ['required', 'string', 'max:255'],
            'items.*.size'    => ['required', 'string', 'in:50ml,100ml'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:' . config('orq.max_item_quantity', 10)],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.regex' => 'رقم الموبايل غير صحيح. مثال: 01012345678',
        ];
    }
}
