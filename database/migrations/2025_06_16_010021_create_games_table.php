<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('player1_id')->constrained()->references('id')->on('users')-> onDelete('cascade');
            $table->foreignId('player2_id')->constrained()->references('id')->on('users')->nullable()-> onDelete('cascade');
            $table->enum ('status', ['waiting', 'playing', 'finished'])->default('waiting');
            $table->foreignId('winner_id')->nullable()->constrained('users')->references('id')->onDelete('set null');
            $table->foreignId('current_turn_id')->nullable()->constrained('users')->references('id')->onDelete('set null');


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('games');
    }
};
