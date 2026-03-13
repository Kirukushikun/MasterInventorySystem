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
        Schema::create('request_items', function (Blueprint $table) {
            $table->id();
            $table->string('series_number');
            $table->unsignedBigInteger('approver_id')->nullable();
            $table->unsignedBigInteger('requested_by_id');
            $table->unsignedBigInteger('farm_location_id');
            $table->unsignedBigInteger('department_division_id');
            $table->unsignedBigInteger('approval_id')->nullable();
            $table->string('remarks')->nullable();
            $table->text('comment')->nullable();
            $table->string('date_requested');
            $table->string('date_needed');
            $table->boolean('checkout_status')->default(false);
            $table->string('jl_pdf')->nullable();
            $table->boolean('active_status')->default(true);
            $table->boolean('deleted_status')->default(true);
            $table->timestamps();

            $table->foreign('farm_location_id')->references('id')->on('farm_locations');
            $table->foreign('department_division_id')->references('id')->on('department_divisions');
            $table->foreign('approver_id')->references('id')->on('users');
            $table->foreign('requested_by_id')->references('id')->on('users');
            $table->foreign('approval_id')->references('id')->on('approvals');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_items');
    }
};
