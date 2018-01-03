<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('book_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('book_id')->unsigned()->index();
            $table->enum('type', ['price', 'availability', 'source']);
            $table->string('value');
            $table->timestamps();

            $table->foreign('book_id')->references('id')->on('books')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('book_histories');
    }
}
