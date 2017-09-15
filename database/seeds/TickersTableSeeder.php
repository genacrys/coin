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
            'pair' => 'ETHJPY',
            'ask' => '',            
            'bid' => '',            
            'closed' => '',            
            'volume' => '',            
            'price' => '',            
            'trades' => '',            
            'low' => '',            
            'high' => '',            
            'opening' => '',
            'threshold' => 1.50,
        ]);
        DB::table('tickers')->insert([
            'pair' => 'XBTJPY',
            'ask' => '',
            'bid' => '',
            'closed' => '',
            'volume' => '',
            'price' => '',
            'trades' => '',
            'low' => '',
            'high' => '',
            'opening' => '',
            'threshold' => 1.50,
        ]);
    }
}
