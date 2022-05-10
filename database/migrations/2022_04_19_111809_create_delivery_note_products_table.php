<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryNoteProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_note_products', function (Blueprint $table) {
            $table->id();
            $table->integer('invoice_id');
            $table->integer('product_id');
            $table->integer('quantity');
            $table->string('description');
            $table->float('tax')->default('0.00');
            $table->float('discount')->default('0.00');
            $table->float('price')->default('0.00');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('delivery_note_product');
    }
}
