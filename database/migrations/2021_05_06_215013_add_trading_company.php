<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTradingCompany extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wallet_investments', function (Blueprint $table) {
            $table->unsignedBigInteger('trading_company_id')->nullable()->index()
            ->after('wallet_id')
            ->comment('FK to trading_company');
            $table->foreign('trading_company_id')->references('id')->on('trading_companys')
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
        Schema::table('wallet_investments', function (Blueprint $table) {
            $table->dropColumn('trading_company_id');
        });
    }
}
