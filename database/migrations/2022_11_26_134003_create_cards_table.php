<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cards', function (Blueprint $table) {
           $table->id();
           $table->string('name')->nullable();
           $table->string('matchs');
           $table->datetime('endtime');
           $table->integer('price');
           $table->string('championship');
           $table->integer('round')->nullable();
           $table->string('type')->default('common');
           $table->integer('host_percentage');
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
        Schema::dropIfExists('cards');
    }
};
