<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proformas', function (Blueprint $table) {
            $table->timestamp('decrypted_at')->nullable();
        });

        Schema::table('proforma_applications', function (Blueprint $table) {
            $table->string('rank_label', 50)->nullable();
        });

        Schema::table('proforma_part_prices', function (Blueprint $table) {
            $table->string('rank_label', 50)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('proformas', function (Blueprint $table) {
            $table->dropColumn('decrypted_at');
        });

        Schema::table('proforma_applications', function (Blueprint $table) {
            $table->dropColumn('rank_label');
        });

        Schema::table('proforma_part_prices', function (Blueprint $table) {
            $table->dropColumn('rank_label');
        });
    }
};
