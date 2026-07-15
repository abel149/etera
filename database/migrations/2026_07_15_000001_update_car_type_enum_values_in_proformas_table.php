<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * RULES & SYNTAX EXPLANATION:
     * - DB::statement() executes raw SQL directly on the database
     * - ALTER TABLE proformas MODIFY COLUMN changes an existing column definition
     * - ENUM('value1', 'value2', ...) defines the allowed values
     * - NOT NULL means the column cannot be empty
     * - DEFAULT 'value' sets the default value for new records
     * 
     * This preserves existing data - only invalid values would cause errors.
     */
    public function up(): void
    {
        // Step 1: Convert existing old values to new values
        DB::statement("UPDATE proformas SET car_type = 'Sedan/S.U.V(GAS)' WHERE car_type = 'ICE'");
        DB::statement("UPDATE proformas SET car_type = 'Sedan/S.U.V(EV)' WHERE car_type = 'EV'");
        DB::statement("UPDATE proformas SET car_type = 'Mini Van(GAS)' WHERE car_type = 'Hybrid'");
        DB::statement("UPDATE proformas SET car_type = 'Heavy' WHERE car_type = 'Others'");

        // Step 2: Change the enum definition
        DB::statement("ALTER TABLE proformas MODIFY COLUMN car_type ENUM('Sedan/S.U.V(GAS)', 'Sedan/S.U.V(EV)', 'Mini Van(GAS)', 'Mini Van(EV)', 'Isuzu/Bus(GAS)', 'Isuzu/Bus(EV)', 'Heavy') NOT NULL DEFAULT 'Sedan/S.U.V(GAS)'");
    }

    /**
     * Reverse the migrations.
     * 
     * This restores the old enum values for rollback.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE proformas MODIFY COLUMN car_type ENUM('ICE', 'EV', 'Hybrid', 'Others') NOT NULL DEFAULT 'ICE'");
    }
};
