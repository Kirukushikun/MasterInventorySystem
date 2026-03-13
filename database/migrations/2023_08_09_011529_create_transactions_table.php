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

        // Read-only
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id')->nullable();
            $table->unsignedBigInteger('assigned_by_user_id');
            $table->unsignedBigInteger('assigned_user_id');
            $table->unsignedBigInteger('transaction_type_id');
            $table->unsignedBigInteger('farm_location_id');
            $table->unsignedBigInteger('department_division_id');
            $table->integer('quantity');
            $table->timestamp('transaction_date');
            $table->text('notes')->nullable();
            $table->boolean('active_status')->default(true);
            $table->boolean('deleted_status')->default(true);
            $table->timestamps();

            //['In', 'Out']

            $table->foreign('item_id')->references('id')->on('items');
            $table->foreign('assigned_by_user_id')->references('id')->on('users');
            $table->foreign('assigned_user_id')->references('id')->on('users');
            $table->foreign('transaction_type_id')->references('id')->on('transaction_types');
            $table->foreign('farm_location_id')->references('id')->on('farm_locations');
            $table->foreign('department_division_id')->references('id')->on('department_divisions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
