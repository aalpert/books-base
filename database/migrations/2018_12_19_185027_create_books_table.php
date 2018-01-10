<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::enableForeignKeyConstraints();
        Schema::create('books', function (Blueprint $table) {
            $table->increments('id')->unique();
            // relations
//            $table->integer('source_id')->unsigned()->index();
//            $table->integer('publisher_id')->unsigned()->index();
            $table->integer('series_id')->nullable()->unsigned()->index();
            //fields
            $table->string('title')->index();
            $table->text('description')->nullable();
            $table->string('sku')->index();
            $table->string('image')->nullable();
            $table->enum('availability', ['A', 'NVN', 'Z', 'AN', 'SB'])->nullable();
            // Book-specific
            $table->string('isbn')->index();
            $table->string('format')->nullable();
            $table->string('bookbinding')->nullable();
            $table->smallInteger('year')->nullable();
            $table->smallInteger('pages')->nullable();
            $table->string('additional_notes')->nullable();

            $table->timestamps();

//            $table->foreign('publisher_id')->references('id')->on('publishers')->onDelete('cascade');
//            $table->foreign('source_id')->references('id')->on('sources')->onDelete('cascade');
            $table->foreign('series_id')->references('id')->on('series')->onDelete('cascade');

        });

        // Pivots
        Schema::create('book_category', function (Blueprint $table) {
            $table->integer('book_id')->unsigned()->index();
            $table->integer('category_id')->unsigned()->index();

            $table->primary(['book_id', 'category_id']);
            $table->foreign('book_id')->references('id')->on('books')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        });

        Schema::create('book_publisher', function (Blueprint $table) {
            $table->integer('book_id')->unsigned()->index();
            $table->integer('publisher_id')->unsigned()->index();

            $table->primary(['book_id', 'publisher_id']);
            $table->foreign('book_id')->references('id')->on('books')->onDelete('cascade');
            $table->foreign('publisher_id')->references('id')->on('publishers')->onDelete('cascade');
        });

        Schema::create('author_book', function (Blueprint $table) {
            $table->integer('author_id')->unsigned()->index();
            $table->integer('book_id')->unsigned()->index();

            $table->primary(['author_id', 'book_id']);
            $table->foreign('book_id')->references('id')->on('books')->onDelete('cascade');
            $table->foreign('author_id')->references('id')->on('authors')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('books');
        Schema::dropIfExists('book_category');
        Schema::dropIfExists('book_publisher');
        Schema::dropIfExists('author_book');
    }
}
