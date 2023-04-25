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
        Schema::create('agency_admins', function (Blueprint $table) {
            $table->uuid('user_id');
            $table->unsignedBigInteger('agency_id');

            //FOREIGN KEY
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('agency_id')->references('id')->on('agencies')->onDelete('cascade');

            //PRIMARY KEYS
            $table->primary(['user_id','agency_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('agency_admins', function (Blueprint $table) {
            $table->dropForeign(['user_id', 'agency_id']);
        });
        Schema::dropIfExists('agency_admins');
    }
};
