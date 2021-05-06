<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Facades\DB;

use Carbon\Carbon;

class CreateTradingCompanysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trading_companys', function (Blueprint $table) {
            $table->id();
            $table->string('name',255)->index()->comment('Name of the Trading Company');
            $table->boolean('is_api_trader')->default(0)->comment('If this app can operate using Trader Apis (Future Versions)');
            $table->boolean('is_active')->default(1)->comment('If the company is active for being selected');
            $table->timestamps();
        });

        DB::table('trading_companys')->insert([
            ['id' => 1, "name"=>"CoinBase","is_api_trader"=>0,"is_active"=>1,"created_at"=>Carbon::now()->format("Y-m-d H:i:s")],
            ['id' => 2, "name"=>"Crypto.com","is_api_trader"=>0,"is_active"=>1,"created_at"=>Carbon::now()->format("Y-m-d H:i:s")]

        ]);
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trading_companys');
    }
}
