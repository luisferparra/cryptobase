<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDataContolsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('data_contols', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('coin_id')->nullable()->index()->comment('Coin that will collect data');
            $table->string('title')->nullable()->comment('Admin Text for better knowledge');
            $table->string('slug')->nullable()->comment('Prefix of Data TAbles==symbol');
            $table->timestamps();
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
        Schema::dropIfExists('data_contols');
    }
}
