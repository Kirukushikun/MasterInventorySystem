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
        Schema::create('item_lists', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('request_item_id');
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('uom_id');
            $table->integer('item_quantity');
            $table->integer('item_released_quantity')->default(0);
            $table->integer('item_partially_release_quantity')->nullable()->default(null);
            $table->boolean('active_status')->default(true);
            $table->boolean('deleted_status')->default(true);
            $table->timestamps();

            $table->foreign('request_item_id')->references('id')->on('request_items');
            $table->foreign('item_id')->references('id')->on('item_names');
            $table->foreign('uom_id')->references('id')->on('unit_of_measurements');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_lists');
    }
};
