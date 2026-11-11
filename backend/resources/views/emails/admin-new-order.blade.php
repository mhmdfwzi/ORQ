<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Order - Medicamo Admin</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f7fa;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f7fa;padding:40px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.08);">
                    {{-- Header --}}
                    <tr>
                        <td style="background:linear-gradient(135deg,#d32f2f,#ff5722);padding:30px;text-align:center;">
                            <h1 style="color:#ffffff;margin:0;font-size:28px;font-weight:700;">🛒 New Order Received!</h1>
                            <p style="color:rgba(255,255,255,0.9);margin:8px 0 0;font-size:14px;">Medicamo Admin Notification</p>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding:30px;">
                            {{-- Order Info --}}
                            <div style="background:#f8f9fa;border-radius:12px;padding:20px;margin:0 0 20px;">
                                <h3 style="color:#1a1a2e;margin:0 0 15px;font-size:16px;">📦 Order Details</h3>
                                <table width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="padding:6px 0;color:#666;font-size:14px;">Order Number</td>
                                        <td style="padding:6px 0;color:#1a1a2e;font-size:14px;font-weight:700;text-align:right;">#{{ $order->order_number }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:6px 0;color:#666;font-size:14px;">Total</td>
                                        <td style="padding:6px 0;color:#d32f2f;font-size:18px;font-weight:700;text-align:right;">EGP {{ number_format($order->total, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:6px 0;color:#666;font-size:14px;">Payment</td>
                                        <td style="padding:6px 0;color:#1a1a2e;font-size:14px;font-weight:600;text-align:right;">{{ strtoupper($order->payment_method) }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:6px 0;color:#666;font-size:14px;">Date</td>
                                        <td style="padding:6px 0;color:#1a1a2e;font-size:14px;text-align:right;">{{ $order->created_at->format('Y-m-d H:i') }}</td>
                                    </tr>
                                </table>
                            </div>

                            {{-- Customer Info --}}
                            <div style="background:#e3f2fd;border-radius:12px;padding:20px;margin:0 0 20px;">
                                <h3 style="color:#1a1a2e;margin:0 0 15px;font-size:16px;">👤 Customer Info</h3>
                                <table width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="padding:6px 0;color:#666;font-size:14px;">Name</td>
                                        <td style="padding:6px 0;color:#1a1a2e;font-size:14px;font-weight:600;text-align:right;">{{ $order->user->name }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:6px 0;color:#666;font-size:14px;">Phone</td>
                                        <td style="padding:6px 0;color:#1a1a2e;font-size:14px;text-align:right;">{{ $order->user->phone ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:6px 0;color:#666;font-size:14px;">Email</td>
                                        <td style="padding:6px 0;color:#1a1a2e;font-size:14px;text-align:right;">{{ $order->user->email ?? 'N/A' }}</td>
                                    </tr>
                                    @if($order->address)
                                    <tr>
                                        <td style="padding:6px 0;color:#666;font-size:14px;">Address</td>
                                        <td style="padding:6px 0;color:#1a1a2e;font-size:14px;text-align:right;">{{ $order->address->full_address ?? '' }}</td>
                                    </tr>
                                    @endif
                                </table>
                            </div>

                            {{-- Order Items --}}
                            @if($order->items && $order->items->count())
                            <div style="background:#fff8e1;border-radius:12px;padding:20px;">
                                <h3 style="color:#1a1a2e;margin:0 0 15px;font-size:16px;">📋 Items ({{ $order->items->count() }})</h3>
                                <table width="100%" cellpadding="0" cellspacing="0">
                                    @foreach($order->items as $item)
                                    <tr style="{{ !$loop->last ? 'border-bottom:1px solid #f0e6c0;' : '' }}">
                                        <td style="padding:8px 0;color:#333;font-size:13px;">
                                            {{ $item->product_name }} × {{ $item->quantity }}
                                        </td>
                                        <td style="padding:8px 0;color:#1a1a2e;font-size:13px;font-weight:600;text-align:right;">
                                            EGP {{ number_format($item->total_price, 2) }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </table>
                            </div>
                            @endif
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background:#f8f9fa;padding:15px 30px;text-align:center;border-top:1px solid #eee;">
                            <p style="color:#999;margin:0;font-size:12px;">© {{ date('Y') }} Medicamo Admin Panel</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
