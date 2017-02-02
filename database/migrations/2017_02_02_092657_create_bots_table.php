<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bots', function (Blueprint $table) {
            $table->bigInteger('id')->unique();
            $table->string('username');
            $table->string('full_name');
            $table->string('profile_picture');
            $table->string('bio');
            $table->string('website');
            $table->string('client_id');
            $table->string('client_secret');
            $table->string('access_token');
            $table->integer('media_count');
            $table->integer('follows_count');
            $table->integer('followed_by_count');
            $table->text('configuration');
            $table->boolean('online')->default(0);
            $table->integer('errors_count');
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
        Schema::drop('bots');
    }
}
