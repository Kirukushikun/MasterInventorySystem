<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('password')->nullable();
            $table->unsignedBigInteger('farm_location_id');
            $table->unsignedBigInteger('department_division_id');
            $table->boolean('active_status')->default(true);
            $table->boolean('deleted_status')->default(true);
            $table->string('role', 20)->nullable();
            $table->timestamps();

            $table->foreign('farm_location_id')->references('id')->on('farm_locations');
            $table->foreign('department_division_id')->references('id')->on('department_divisions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
