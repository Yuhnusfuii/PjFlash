<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('items', function (Blueprint $table) {
    $table->id();
    $table->foreignId('deck_id')->constrained()->cascadeOnDelete();
    $table->string('type', 20);     // flashcard | mcq | matching
    $table->text('front')->nullable();   // ✅ nullable
    $table->text('back')->nullable();    // ✅ nullable
    $table->json('data')->nullable();    // ✅ nullable
    $table->timestamps();
});

    }
    public function down(): void {
        Schema::dropIfExists('items');
    }
};
