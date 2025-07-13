<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Kalnoy\Nestedset\NestedSet;
use App\Enums\MenuType;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('navigation', function (Blueprint $table) {
            $table->increments('id');
            NestedSet::columns($table);
            $table->string('name');
            $table->text('value')->nullable();
            $table->enum('type', MenuType::toArray())->default(MenuType::URI);
            $table->string('code')->nullable();
            $table->text('attribute')->nullable();
            $table->string('icon')->nullable();
            $table->boolean('is_system')->default(0);
            $table->boolean('active')->default(1);
            $table->integer('order')->nullable()->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('navigation');
    }
};
