<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWalletInvestmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wallet_investments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('wallet_id')->nullable()->index()->comment('Wallet the investment belongs to');
            $table->enum('operation_type',config('cryptbase.operations_type'))->default('PURCHASE')->index();
            $table->double('quantity',20,10)->default(0)->comment('Quantity Total Owned');
            $table->double('value',50,20)->default(0)->comment('Total owned in €');
            $table->double('total_amount',50,20)->default(0)->comment('Total Operation amount in €');
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
        Schema::dropIfExists('wallet_investments');
    }
}
