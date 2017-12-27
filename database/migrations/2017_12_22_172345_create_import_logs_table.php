<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImportLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('import_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('import_id')->unsigned()->index();
            $table->string('title')->index();
            $table->string('isbn')->index();
            $table->string('sku');
            $table->string('publisher')->index();
            $table->string('author')->nullable();
            $table->float('price');
            $table->enum('status', ['created', 'updated', 'deleted']);
            $table->timestamps();

            $table->foreign('import_id')->references('id')->on('imports')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('import_logs');
    }
}
