<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::table('items', function (Blueprint $table) {
        $table->float('ef')->default(2.5);           // Easiness factor
        $table->integer('interval')->default(1);     // Ngày giữa các lần lặp
        $table->integer('repetition')->default(0);   // Số lần lặp liên tiếp nhớ được
        $table->timestamp('due_at')->nullable();     // Lịch hẹn ôn
    });
}

public function down(): void
{
    Schema::table('items', function (Blueprint $table) {
        $table->dropColumn(['ef','interval','repetition','due_at']);
    });
}

};
