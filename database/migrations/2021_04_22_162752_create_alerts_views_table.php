<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlertsViewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alerts_views', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('alert_id')->index()->nullable()->comment('FK to the table Alerts');
            $table->unsignedInteger('admin_user_id')->index()->nullable()->comment('FK to the table admin_user');
            $table->boolean('viewed')->default(0)->index()->comment('If the user has viewed the Alert');
            $table->timestamp('viewed_at')->nullable()->comment('When the user viewed the alert');
            $table->timestamps();

            $table->foreign('alert_id')->references('id')->on('alerts')->onDelete('SET NULL')->onUpdate('SET NULL');
            $table->foreign('admin_user_id')->references('id')->on('admin_users')->onDelete('SET NULL')->onUpdate('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('alerts_views');
    }
}
