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
        Schema::create('regions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('alpha2Code')->nullable();
            $table->string('alpha3Code')->nullable();
            $table->string('numberCode')->nullable();
            $table->unsignedBigInteger('type_id');
            $table->unsignedBigInteger('region_id')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('type_id')->references('id')->on('region_types')->onDelete('cascade');
            $table->foreign('region_id')->references('id')->on('regions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('regions', function (Blueprint $table) {
            $table->dropForeign(['type_id', 'region_id']);
        });
        Schema::dropIfExists('regions');
    }
};
