<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item', function (Blueprint $table) {
            $table->uuid('iid');
            $table->uuid('nid');
            $table->boolean('checked');
            $table->longText('content');
            $table->integer('_constructedStringLength');
            $table->integer('read');
            $table->primary('iid');
            $table->foreign('nid')->references('nid')->on('note');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item');
    }
}
