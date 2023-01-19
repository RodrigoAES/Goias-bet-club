<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Models\Attendant;
use App\Models\UserCard;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Attendant::class)->nullable();
            $table->foreign('attendant_id')->references('id')->on('attendants')->onDelete('CASCADE');
            $table->foreignIdFor(UserCard::class);
            $table->foreign('user_card_id')->references('id')->on('user_cards')->onDelete('CASCADE');
            $table->string('name');
            $table->string('code');
            $table->string('phone');
            $table->integer('value');
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
        Schema::dropIfExists('sales');
    }
};
