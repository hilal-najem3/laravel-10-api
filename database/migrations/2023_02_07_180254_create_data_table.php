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
        Schema::create('data', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->longText('value');
            $table->string('description')->nullable();
            $table->unsignedBigInteger('type_id');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('type_id')->references('id')->on('data_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('data', function (Blueprint $table) {
            $table->dropForeign(['type_id']);
        });
        Schema::dropIfExists('data');
    }
};
