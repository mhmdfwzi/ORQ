<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0; padding:0; background:#f4f7fa; font-family: Arial, sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f7fa; padding:30px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background:#fff; border-radius:12px; overflow:hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #0d6efd, #198754); padding:25px; text-align:center;">
                            <h1 style="color:#fff; margin:0; font-size:22px;">📋 طلب توظيف جديد</h1>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding:30px;">
                            <!-- Job Title -->
                            <div style="background:#f0f7ff; border-radius:8px; padding:15px; margin-bottom:20px; border-right:4px solid #0d6efd;">
                                <strong style="color:#0d6efd;">الوظيفة:</strong>
                                <span style="font-size:16px; color:#333;">{{ $application->job->title }}</span>
                            </div>

                            <!-- Personal Info -->
                            <h3 style="color:#0d6efd; border-bottom:2px solid #f0f0f0; padding-bottom:8px;">👤 البيانات الشخصية</h3>
                            <table width="100%" style="margin-bottom:20px; font-size:14px;">
                                <tr><td style="padding:6px 0; color:#666; width:130px;"><strong>الاسم:</strong></td><td style="padding:6px 0; color:#333;">{{ $application->full_name }}</td></tr>
                                <tr><td style="padding:6px 0; color:#666;"><strong>الإيميل:</strong></td><td style="padding:6px 0; color:#333;">{{ $application->email }}</td></tr>
                                <tr><td style="padding:6px 0; color:#666;"><strong>التليفون:</strong></td><td style="padding:6px 0; color:#333;">{{ $application->phone }}</td></tr>
                                @if($application->city)<tr><td style="padding:6px 0; color:#666;"><strong>المدينة:</strong></td><td style="padding:6px 0; color:#333;">{{ $application->city }}</td></tr>@endif
                                @if($application->gender)<tr><td style="padding:6px 0; color:#666;"><strong>النوع:</strong></td><td style="padding:6px 0; color:#333;">{{ $application->gender == 'male' ? 'ذكر' : 'أنثى' }}</td></tr>@endif
                            </table>

                            <!-- Education -->
                            @if($application->education_level || $application->university || $application->major)
                            <h3 style="color:#0d6efd; border-bottom:2px solid #f0f0f0; padding-bottom:8px;">🎓 التعليم</h3>
                            <table width="100%" style="margin-bottom:20px; font-size:14px;">
                                @if($application->education_level)<tr><td style="padding:6px 0; color:#666; width:130px;"><strong>المؤهل:</strong></td><td style="padding:6px 0; color:#333;">{{ $application->education_level }}</td></tr>@endif
                                @if($application->university)<tr><td style="padding:6px 0; color:#666;"><strong>الجامعة:</strong></td><td style="padding:6px 0; color:#333;">{{ $application->university }}</td></tr>@endif
                                @if($application->major)<tr><td style="padding:6px 0; color:#666;"><strong>التخصص:</strong></td><td style="padding:6px 0; color:#333;">{{ $application->major }}</td></tr>@endif
                                @if($application->graduation_year)<tr><td style="padding:6px 0; color:#666;"><strong>سنة التخرج:</strong></td><td style="padding:6px 0; color:#333;">{{ $application->graduation_year }}</td></tr>@endif
                            </table>
                            @endif

                            <!-- Experience -->
                            @if($application->years_of_experience || $application->current_job)
                            <h3 style="color:#0d6efd; border-bottom:2px solid #f0f0f0; padding-bottom:8px;">💼 الخبرة</h3>
                            <table width="100%" style="margin-bottom:20px; font-size:14px;">
                                @if($application->years_of_experience !== null)<tr><td style="padding:6px 0; color:#666; width:130px;"><strong>سنوات الخبرة:</strong></td><td style="padding:6px 0; color:#333;">{{ $application->years_of_experience }} سنة</td></tr>@endif
                                @if($application->current_job)<tr><td style="padding:6px 0; color:#666;"><strong>الوظيفة الحالية:</strong></td><td style="padding:6px 0; color:#333;">{{ $application->current_job }}</td></tr>@endif
                            </table>
                            @endif

                            @if($application->skills)
                            <h3 style="color:#0d6efd; border-bottom:2px solid #f0f0f0; padding-bottom:8px;">⚡ المهارات</h3>
                            <p style="font-size:14px; color:#333; line-height:1.8; background:#f8f8f8; padding:12px; border-radius:8px;">{{ $application->skills }}</p>
                            @endif

                            <!-- Action Button -->
                            <div style="text-align:center; margin-top:25px;">
                                <a href="{{ url('/admin/job-applications/' . $application->id) }}" style="background: linear-gradient(135deg, #0d6efd, #198754); color:#fff; padding:12px 30px; border-radius:25px; text-decoration:none; font-weight:bold; font-size:14px;">
                                    عرض الطلب في لوحة التحكم
                                </a>
                            </div>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background:#f8f9fa; padding:15px; text-align:center; font-size:12px; color:#999;">
                            Medicamo Drug — نظام التوظيف
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
