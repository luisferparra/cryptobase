<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoinsCurrentValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coins_current_values', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('coin_id')->nullable()->index()->comment('Coin that will collect data');
            $table->string('slug')->nullable()->comment('Redundant. Just for Management. Prefix of Data TAbles==symbol');
            $table->double('eur',50,20);
            $table->double('eur_24h_change',50,20)->nullable();
            $table->unsignedBigInteger('last_updated_at')->nullable();
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
        Schema::dropIfExists('coins_current_values');
    }
}
