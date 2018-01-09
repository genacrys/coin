<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Ticker;

class CreateTickersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('exchange');
            $table->string('pair');
            $table->string('price')->default('');
            $table->string('price_sentiment')->default('');
            $table->string('price_threshold')->default(Ticker::THRESHOLD);
            $table->string('macd_time_frame')->default(Ticker::TIME_FRAME);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tickers');
    }
}
