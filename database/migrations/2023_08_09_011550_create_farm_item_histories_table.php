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
        Schema::create('farm_item_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('farm_item_id');
            $table->unsignedBigInteger('transaction_type_id')->nullable();
            $table->integer('previous_quantity')->nullable();
            $table->integer('new_quantity');
            $table->timestamp('change_date');
            $table->text('change_reason')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->date('old_purchase_date')->nullable();
            $table->date('new_purchase_date')->nullable();
            $table->date('old_expiry_date')->nullable();
            $table->date('new_expiry_date')->nullable();
            $table->boolean('active_status')->default(true);
            $table->boolean('deleted_status')->default(true);
            $table->timestamps();

            $table->foreign('farm_item_id')->references('id')->on('farm_inventories');
            $table->foreign('transaction_type_id')->references('id')->on('transaction_types');
            $table->foreign('user_id')->references('id')->on('users'); // You need to create a 'users' table migration for this reference.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('farm_item_histories');
    }
};
