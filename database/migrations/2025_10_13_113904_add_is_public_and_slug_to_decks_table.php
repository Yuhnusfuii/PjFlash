<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
{
    Schema::table('decks', function (Blueprint $table) {
        if (!Schema::hasColumn('decks', 'is_public')) {
            $table->boolean('is_public')->default(false)->index()->after('description');
        }
        if (!Schema::hasColumn('decks', 'slug')) {
            $table->string('slug')->nullable()->unique()->after('name');
        }
    });
}


    public function down(): void
    {
        Schema::table('decks', function (Blueprint $table) {
            $table->dropColumn(['is_public', 'slug']);
        });
    }
};
