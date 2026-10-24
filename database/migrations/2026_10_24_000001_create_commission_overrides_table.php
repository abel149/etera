<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commission_overrides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proforma_id')->constrained('proformas')->cascadeOnDelete();
            $table->foreignId('application_id')->unique()->constrained('proforma_applications')->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->string('reason')->nullable();
            $table->foreignId('set_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('proforma_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commission_overrides');
    }
};
