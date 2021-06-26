<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('code');
            $table->string('icon')->nullable();
            $table->boolean('active')->default(1);
            $table->integer('order')->nullable()->default(1);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('settings_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('group_id')->unsigned();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('type')->default('input-text');
            $table->string('icon')->nullable();
            $table->boolean('active')->default(1);
            $table->integer('order')->default(1);
            $table->boolean('is_system')->default(0);
            $table->text('data')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('group_id')->references('id')->on('settings_groups')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings_items');
        Schema::dropIfExists('settings_groups');
    }
}
