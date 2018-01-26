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
        $tickers = json_decode(File::get('database/data/tickers.json'));
        foreach ($tickers as $ticker) {
            DB::table('tickers')->insert([
                'exchange' => $ticker[0],
                'pair' => $ticker[1],
                'price_variation' => $ticker[2],
                'macd_time_frames' => $ticker[3],
            ]);
        }
    }
}
