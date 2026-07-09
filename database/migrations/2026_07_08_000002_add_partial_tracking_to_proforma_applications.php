<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proforma_applications', function (Blueprint $table) {
            $table->unsignedSmallInteger('filled_parts_count')->default(0)->after('inbox_group');
            $table->unsignedSmallInteger('total_parts_count')->default(0)->after('filled_parts_count');
            $table->boolean('is_partial')->default(false)->after('total_parts_count');
        });
    }

    public function down(): void
    {
        Schema::table('proforma_applications', function (Blueprint $table) {
            $table->dropColumn(['filled_parts_count', 'total_parts_count', 'is_partial']);
        });
    }
};
