<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('review_states', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();

            // SM-2
            $table->float('ease_factor')->default(2.5);       // EF ≥ 1.3
            $table->unsignedInteger('interval_days')->default(0);
            $table->timestamp('due_at')->nullable();

            // Theo dõi
            $table->unsignedInteger('repetitions')->default(0);
            $table->unsignedInteger('lapses')->default(0);
            $table->timestamp('last_reviewed_at')->nullable();
            $table->boolean('suspended')->default(false);

            // Chỉ số phụ để analytics/ordering
            $table->unsignedSmallInteger('stability')->default(0);

            $table->timestamps();

            $table->unique(['user_id','item_id']); // mỗi user có 1 state cho 1 item
            $table->index(['user_id','due_at']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('review_states');
    }
};
