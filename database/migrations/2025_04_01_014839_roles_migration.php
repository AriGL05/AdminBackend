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
        // Check if the 'rol' table already exists before trying to create it
        if (!Schema::hasTable('rol')) {
            Schema::create('rol', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->timestamps();
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
        // Disable foreign key checks before dropping the table
        Schema::disableForeignKeyConstraints();

        // Drop the table
        Schema::dropIfExists('rol');

        // Re-enable foreign key checks
        Schema::enableForeignKeyConstraints();
    }
};
