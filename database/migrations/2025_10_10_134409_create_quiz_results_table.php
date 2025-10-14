<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('quiz_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->cascadeOnDelete();
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            $table->string('direction', 20);       // front_to_back|back_to_front
            $table->text('question');              // text hiển thị
            $table->text('correct');               // đáp án đúng
            $table->text('picked')->nullable();    // đáp án đã chọn
            $table->boolean('is_correct')->default(false);
            $table->timestamps();
            $table->index(['quiz_id','item_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('quiz_results'); }
};
