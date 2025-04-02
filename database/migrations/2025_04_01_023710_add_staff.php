<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Only run if the staff table exists
        if (Schema::hasTable('staff')) {
            Schema::table('staff', function (Blueprint $table) {
                if (!Schema::hasColumn('staff', 'two_factor_code')) {
                    $table->string('two_factor_code')->nullable();
                }

                if (!Schema::hasColumn('staff', 'two_factor_expires_at')) {
                    $table->timestamp('two_factor_expires_at')->nullable();
                }

                // Change rol_id to role_id for consistency
                if (!Schema::hasColumn('staff', 'role_id') && Schema::hasTable('rol')) {
                    $table->foreignId('role_id')->after('staff_id')->nullable()->constrained('rol');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('staff')) {
            // Drop columns only if they exist
            Schema::table('staff', function (Blueprint $table) {
                $columns = ['two_factor_code', 'two_factor_expires_at'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('staff', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });

            // Drop foreign key if it exists
            Schema::table('staff', function (Blueprint $table) {
                if (Schema::hasColumn('staff', 'role_id')) {
                    $table->dropForeign(['role_id']);
                    $table->dropColumn('role_id');
                }
            });
        }
    }
};
