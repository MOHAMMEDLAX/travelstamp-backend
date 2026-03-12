<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visits', function (Blueprint $table) {
            $table->id();                                      // رقم تلقائي
            $table->foreignId('user_id')->constrained();      // صاحب الزيارة
            $table->string('country');                        // الدولة
            $table->string('country_code', 2);               // رمز الدولة (SA, JP)
            $table->string('city');                           // المدينة
            $table->date('visit_date');                       // تاريخ الزيارة
            $table->text('notes')->nullable();                // ملاحظات (اختياري)
            $table->integer('rating')->default(5);            // التقييم 1-5
            $table->string('photo')->nullable();              // الصورة (اختياري)
            $table->decimal('latitude', 10, 7)->nullable();   // موقع الخريطة
            $table->decimal('longitude', 10, 7)->nullable();  // موقع الخريطة
            $table->timestamps();                             // تاريخ الإنشاء
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};