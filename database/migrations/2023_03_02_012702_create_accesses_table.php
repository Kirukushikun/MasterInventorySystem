<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('accesses', function (Blueprint $table) {
            $table->comment('');
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('farm_location_id');
            $table->unsignedBigInteger('department_division_id');
            $table->text('access')->nullable();
            $table->string('action')->nullable();
            $table->timestamps();

            
            $table->foreign('farm_location_id')->references('id')->on('farm_locations');
            $table->foreign('department_division_id')->references('id')->on('department_divisions');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accesses');
    }
};
