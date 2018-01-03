<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('imports', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('source_id')->unsigned()->index();
            $table->integer('total')->default(0);
            $table->integer('processed')->default(0);
            $table->integer('skipped')->default(0);
            $table->integer('created')->default(0);
            $table->integer('updated')->default(0);
            $table->integer('removed')->default(0);
            $table->json('params');

            $table->enum('clear', ['none', 'all', 'publishers'])->default('all');

            $table->enum('status', ['started', 'finished', 'failed', 'unknown'])->default('unknown');
            $table->timestamps();

            $table->foreign('source_id')->references('id')->on('sources')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('imports');
    }
}
