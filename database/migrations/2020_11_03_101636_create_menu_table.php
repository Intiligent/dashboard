<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Kalnoy\Nestedset\NestedSet;
use App\Models\Menu;

class CreateMenuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->increments('id');
            NestedSet::columns($table);
            $table->string('name');
            $table->text('value')->nullable();
            $table->enum('type', Menu::TYPES)->default(Menu::TYPE_URI);
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
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menu_items');
    }
}
