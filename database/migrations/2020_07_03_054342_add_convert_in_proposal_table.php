<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddConvertInProposalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            'proposals', function (Blueprint $table){
            $table->integer('invoice_is_convert')->default('0')->after('discount_apply');
            $table->integer('delivery_note_is_convert')->default('0')->after('discount_apply');
            $table->integer('converted_invoice_id')->default('0')->after('delivery_note_is_convert');
            $table->integer('converted_delivery_note_id')->default('0')->after('converted_invoice_id');
        }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(
            'proposals', function (Blueprint $table){
            $table->dropColumn('invoice_is_convert');
            $table->dropColumn('delivery_note_is_convert');
            $table->dropColumn('converted_invoice_id');
            $table->dropColumn('converted_delivery_note_id');
        }
        );
    }
}
