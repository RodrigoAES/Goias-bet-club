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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Attendant::class);
            $table->foreign('attendant_id')->references('id')->on('attendants')->onDelete('CASCADE');
            $table->foreignIdFor(UserCard::class)->nullable();
            $table->foreign('user_card_id')->references('id')->on('user_cards')->onDelete('CASCADE');
            $table->string('attendant_name', 100);
            $table->string('client_name', 100)->nullable();
            $table->integer('client_phone')->nullable();
            $table->string('user_card_code', 6)->nullable();
            $table->string('type', 7);
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
        Schema::dropIfExists('attendances');
    }
};
