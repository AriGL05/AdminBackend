<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FixStaffPasswordLength extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Direct SQL approach to ensure compatibility with MySQL
        DB::statement('ALTER TABLE staff MODIFY password VARCHAR(255)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // This is optional as we probably don't want to go back to a shorter length
        // If you need to revert, you'd need to know the original length
        // DB::statement('ALTER TABLE staff MODIFY password VARCHAR(40)');
    }
}
