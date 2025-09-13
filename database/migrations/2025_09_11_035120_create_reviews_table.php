<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();

            // 0=Again,1=Hard,2=Good,3=Easy
            $table->tinyInteger('rating');

            // Snapshot sau lần review này
            $table->unsignedInteger('interval_days');
            $table->float('ease_factor');
            $table->timestamp('reviewed_at');   // thời điểm chấm
            $table->timestamp('next_due_at');   // dự kiến lần sau
            $table->unsignedInteger('duration_ms')->default(0); // thời gian trả lời (tuỳ chọn)
            $table->json('meta')->nullable();

            $table->timestamps();

            $table->index(['user_id','item_id','reviewed_at']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('reviews');
    }
};
