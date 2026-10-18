# بنز پارت عمان — Benz Part Oman

فروشگاه آنلاین قطعات خودرو (به‌خصوص مرسدس بنز) از عمان به ایران.
**Laravel 12 + PHP 8.2 + Blade + Tailwind CSS 4 + Swiper + Vite**

---

## راه‌اندازی (Development)

```bash
composer install
npm install
cp .env.example .env          # سپس SUPER_ADMIN_EMAIL و SUPER_ADMIN_PASSWORD را پر کنید
php artisan key:generate
php artisan migrate --seed    # نقش‌ها، حساب مدیر کل، برندها/مدل‌ها/شاسی‌ها و محصولات نمونه
php artisan storage:link
npm run build                 # یا در حین توسعه: npm run dev
php artisan serve --no-reload
```

> **ویندوز:** بدون `--no-reload` لاراول متغیرهای محیطی ویندوز (مثل `SystemRoot`) را به PHP نمی‌دهد و خطای
> `Failed to listen on 127.0.0.1:8000 (reason: ?)` می‌گیرید.

- سایت: http://127.0.0.1:8000
- پنل مدیریت: http://127.0.0.1:8000/admin (قابل تغییر با `ADMIN_PATH`)
- تست‌ها: `php artisan test`

### دیتابیس MySQL (WAMP)
پیش‌فرض SQLite است. برای MySQL یک دیتابیس `benz_oman_part` (utf8mb4) بسازید و در `.env`:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=benz_oman_part
DB_USERNAME=root
DB_PASSWORD=
```
سپس `php artisan migrate --seed`.

---

## حساب مدیر کل (Owner)

در `.env`:
```
SUPER_ADMIN_EMAIL=you@yourdomain.com
SUPER_ADMIN_PASSWORD=a-long-strong-password
```
سپس: `php artisan db:seed --class=SuperAdminSeeder` (هر بار اجرا، حساب را ایجاد/به‌روزرسانی می‌کند).

- نقش «مدیر کل» همه دسترسی‌ها را دارد و **از داخل پنل قابل اعطا، تغییر یا مسدودسازی نیست**.
- فقط مدیر کل «نقش‌ها و دسترسی‌ها» و «گزارش فعالیت‌ها» را می‌بیند.
- هر مدیر فقط می‌تواند نقشی را اعطا کند که دسترسی‌هایش زیرمجموعه دسترسی‌های خودش باشد (جلوگیری از ارتقای غیرمجاز).

---

## ارسال کد تایید (OTP)

| کانال | وضعیت فعلی | برای فعال‌سازی |
|---|---|---|
| ایمیل | `MAIL_MAILER=log` → کد در `storage/logs/laravel.log` | تنظیم SMTP در `.env` (پایین) |
| پیامک | `SMS_DRIVER=log` → کد در لاگ | `SMS_DRIVER=kavenegar` + `SMS_API_KEY` + `SMS_SENDER` (نام الگو) |

در محیط local با `OTP_DEBUG_SHOW=true` کد روی صفحه هم نمایش داده می‌شود (در production هرگز فعال نمی‌شود).

### ایمیل (SMTP)
ساده‌ترین روش: ایمیل روی دامنه خودتان از طریق هاست (cPanel/DirectAdmin) مثل `no-reply@yourdomain.com`:
```
MAIL_MAILER=smtp
MAIL_SCHEME=smtps
MAIL_HOST=mail.yourdomain.com
MAIL_PORT=465
MAIL_USERNAME=no-reply@yourdomain.com
MAIL_PASSWORD=********
MAIL_FROM_ADDRESS=no-reply@yourdomain.com
```
حتماً رکوردهای **SPF، DKIM و DMARC** دامنه را تنظیم کنید تا ایمیل‌ها به Spam نروند.
برای حجم بالا: سرویس‌هایی مثل Amazon SES، Brevo یا Mailgun (بسته به محل سرور و محدودیت‌های تحریم).

### پیامک
درایور آماده کاوه‌نگار (`app/Services/Sms/KavenegarSmsSender.php`). برای سرویس‌های دیگر (SMS.ir، ملی‌پیامک، قاصدک) کافی است یک کلاس با رابط `App\Contracts\SmsSender` بسازید و در `AppServiceProvider` ثبت کنید.

---

## امنیت

- رمزها با bcrypt؛ کدهای OTP به‌صورت HMAC ذخیره، ۲ دقیقه اعتبار، حداکثر ۵ تلاش، فاصله ارسال مجدد ۶۰ ثانیه
- Rate limit روی ورود، ارسال/بررسی کد، استعلام، ثبت سفارش و جستجو
- CSRF، Session رمزنگاری‌شده، regenerate پس از ورود، خروج از سایر دستگاه‌ها پس از تغییر رمز
- هدرهای امنیتی + Content-Security-Policy (همه اسکریپت‌ها bundle محلی؛ بدون CDN)
- هر کاربر یک **کد عددی داخلی ۱۰ رقمی ثابت** دارد که هرگز تغییر نمی‌کند، به خود کاربر نمایش داده نمی‌شود و آدرس کاربران در پنل با آن ساخته می‌شود (نه id ترتیبی)
- تصاویر آپلودی با GD دوباره رمزگذاری (WebP) می‌شوند: حذف متادیتا و محتوای مخفی
- `role_id`، `internal_code` و وضعیت مسدودی mass-assignable نیستند
- گزارش فعالیت مدیران (Audit log)

---

## چک‌لیست انتشار (Production)

1. `APP_ENV=production` و `APP_DEBUG=false`
2. HTTPS و `SESSION_SECURE_COOKIE=true`
3. `ADMIN_PATH` را به یک مسیر غیرقابل حدس تغییر دهید
4. `OTP_DEBUG_SHOW=false`، تنظیم SMTP و پیامک
5. `SUPER_ADMIN_PASSWORD` قوی؛ بعد از seed می‌توانید آن را از `.env` پاک کنید
6. محصولات و اسلایدهای نمونه را از پنل حذف کنید (در production ساخته نمی‌شوند)
7. `composer install --no-dev -o && npm run build`
8. `php artisan config:cache && php artisan route:cache && php artisan view:cache`
9. پشت CDN (ابرآروان/کلودفلر): `TRUSTED_PROXIES=*`

---

## ساختار

```
app/Http/Controllers         فروشگاه (Home, Product, Search, Cart, Checkout, Inquiry, Page)
app/Http/Controllers/Auth    ورود، ثبت‌نام سه‌مرحله‌ای با OTP، بازیابی رمز
app/Http/Controllers/Account حساب کاربری، ویرایش اطلاعات، تغییر ایمیل/موبایل، استعلام‌ها و سفارش‌های من
app/Http/Controllers/Admin   پنل مدیریت
app/Services                 OtpService, Cart, ImageService, Sms drivers
config/permissions.php       فهرست دسترسی‌ها (منبع واحد)
config/shop.php              وضعیت‌ها، کشورها، استان‌ها، تنظیمات OTP و مسیر پنل
resources/views              قالب‌های Blade (RTL)
resources/js/modules         اسلایدرها، جستجوی زنده، تغییر ارز، سبد خرید و ...
```
