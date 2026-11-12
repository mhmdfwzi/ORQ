<?php

return [

    /*
    |--------------------------------------------------------------------------
    | ORQ storefront settings
    |--------------------------------------------------------------------------
    */
    // Max units of a single line item per order.
    'max_item_quantity' => 10,

    'order_prefix' => env('ORQ_ORDER_PREFIX', 'ORQ'),

];
