<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTradeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trade', function (Blueprint $table) {
            $table->id();
            $table->string('index')->nullable();
            $table->decimal('previous_open', 10, 2)->default(0);
            $table->decimal('open', 10, 2)->default(0);
            $table->decimal('high', 10, 2)->default(0);
            $table->decimal('low', 10, 2)->default(0);
            $table->decimal('close', 10, 2)->default(0);
            $table->decimal('gain_loss', 10, 2)->default(0);
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
        Schema::dropIfExists('trade');
    }
}
