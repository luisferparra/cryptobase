<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoinsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coins', function (Blueprint $table) {
            $table->id();
            $table->string('coin_cod')->index()->unique()->comment('ANSI code of the coin');
            $table->string('symbol')->nullable()->comment('Ansi Symbol of the cryptocoin');
            $table->string('name',255)->index()->comment('Name of the cc');
            $table->boolean('is_active')->index()->default(0)->comment('If the coin is available for using at the platform');
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
        Schema::dropIfExists('coins');
    }
}
