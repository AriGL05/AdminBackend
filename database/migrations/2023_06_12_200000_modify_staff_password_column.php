<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class ModifyStaffPasswordColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Use direct MySQL query for maximum compatibility
        try {
            // First try to modify it with ALTER TABLE
            DB::statement('ALTER TABLE staff MODIFY password VARCHAR(255)');
        } catch (\Exception $e) {
            // If that fails, try with a different approach
            $columnExists = DB::select("SHOW COLUMNS FROM staff LIKE 'password'");

            if (count($columnExists) > 0) {
                // Get current column definition and change only the type
                $columnInfo = DB::select("SHOW COLUMNS FROM staff WHERE Field = 'password'")[0];
                $isNullable = $columnInfo->Null === 'YES' ? 'NULL' : 'NOT NULL';
                $default = $columnInfo->Default !== null ? "DEFAULT '" . $columnInfo->Default . "'" : '';

                DB::statement("ALTER TABLE staff CHANGE password password VARCHAR(255) $isNullable $default");
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // This is not recommended as it could cause data loss
    }
}
