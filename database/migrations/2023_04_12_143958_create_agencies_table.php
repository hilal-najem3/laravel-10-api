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
        Schema::create('agencies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username')->unique();
            $table->string('bio')->nullable();
            $table->boolean('active')->default(true);
            $table->boolean('is_branch')->default(false);
            $table->unsignedBigInteger('type_id');
            $table->unsignedBigInteger('agency_id')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('type_id')->references('id')->on('agency_types')->onDelete('cascade');
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
        Schema::table('agencies', function (Blueprint $table) {
            $table->dropForeign(['type_id', 'agency_id']);
        });
        Schema::dropIfExists('agencies');
    }
};
