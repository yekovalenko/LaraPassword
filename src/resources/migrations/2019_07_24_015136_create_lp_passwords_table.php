<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLpPasswordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lp_passwords', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('category_id')->default(0);
            $table->string('label')->default('');
            $table->text('login')->nullable();
            $table->text('password')->nullable();
            $table->text('url')->nullable();
            $table->text('description')->nullable();
            $table->text('metadata')->nullable();
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
        Schema::dropIfExists('lp_passwords');
    }
}
