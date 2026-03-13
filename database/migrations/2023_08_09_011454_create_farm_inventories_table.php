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
        Schema::create('farm_inventories', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('user_assigned_id');
            $table->unsignedBigInteger('approval_id')->nullable();
            $table->integer('quantity');
            $table->integer('quantity_to_remove')->nullable();
            $table->integer('current_quantity')->nullable();
            $table->integer('reorder_threshold');
            $table->string('qr_code');
            $table->text('remarks');
            $table->integer('item_quantity_just_checked_out')->default(0);
            $table->unsignedBigInteger('request_id')->nullable();
            $table->boolean('active_status')->default(true);
            $table->boolean('deleted_status')->default(true);
            $table->timestamps();

            $table->foreign('item_id')->references('id')->on('items');
            $table->foreign('user_assigned_id')->references('id')->on('users');
            $table->foreign('approval_id')->references('id')->on('approvals');
            $table->foreign('request_id')->references('id')->on('request_items');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('farm_inventories');
    }
};
