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
        Schema::create('items', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('subcategory_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('item_name_id');
            $table->unsignedBigInteger('location_id');
            $table->unsignedBigInteger('user_id');
            $table->text('model_number')->nullable();
            $table->string('item_number')->nullable();
            $table->string('order_number')->nullable();
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->unsignedBigInteger('uom_id');
            $table->integer('quantity');
            $table->integer('current_quantity')->nullable();
            $table->integer('reorder_threshold');
            $table->integer('average_consumption')->nullable();
            $table->integer('average_lead_time')->nullable();
            $table->string('time_unit')->nullable();
            // $table->integer('safety_stock')->nullable();
            $table->date('purchase_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->double('purchase_cost')->nullable();
            $table->string('remarks')->nullable();
            $table->string('qr_code');
            $table->string('item_image')->nullable();
            $table->text('item_image_path')->nullable();
            $table->unsignedBigInteger('approval_id')->nullable();
            $table->boolean('active_status')->default(true);
            $table->boolean('deleted_status')->default(true);

            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('categories');
            $table->foreign('subcategory_id')->references('id')->on('sub_categories');
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('item_name_id')->references('id')->on('item_names');
            $table->foreign('location_id')->references('id')->on('locations');
            $table->foreign('supplier_id')->references('id')->on('suppliers');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('uom_id')->references('id')->on('unit_of_measurements');
            $table->foreign('approval_id')->references('id')->on('approvals');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
