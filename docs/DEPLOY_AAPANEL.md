# نشر ORQ على سيرفر aaPanel (Ubuntu 22)

> الموقع والمتجر ولوحة الإدارة والـ API على `orqperfume.com`.
> لو ORQ شغّال بالفعل، التحديث يستخدم عملية PM2 باسم `orq` وإعداداتها الحالية. في أول نشر فقط، اختار بورت داخلي فاضي مخصص لـ ORQ.

## 1) من لوحة aaPanel (مرة واحدة)

**App Store** ثبّت: Nginx، MySQL 8، **PHP 8.3 أو 8.4**، و**PM2 Manager** (ومعاه Node.js 20+).

**PHP ← Settings**
- *Install extensions*: `fileinfo` و`intl` و`exif` و`opcache`.
- *Disabled functions*: امسح من القائمة `putenv` و`proc_open` و`proc_get_status` و`symlink` و`exec` (لازمين لـ Composer وLaravel).

**Database ← Add**: قاعدة `orq` + مستخدم `orq_user` بباسورد قوي (استخدم Utf8mb4).

**Website ← Add site** (للموقع): الدومين `orqperfume.com` و`www.orqperfume.com` (PHP: Static) ثم:
- *Reverse proxy* ← Add: الهدف `http://127.0.0.1:<بورت ORQ>` (نفس `FRONTEND_PORT` عند إنشاء العملية).
- اقفل **Cache** لبروكسي ORQ فقط علشان الزوار يشوفوا الـ HTML الجديد بعد النشر. لو بتعدّل إعداد Nginx مباشرة، استخدم `proxy_cache off;` في الـ `location` اللي بيخدم واجهة ORQ.
- *SSL* ← Let's Encrypt ← Force HTTPS.
- في **Config** أضف محتوى [`scripts/nginx/orq-laravel.conf`](../scripts/nginx/orq-laravel.conf) داخل `server { ... }`، مكان قواعد التحويل القديمة لـ`/admin` إن وجدت. الملف بيوجّه الإدارة والـAPI والملفات اللازمة إلى Laravel وPHP 8.4. استخدم مسار الـsocket اللي بيعرضه إعداد PHP-FPM عندك.
- بعد حفظ الإعداد، جرّب `https://orqperfume.com/admin/login` و`https://orqperfume.com/api/v1/site` قبل إعادة بناء الواجهة.

## 2) على السيرفر (SSH كـ root)

```bash
cd /www/wwwroot
git clone https://github.com/mhmdfwzi/ORQ.git orq
cd orq

# أدوات (لو مش موجودة)
curl -sS https://getcomposer.org/installer | /www/server/php/84/bin/php -- --install-dir=/usr/local/bin --filename=composer   # عدّل 84 حسب نسختك

# ملفات البيئة
cp backend/.env.production.example backend/.env
SECRET=$(openssl rand -hex 32); echo "REVALIDATE_SECRET=$SECRET"   # انسخه للملفين
nano backend/.env          # APP_URL, DB_PASSWORD, FRONTEND_URL, REVALIDATE_SECRET
/www/server/php/84/bin/php backend/artisan key:generate --force

cp frontend/.env.example frontend/.env.production
nano frontend/.env.production
#   API_URL=https://orqperfume.com/api/v1
#   NEXT_PUBLIC_API_URL=/api/v1
#   REVALIDATE_SECRET=<نفس القيمة>
#   NEXT_PUBLIC_SITE_URL=https://orqperfume.com
```

> اضبط `APP_URL` و`FRONTEND_URL` في `backend/.env` على `https://orqperfume.com`، و`SESSION_DOMAIN=null` لجلسة على نفس المضيف. لو `ASSET_URL` متعيّن، اضبطه على `https://orqperfume.com`. استخدم رابط HTTPS كامل في `API_URL` على السيرفر، ومسار `/api/v1` لطلبات المتصفح.

## 3) أول نشر

```bash
cd /www/wwwroot/orq
read -r -p 'ORQ frontend port: ' FRONTEND_PORT
PHP_BIN=/www/server/php/84/bin/php FRONTEND_PORT="$FRONTEND_PORT" bash scripts/deploy.sh --seed
```

بعده أنشئ حساب الأدمن (غيّر الإيميل والباسورد):

```bash
cd backend && /www/server/php/84/bin/php artisan tinker --execute="App\Models\User::create(['name'=>'ORQ Admin','email'=>'you@example.com','password'=>bcrypt('CHANGE-ME-STRONG'),'role'=>'admin','email_verified_at'=>now()]);"
```

ادخل `https://orqperfume.com/admin`، وراجع **Settings ← إعدادات الموقع** و**قصة البراند**.

لتشغيل PM2 بعد إعادة تشغيل السيرفر: `pm2 startup` ثم `pm2 save`.

## 4) كل تحديث بعد كده

```bash
cd /www/wwwroot/orq && PHP_BIN=/www/server/php/84/bin/php bash scripts/deploy.sh
```

لو التحديث للواجهة فقط، شغّل `bash scripts/deploy.sh --frontend-only`. السكريبت يتأكد إن الصفحة الرئيسية على الدومين العام بتعرض الإصدار الجديد بطلب عادي وطلب يدعم ضغط المتصفح.

بعد تعطيل كاش البروكسي، تقدر تتحقق من نسخة المتصفح من غير إعادة نشر:

```bash
curl -fsS --compressed -H 'Accept-Encoding: gzip, deflate, br, zstd' \
  https://orqperfume.com/ | grep -o 'data-dpl-id="[^"]*"'
```

رقم الإصدار لازم يطابق اللي ظهر في سطر `✔ Web` في آخر نشر.

> **لا** تشغّل `--seed` على الموقع الحي بعد ما تعدّل المنتجات: بيرجّع الأسعار والصور للقيم الأصلية.

## تأكيد الطلب للعميل على واتساب

1. اربط رقم ORQ بـ [WhatsApp Cloud API](https://developers.facebook.com/documentation/business-messaging/whatsapp/overview). استخدم **Phone Number ID** للرقم وتوكن مستخدم النظام بصلاحية `whatsapp_business_messaging`.
2. في [WhatsApp Manager](https://business.facebook.com/latest/whatsapp_manager/message_templates)، اعمل قالب باسم `orq_order_received` من فئة **Utility** باللغة العربية (`ar`). استخدم متغيرين بالترتيب، رقم الطلب والإجمالي.

   > استلمنا طلبك رقم {{1}} من ORQ. إجمالي الطلب {{2}} جنيه مصري، والدفع نقدًا عند الاستلام. شكرًا لاختيارك ORQ.

   أمثلة المتغيرات `ORQ-2026-00001` و`1490.00`.

3. بعد اعتماد القالب، حط بيانات `WHATSAPP_PHONE_NUMBER_ID` و`WHATSAPP_ACCESS_TOKEN` في `backend/.env` على السيرفر. لو اسم القالب مختلف، اضبط `WHATSAPP_ORDER_TEMPLATE` بنفس الاسم، مع نفس ترتيب المتغيرات.
4. نفّذ نشر الواجهة والـbackend بالأمر في قسم التحديث حتى تتطبق الـmigration وتتجدد إعدادات الموقع. اختبر بطلب على رقم تملكه مع اختيار استلام التأكيد على واتساب. Laravel يسجل رفض Meta في الـlog مع رقم الطلب وكود الخطأ.

## ملاحظات أمان
- خلي `APP_DEBUG=false` و`APP_ENV=production` (القالب جاهز كده).
- افتح من الـ Firewall بورتات 80/443 بس. بورت عملية ORQ يفضل داخلي.
- مفاتيح UltraMsg القديمة (من مشروع Medicamo) اتشالت من الكود. لو استخدمتها في أي مكان قبل كده اعتبرها مكشوفة وغيّرها. واتساب الأدمن اختياري (اتركه فاضي).
- اعمل نسخة احتياطية دورية لقاعدة `orq` من aaPanel (Database ← Backup) ولمجلد `backend/storage/app/public` (صور المنتجات المرفوعة).
