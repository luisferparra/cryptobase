<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoinsInformationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coins_information', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('coin_id')->nullable()->index()->comment('Coin that will collect data');
            $table->longText('description')->nullable()->comment('Description of the Coin');
            $table->json('links')->nullable()->comment('Information about Links');
            $table->json('images')->nullable()->comment('Images of the coin: thumb, small, large');
            $table->json('scores')->nullable()->comment('Information about Scores and Rankings');
            $table->json('community')->nullable()->comment('Information about Community (facebook...)');
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
        Schema::dropIfExists('coins_information');
    }
}
