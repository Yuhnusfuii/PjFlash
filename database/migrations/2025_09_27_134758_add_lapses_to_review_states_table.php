<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('review_states', function (Blueprint $table) {
            if (!Schema::hasColumn('review_states', 'lapses')) {
                $table->unsignedInteger('lapses')->default(0)->after('repetitions');
            }
        });
    }

    public function down(): void
    {
        Schema::table('review_states', function (Blueprint $table) {
            if (Schema::hasColumn('review_states', 'lapses')) {
                $table->dropColumn('lapses');
            }
        });
    }
};
