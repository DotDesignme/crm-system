# نظام إدارة الليدز - Lead Management System

## التشغيل المحلي

```bash
# 1. تثبيت الاعتماديات
composer install

# 2. إنشاء ملف البيئة
cp .env.example .env
php artisan key:generate

# 3. تعديل إعدادات قاعدة البيانات في .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=lead_system
DB_USERNAME=root
DB_PASSWORD=your_password

# 4. إنشاء قاعدة البيانات
mysql -u root -p -e "CREATE DATABASE lead_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 5. تشغيل المigrations والSeeder
php artisan migrate --seed

# 6. تشغيل السيرفر
php artisan serve
```

## النشر على aaPanel

### 1. رفع الملفات
ارفع مجلد المشروع على السيرفر في `/www/wwwroot/lead-system`

### 2. إعداد Nginx
- في aaPanel، أنشئ موقع جديد
- في إعدادات Nginx، استبدل المحتوى بملف `nginx.conf`
- تأكد من تغيير `server_name` و `fastcgi_pass` حسب إعداداتك

### 3. إعداد قاعدة البيانات
```bash
mysql -u root -p -e "CREATE DATABASE lead_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

### 4. إعداد .env
```bash
cp .env.example .env
# عدّل DB_CONNECTION, DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD
# غيّر APP_URL للدومين بتاعك
# غيّر APP_DEBUG=false
```

### 5. تشغيل الأوامر
```bash
cd /www/wwwroot/lead-system
composer install --optimize-autoloader --no-dev
php artisan key:generate
php artisan migrate --seed
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 6. إعداد الصلاحيات
```bash
chown -R www:www /www/wwwroot/lead-system
chmod -R 755 /www/wwwroot/lead-system/storage
chmod -R 755 /www/wwwroot/lead-system/bootstrap/cache
```

## بيانات الدخول الافتراضية

| الدور | اسم المستخدم | كلمة المرور |
|-------|-------------|-------------|
| أدمن  | admin       | admin123    |
| موظف 1 | emp1      | 123456      |
| موظف 2 | emp2      | 123456      |
| موظف 3 | emp3      | 123456      |

## المميزات

- كل موظف مرتبط بصفحة فيسبوك واحدة
- منع تكرار الليدز (بنفس التليفون أو الإيميل) على مستوى النظام كله
- لوحة تحكم للأدمن لإدارة الصفحات والموظفين
- البحث والفلترة في الليدز
- واجهة عربية RTL
