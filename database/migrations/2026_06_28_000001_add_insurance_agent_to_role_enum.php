<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE users
            MODIFY COLUMN role ENUM(
                'superadmin',
                'admin',
                'manager',
                'operator',
                'business_owner',
                'insurance',
                'insurance_agent',
                'shop',
                'garage',
                'marketer',
                'individual',
                'accountant'
            ) DEFAULT 'operator'
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE users
            MODIFY COLUMN role ENUM(
                'superadmin',
                'admin',
                'manager',
                'operator',
                'business_owner',
                'insurance',
                'shop',
                'garage',
                'marketer',
                'individual',
                'accountant'
            ) DEFAULT 'operator'
        ");
    }
};
