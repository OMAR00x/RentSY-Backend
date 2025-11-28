# تعليمات تشغيل المشروع

## 1. تثبيت المكتبات المطلوبة

```bash
composer require laravel/sanctum
```

## 2. تشغيل الـ Migrations

```bash
php artisan migrate:fresh --seed
```

## 3. إنشاء رابط للتخزين

```bash
php artisan storage:link
```

## 4. تشغيل السيرفر

```bash
php artisan serve
```

## معلومات الأدمن الافتراضي

- الهاتف: `0900000000`
- كلمة المرور: `admin123`

## API Endpoints

### المصادقة
- `POST /api/register` - إنشاء حساب جديد
- `POST /api/login` - تسجيل الدخول
- `POST /api/logout` - تسجيل الخروج

### العقارات
- `GET /api/apartments` - عرض جميع العقارات (مع الفلترة)
- `GET /api/apartments/{id}` - تفاصيل عقار
- `POST /api/apartments` - إضافة عقار (مؤجر فقط)
- `GET /api/my-apartments` - عقاراتي (مؤجر فقط)
- `PUT /api/apartments/{id}` - تعديل عقار
- `DELETE /api/apartments/{id}` - حذف عقار

### الحجوزات
- `POST /api/bookings` - إنشاء حجز
- `GET /api/my-bookings?status=upcoming|past|cancelled` - حجوزاتي
- `GET /api/owner-bookings` - طلبات الحجز (مؤجر فقط)
- `PUT /api/bookings/{id}` - تعديل حجز
- `PUT /api/bookings/{id}/status` - تحديث حالة الحجز

### المفضلة
- `GET /api/favorites` - عرض المفضلة
- `POST /api/favorites/toggle` - إضافة/إزالة من المفضلة

### التقييمات
- `POST /api/reviews` - إضافة تقييم

### الرسائل
- `GET /api/conversations` - قائمة المحادثات
- `GET /api/messages/{userId}` - رسائل مع مستخدم
- `POST /api/messages` - إرسال رسالة

### الإشعارات
- `GET /api/notifications` - عرض الإشعارات
- `PUT /api/notifications/{id}/read` - تحديد كمقروء
- `PUT /api/notifications/read-all` - تحديد الكل كمقروء

### البحث
- `GET /api/search/history` - سجل البحث
- `POST /api/search/save` - حفظ بحث
- `DELETE /api/search/history/{id}` - حذف من السجل

### الفلاتر
- `GET /api/filters` - خيارات الفلترة (المحافظات، المدن، الميزات)

### الأدمن
- `GET /api/admin/users/pending` - المستخدمين بانتظار الموافقة
- `PUT /api/admin/users/{id}/approve` - الموافقة على مستخدم
- `PUT /api/admin/users/{id}/reject` - رفض مستخدم
- `GET /api/admin/apartments/pending` - العقارات بانتظار الموافقة
- `PUT /api/admin/apartments/{id}/approve` - الموافقة على عقار
- `PUT /api/admin/apartments/{id}/reject` - رفض عقار

## معاملات الفلترة للعقارات

```
GET /api/apartments?area_id=1&city_id=2&min_price=100&max_price=500&rooms=2&amenities=1,2,3&search=شقة
```

## ملاحظات مهمة

1. جميع الطلبات المحمية تحتاج إلى Header:
   ```
   Authorization: Bearer {token}
   ```

2. عند رفع الصور استخدم `multipart/form-data`

3. رقم الهاتف السوري يجب أن يكون بصيغة: `09xxxxxxxx`

4. حالات الحجز:
   - `pending` - بانتظار الموافقة
   - `approved` - تمت الموافقة
   - `rejected` - مرفوض
   - `cancelled` - ملغي
   - `completed` - مكتمل

5. حالات المستخدم:
   - `pending` - بانتظار الموافقة
   - `approved` - تمت الموافقة
   - `rejected` - مرفوض

6. أدوار المستخدمين:
   - `renter` - مستأجر
   - `owner` - مؤجر
   - `admin` - أدمن
