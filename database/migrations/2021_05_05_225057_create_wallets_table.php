<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWalletsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('admin_user_id')->nullable()->index()->comment('FK to admin_users');
            $table->unsignedBigInteger('trading_company_id')->nullable()->index()->comment('FK to trading_company');
            
            $table->unsignedBigInteger('coin_id')->nullable()->index()->comment('Coin which user has invested');
            $table->double('quantity',20,10)->default(0)->comment('Quantity Total Owned');
            $table->double('value',50,20)->default(0)->comment('Total owned in €');
            $table->double('value_original',50,20)->default(0)->comment('Total Invested in €');


            $table->boolean('is_active')->default(1)->index();
            $table->dateTime('purchased_last_at')->nullable()->comment('whenever it was purchased');
            $table->timestamps();

            $table->foreign('admin_user_id')->references('id')->on('admin_users')
            ->onDelete('SET NULL')->onUpdate('SET NULL');
            $table->foreign('trading_company_id')->references('id')->on('trading_companys')
            ->onDelete('SET NULL')->onUpdate('SET NULL');
            $table->foreign('coin_id')->references('coin_id')->on('coins_current_values')
            ->onDelete('SET NULL')->onUpdate('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wallets');
    }
}
