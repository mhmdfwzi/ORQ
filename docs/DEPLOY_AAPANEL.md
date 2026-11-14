# نشر ORQ على سيرفر aaPanel (Ubuntu 22)

> الدومينات هنا أمثلة: الموقع `orq.mhmdfwzi.com` والـ API `api.orq.mhmdfwzi.com`. غيّرها بدوميناتك.
> لو ORQ شغّال بالفعل، التحديث يستخدم عملية PM2 باسم `orq` وإعداداتها الحالية. في أول نشر فقط، اختار بورت داخلي فاضي مخصص لـ ORQ.

## 1) من لوحة aaPanel (مرة واحدة)

**App Store** ثبّت: Nginx، MySQL 8، **PHP 8.3 أو 8.4**، و**PM2 Manager** (ومعاه Node.js 20+).

**PHP ← Settings**
- *Install extensions*: `fileinfo` و`intl` و`exif` و`opcache`.
- *Disabled functions*: امسح من القائمة `putenv` و`proc_open` و`proc_get_status` و`symlink` و`exec` (لازمين لـ Composer وLaravel).

**Database ← Add**: قاعدة `orq` + مستخدم `orq_user` بباسورد قوي (استخدم Utf8mb4).

**Website ← Add site** (للـ API):
- الدومين: `api.orq.mhmdfwzi.com`، النسخة PHP اللي ثبتّها.
- بعد إنشاءه: *Site directory* ← **Running directory = `/public`** والمسار `/www/wwwroot/orq/backend`.
- *Site directory* ← **أوقف** "Anti-XSS attack (open_basedir)" (وإلا Laravel مش هيقدر يقرأ برا `public`).
- *Pseudo static* ← اختر قالب **laravel5** (أو الصق: `location / { try_files $uri $uri/ /index.php?$query_string; }`).
- *SSL* ← Let's Encrypt ← فعّل Force HTTPS.

**Website ← Add site** (للموقع): الدومين `orq.mhmdfwzi.com` (PHP: Static) ثم:
- *Reverse proxy* ← Add: الهدف `http://127.0.0.1:<بورت ORQ>` (نفس `FRONTEND_PORT` عند إنشاء العملية).
- اقفل **Cache** لبروكسي ORQ فقط علشان الزوار يشوفوا الـ HTML الجديد بعد النشر. لو بتعدّل إعداد Nginx مباشرة، استخدم `proxy_cache off;` في الـ `location` اللي بيخدم واجهة ORQ.
- *SSL* ← Let's Encrypt ← Force HTTPS.

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
#   API_URL=https://api.orq.mhmdfwzi.com/api/v1
#   NEXT_PUBLIC_API_URL=https://api.orq.mhmdfwzi.com/api/v1
#   REVALIDATE_SECRET=<نفس القيمة>
#   NEXT_PUBLIC_SITE_URL=https://orq.mhmdfwzi.com
```

> `API_URL` لازم يكون رابط **الدومين العام (https)** مش `127.0.0.1`، لأن الـ API ورا Virtual Host بالدومين.

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

ادخل `https://api.orq.mhmdfwzi.com/admin`، وراجع **Settings ← إعدادات الموقع** و**قصة البراند**.

لتشغيل PM2 بعد إعادة تشغيل السيرفر: `pm2 startup` ثم `pm2 save`.

## 4) كل تحديث بعد كده

```bash
cd /www/wwwroot/orq && PHP_BIN=/www/server/php/84/bin/php bash scripts/deploy.sh
```

لو التحديث للواجهة فقط، شغّل `bash scripts/deploy.sh --frontend-only`. السكريبت يتأكد إن الصفحة الرئيسية على الدومين العام بتعرض الإصدار الجديد بطلب عادي وطلب يدعم ضغط المتصفح.

بعد تعطيل كاش البروكسي، تقدر تتحقق من نسخة المتصفح من غير إعادة نشر:

```bash
curl -fsS --compressed -H 'Accept-Encoding: gzip, deflate, br, zstd' \
  https://orq.mhmdfwzi.com/ | grep -o 'data-dpl-id="[^"]*"'
```

رقم الإصدار لازم يطابق اللي ظهر في سطر `✔ Web` في آخر نشر.

> **لا** تشغّل `--seed` على الموقع الحي بعد ما تعدّل المنتجات: بيرجّع الأسعار والصور للقيم الأصلية.

## ملاحظات أمان
- خلي `APP_DEBUG=false` و`APP_ENV=production` (القالب جاهز كده).
- افتح من الـ Firewall بورتات 80/443 بس. بورت عملية ORQ يفضل داخلي.
- مفاتيح UltraMsg القديمة (من مشروع Medicamo) اتشالت من الكود. لو استخدمتها في أي مكان قبل كده اعتبرها مكشوفة وغيّرها. واتساب الأدمن اختياري (اتركه فاضي).
- اعمل نسخة احتياطية دورية لقاعدة `orq` من aaPanel (Database ← Backup) ولمجلد `backend/storage/app/public` (صور المنتجات المرفوعة).
