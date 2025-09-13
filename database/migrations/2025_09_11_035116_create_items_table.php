<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deck_id')->constrained()->cascadeOnDelete();

            // 'flashcard' | 'mcq' | 'matching'
            $table->enum('type', ['flashcard','mcq','matching']);

            // Nội dung chính (cho flashcard/mcq/matching đều dùng):
            $table->string('front', 512);
            $table->text('back')->nullable(); // nghĩa/đáp án/giải thích
            $table->json('data')->nullable(); // choices (MCQ), pairs (matching), hints...

            $table->string('hint', 255)->nullable();
            $table->unsignedInteger('position')->default(0); // để sort trong deck
            $table->timestamps();

            $table->index(['deck_id','type']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('items');
    }
};
