<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTwoFactorToStaffTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('staff', function (Blueprint $table) {
            // Check if columns don't exist before adding
            if (!Schema::hasColumn('staff', 'two_factor_code')) {
                $table->string('two_factor_code')->nullable();
            }

            if (!Schema::hasColumn('staff', 'two_factor_expires_at')) {
                $table->timestamp('two_factor_expires_at')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->dropColumn(['two_factor_code', 'two_factor_expires_at']);
        });
    }
}
