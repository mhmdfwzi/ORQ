<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset - Medicamo</title>
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
                                <div style="width:70px;height:70px;background:#fff3e0;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:32px;">
                                    🔑
                                </div>
                            </div>

                            <h2 style="color:#1a1a2e;text-align:center;margin:0 0 10px;font-size:22px;">Password Reset Code</h2>
                            <p style="color:#666;text-align:center;margin:0 0 30px;font-size:15px;">You requested to reset your password. Use the code below:</p>

                            {{-- OTP Code --}}
                            <div style="background:#fff8e1;border:2px dashed #ff9800;border-radius:12px;padding:25px;text-align:center;margin:0 0 30px;">
                                <p style="color:#666;margin:0 0 10px;font-size:13px;text-transform:uppercase;letter-spacing:2px;">Reset Code</p>
                                <h1 style="color:#e65100;margin:0;font-size:42px;letter-spacing:12px;font-weight:800;">{{ $otp }}</h1>
                            </div>

                            <p style="color:#888;text-align:center;font-size:14px;margin:0 0 5px;">⏱ This code expires in <strong>10 minutes</strong></p>
                            <p style="color:#d32f2f;text-align:center;font-size:13px;margin:10px 0 0;font-weight:600;">⚠️ If you didn't request this, someone may be trying to access your account.</p>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background:#f8f9fa;padding:20px 30px;text-align:center;border-top:1px solid #eee;">
                            <p style="color:#999;margin:0;font-size:12px;">© {{ date('Y') }} Medicamo. All rights reserved.</p>
                            <p style="color:#bbb;margin:8px 0 0;font-size:11px;">If you didn't request a password reset, please ignore this email.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
