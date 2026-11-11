<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmed - Medicamo</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f7fa;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f7fa;padding:40px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.08);">
                    {{-- Header --}}
                    <tr>
                        <td style="background:linear-gradient(135deg,#0d6efd,#00b4d8);padding:30px;text-align:center;">
                            <h1 style="color:#ffffff;margin:0;font-size:28px;font-weight:700;">Medicamo</h1>
                            <p style="color:rgba(255,255,255,0.9);margin:8px 0 0;font-size:14px;">Smart Medical Solutions</p>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding:40px 30px;">
                            <div style="text-align:center;margin-bottom:30px;">
                                <div style="width:70px;height:70px;background:#e8f5e9;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:32px;">
                                    ✅
                                </div>
                            </div>

                            <h2 style="color:#1a1a2e;text-align:center;margin:0 0 10px;font-size:22px;">Order Confirmed!</h2>
                            <p style="color:#666;text-align:center;margin:0 0 30px;font-size:15px;">Thank you for your order, <strong>{{ $order->user->name }}</strong>!</p>

                            {{-- Order Summary --}}
                            <div style="background:#f8f9fa;border-radius:12px;padding:20px;margin:0 0 25px;">
                                <table width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="padding:8px 0;color:#666;font-size:14px;">Order Number</td>
                                        <td style="padding:8px 0;color:#1a1a2e;font-size:14px;font-weight:600;text-align:right;">#{{ $order->order_number }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:8px 0;color:#666;font-size:14px;border-top:1px solid #eee;">Total</td>
                                        <td style="padding:8px 0;color:#0d6efd;font-size:18px;font-weight:700;text-align:right;border-top:1px solid #eee;">EGP {{ number_format($order->total, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:8px 0;color:#666;font-size:14px;border-top:1px solid #eee;">Payment</td>
                                        <td style="padding:8px 0;color:#1a1a2e;font-size:14px;font-weight:600;text-align:right;border-top:1px solid #eee;">{{ strtoupper($order->payment_method) }}</td>
                                    </tr>
                                </table>
                            </div>

                            {{-- Order Items --}}
                            @if($order->items && $order->items->count())
                            <h3 style="color:#1a1a2e;margin:0 0 15px;font-size:16px;">Order Items</h3>
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 25px;">
                                @foreach($order->items as $item)
                                <tr style="border-bottom:1px solid #f0f0f0;">
                                    <td style="padding:12px 0;color:#333;font-size:14px;">
                                        {{ $item->product_name }}
                                        <span style="color:#999;font-size:12px;">× {{ $item->quantity }}</span>
                                    </td>
                                    <td style="padding:12px 0;color:#1a1a2e;font-size:14px;font-weight:600;text-align:right;">
                                        EGP {{ number_format($item->total_price, 2) }}
                                    </td>
                                </tr>
                                @endforeach
                            </table>
                            @endif

                            <p style="color:#666;text-align:center;font-size:14px;margin:0;">We'll notify you when your order is shipped 🚚</p>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background:#f8f9fa;padding:20px 30px;text-align:center;border-top:1px solid #eee;">
                            <p style="color:#2e7d32;margin:0 0 8px;font-size:14px;font-weight:600;">Thank you for choosing Medicamo! 💚</p>
                            <p style="color:#999;margin:0;font-size:12px;">© {{ date('Y') }} Medicamo. All rights reserved.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
