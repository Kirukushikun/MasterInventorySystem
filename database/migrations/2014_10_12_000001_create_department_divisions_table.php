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
        Schema::create('department_divisions', function (Blueprint $table) {
            $table->id();
            $table->string('department_division');
            $table->string('abbreviation');
            $table->boolean('active_status')->default(true);
            $table->boolean('deleted_status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('department_divisions');
    }
};
