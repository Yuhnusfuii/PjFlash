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

        $table->tinyInteger('rating');                  // 1..4
        $table->unsignedInteger('interval_days');
        $table->float('ease_factor');

        $table->timestamp('reviewed_at')->useCurrent(); // ✅ có DEFAULT NOW
        $table->timestamp('next_due_at')->nullable();   // ✅ cho phép null

        $table->unsignedInteger('duration_ms')->default(0);
        $table->json('meta')->nullable();
        $table->timestamps();

        $table->index(['user_id','item_id','reviewed_at']);
    });

    }
    public function down(): void {
        Schema::dropIfExists('reviews');
    }
};
