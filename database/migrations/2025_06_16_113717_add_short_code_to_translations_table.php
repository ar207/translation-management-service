<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('translations', function (Blueprint $table) {
            $table->string('locale_code')->after('locale_id')->nullable()->index();
            $table->index([DB::raw('locale_code(10)'), DB::raw('`key`(190)')], 'locale_code_key_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('translations', function (Blueprint $table) {
            $table->dropColumn('locale_code');
            $table->dropIndex('translations_locale_code_index');
        });
    }
};
