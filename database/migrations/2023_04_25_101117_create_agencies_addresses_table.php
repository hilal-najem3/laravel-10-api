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
        Schema::create('agencies_addresses', function (Blueprint $table) {
            $table->unsignedBigInteger('agency_id');
            $table->unsignedBigInteger('address_id');

            //FOREIGN KEY
            $table->foreign('agency_id')->references('id')->on('agencies')->onDelete('cascade');
            $table->foreign('address_id')->references('id')->on('addresses')->onDelete('cascade');

            //PRIMARY KEYS
            $table->primary(['agency_id', 'address_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('agencies_addresses', function (Blueprint $table) {
            $table->dropForeign(['agency_id', 'address_id']);
        });
        Schema::dropIfExists('agencies_addresses');

    }
};
