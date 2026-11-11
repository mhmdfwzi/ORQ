<?php

return [

    /*
    |--------------------------------------------------------------------------
    | ORQ storefront settings
    |--------------------------------------------------------------------------
    */

    // Flat shipping fee in EGP applied to every order (0 = free shipping).
    'shipping_cost' => (float) env('ORQ_SHIPPING_COST', 0),

    // Max units of a single line item per order.
    'max_item_quantity' => 10,

    'order_prefix' => env('ORQ_ORDER_PREFIX', 'ORQ'),

];
