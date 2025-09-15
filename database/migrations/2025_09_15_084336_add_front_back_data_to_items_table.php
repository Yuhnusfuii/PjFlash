<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::table('items', function (Blueprint $table) {
        if (!Schema::hasColumn('items','front')) $table->text('front')->nullable()->after('type');
        if (!Schema::hasColumn('items','back'))  $table->text('back')->nullable()->after('front');
        if (!Schema::hasColumn('items','data'))  $table->json('data')->nullable()->after('back');
    });
}

public function down(): void
{
    Schema::table('items', function (Blueprint $table) {
        $table->dropColumn(['front','back','data']);
    });
}

};
