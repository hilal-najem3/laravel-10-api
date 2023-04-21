<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agency_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('plan_id');
            $table->unsignedBigInteger('agency_id');
            $table->dateTime('starting_date');
            $table->dateTime('ending_date');
            $table->boolean('active')->default(true);
            $table->mediumText('note')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('plan_id')->references('id')->on('plans')->onDelete('cascade');
            $table->foreign('agency_id')->references('id')->on('agencies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('agency_plans', function (Blueprint $table) {
            $table->dropForeign(['plan_id', 'agency_id']);
        });
        Schema::dropIfExists('agency_plans');
    }
};
