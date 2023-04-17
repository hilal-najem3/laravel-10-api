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
        Schema::create('currency_conversions', function (Blueprint $table) {
            $table->unsignedBigInteger('agency_id');
            $table->unsignedBigInteger('from');
            $table->unsignedBigInteger('to');
            $table->decimal('ratio', 19, 6)->default(1);
            $table->enum('operation', ['*', '/']);
            $table->dateTime('date_time');
            $table->softDeletes();
            $table->timestamps();

            //FOREIGN KEY
            $table->foreign('agency_id')->references('id')->on('agencies')->onDelete('cascade');
            $table->foreign('from')->references('id')->on('currencies')->onDelete('cascade');
            $table->foreign('to')->references('id')->on('currencies')->onDelete('cascade');

            //PRIMARY KEYS
            $table->primary(['agency_id','from', 'to']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('currency_conversions', function (Blueprint $table) {
            $table->dropForeign(['agency_id','from', 'to']);
        });
        Schema::dropIfExists('currency_conversions');
    }
};
