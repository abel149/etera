<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proforma_part_prices', function (Blueprint $table) {
            $table->unsignedBigInteger('proforma_id')->nullable()->after('id');
            $table->unsignedTinyInteger('inbox_group')->nullable()->after('proforma_id');
            $table->foreign('proforma_id')->references('id')->on('proformas')->nullOnDelete();
        });

        // Backfill proforma_id from the related application
        DB::statement('
            UPDATE proforma_part_prices ppp
            JOIN proforma_applications pa ON pa.id = ppp.application_id
            SET ppp.proforma_id = pa.proforma_id
            WHERE ppp.proforma_id IS NULL
        ');

        // Backfill inbox_group from the related application
        DB::statement('
            UPDATE proforma_part_prices ppp
            JOIN proforma_applications pa ON pa.id = ppp.application_id
            SET ppp.inbox_group = pa.inbox_group
            WHERE ppp.inbox_group IS NULL AND pa.inbox_group IS NOT NULL
        ');

        // Unique constraint: prevents two prices for the same part in the same group
        // NULL inbox_group is excluded from MySQL unique enforcement (NULLs != NULLs),
        // so only new records with real group numbers are protected.
        Schema::table('proforma_part_prices', function (Blueprint $table) {
            $table->unique(['proforma_id', 'inbox_group', 'car_part_id'], 'uq_group_part_price');
        });
    }

    public function down(): void
    {
        Schema::table('proforma_part_prices', function (Blueprint $table) {
            $table->dropUnique('uq_group_part_price');
            $table->dropForeign(['proforma_id']);
            $table->dropColumn(['proforma_id', 'inbox_group']);
        });
    }
};
