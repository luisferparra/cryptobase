<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('coins_current_values', function (Blueprint $table) {
            $table->foreign('coin_id')->references('id')->on('coins')->onDelete('SET NULL')->onUpdate('SET NULL');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('coins_current_values', function (Blueprint $table) {
            //
        });
    }
}
