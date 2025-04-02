<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Check if staff table exists
        if (Schema::hasTable('staff')) {
            // Check if rol_id exists and role_id doesn't exist
            if (Schema::hasColumn('staff', 'rol_id') && !Schema::hasColumn('staff', 'role_id')) {
                // First drop the foreign key constraint
                Schema::table('staff', function (Blueprint $table) {
                    $table->dropForeign(['rol_id']);
                });

                // Then rename the column
                Schema::table('staff', function (Blueprint $table) {
                    $table->renameColumn('rol_id', 'role_id');
                });

                // Re-add the foreign key constraint
                Schema::table('staff', function (Blueprint $table) {
                    $table->foreign('role_id')->references('id')->on('rol');
                });
            }
            // Removed the code that added a new role_id column
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // No need to reverse this operation as we're just standardizing naming
    }
};
