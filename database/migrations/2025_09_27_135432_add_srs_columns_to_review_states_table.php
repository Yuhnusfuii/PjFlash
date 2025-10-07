<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('review_states', function (Blueprint $table) {
            if (!Schema::hasColumn('review_states', 'ease_factor')) {
                // 2.50 là mặc định SM-2
                $table->decimal('ease_factor', 3, 2)->default(2.50)->after('item_id');
            }
            if (!Schema::hasColumn('review_states', 'interval_days')) {
                $table->unsignedInteger('interval_days')->default(0)->after('ease_factor');
            }
            if (!Schema::hasColumn('review_states', 'repetitions')) {
                $table->unsignedInteger('repetitions')->default(0)->after('interval_days');
            }
            if (!Schema::hasColumn('review_states', 'lapses')) {
                $table->unsignedInteger('lapses')->default(0)->after('repetitions');
            }
            if (!Schema::hasColumn('review_states', 'last_reviewed_at')) {
                $table->timestamp('last_reviewed_at')->nullable()->after('due_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('review_states', function (Blueprint $table) {
            if (Schema::hasColumn('review_states', 'last_reviewed_at')) {
                $table->dropColumn('last_reviewed_at');
            }
            if (Schema::hasColumn('review_states', 'lapses')) {
                $table->dropColumn('lapses');
            }
            if (Schema::hasColumn('review_states', 'repetitions')) {
                $table->dropColumn('repetitions');
            }
            if (Schema::hasColumn('review_states', 'interval_days')) {
                $table->dropColumn('interval_days');
            }
            if (Schema::hasColumn('review_states', 'ease_factor')) {
                $table->dropColumn('ease_factor');
            }
        });
    }
};
