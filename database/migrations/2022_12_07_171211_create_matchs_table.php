<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Models\Championship;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('matchs', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Championship::class);
            $table->foreign('championship_id')->references('id')->on('championships')->onDelete('CASCADE');
            $table->string('group');
            $table->datetime('datetime');
            $table->boolean('finished')->default(false);
            $table->integer('home_score');
            $table->integer('away_score');
            $table->string('home_name');
            $table->string('away_name');
            $table->string('home_flag');
            $table->string('away_flag');
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
        Schema::dropIfExists('matchs');
    }
};
