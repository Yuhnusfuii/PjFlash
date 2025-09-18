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

            $table->double('ease')->default(2.5);
            $table->integer('interval')->default(0);      // Laravel sẽ tự quote `interval`
            $table->integer('repetitions')->default(0);
            $table->timestamp('due_at')->nullable();
            $table->timestamp('last_reviewed_at')->nullable();

            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('review_states');
    }
};
