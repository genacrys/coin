<?php

use Illuminate\Database\Seeder;

class TickersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tickers')->insert([
            'exchange' => 'Bitflyer',
            'pair' => 'BTCFXJPY',
            'price_threshold' => '2.00',
            'macd_time_frame' => '7200',
        ]);
        DB::table('tickers')->insert([
            'exchange' => 'Bitfinex',
            'pair' => 'BTCUSD',
            'price_threshold' => '2.00',
            'macd_time_frame' => '7200',
        ]);
        DB::table('tickers')->insert([
            'exchange' => 'Bitfinex',
            'pair' => 'ETHUSD',
            'price_threshold' => '2.00',
            'macd_time_frame' => '7200',
        ]);
        DB::table('tickers')->insert([
            'exchange' => 'Bitfinex',
            'pair' => 'BCHUSD',
            'price_threshold' => '2.00',
            'macd_time_frame' => '7200',
        ]);
        DB::table('tickers')->insert([
            'exchange' => 'Bitfinex',
            'pair' => 'LTCUSD',
            'price_threshold' => '2.00',
            'macd_time_frame' => '7200',
        ]);
        DB::table('tickers')->insert([
            'exchange' => 'Bitfinex',
            'pair' => 'XRPUSD',
            'price_threshold' => '2.00',
            'macd_time_frame' => '7200',
        ]);
        DB::table('tickers')->insert([
            'exchange' => 'Bitfinex',
            'pair' => 'XMRUSD',
            'price_threshold' => '2.00',
            'macd_time_frame' => '7200',
        ]);
        DB::table('tickers')->insert([
            'exchange' => 'Bitfinex',
            'pair' => 'IOTUSD',
            'price_threshold' => '2.00',
            'macd_time_frame' => '7200',
        ]);
    }
}
