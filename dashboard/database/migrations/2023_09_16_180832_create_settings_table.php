<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Kalnoy\Nestedset\NestedSet;
use Dashboard\Models\Setting;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->increments('id');
            NestedSet::columns($table);
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('title');
            $table->string('description')->nullable();
            $table->string('type')->default('input-text');
            $table->string('icon')->nullable();
            $table->json('properties')->nullable();
            $table->boolean('active')->default(1);
            $table->integer('order')->default(1);
            $table->boolean('is_system')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
