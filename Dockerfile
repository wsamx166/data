# استخدام صورة رسمية لـ PHP
FROM php:8.2-cli

# تعيين مجلد العمل داخل الحاوية
WORKDIR /app

# نسخ كل الملفات إلى داخل الحاوية
COPY . .

# تثبيت curl وأدوات مساعدة (اختياري لو بوتك يحتاج يتعامل مع APIs)
RUN apt-get update && apt-get install -y \
    curl \
    unzip \
    libzip-dev \
    zip

# تحديد الأمر الذي يتم تنفيذه عند تشغيل الحاوية
CMD ["php", "bot.php"]
