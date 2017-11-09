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
            'exchange' => 'Kraken',
            'pair' => 'ETHJPY',
            'price' => '',
            'threshold' => '2.00',
        ]);
        DB::table('tickers')->insert([
            'exchange' => 'Kraken',
            'pair' => 'XBTJPY',
            'price' => '',
            'threshold' => '2.00',
        ]);
    }
}
