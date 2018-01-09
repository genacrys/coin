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
            $market = explode(':', $ticker);
            DB::table('tickers')->insert([
                'exchange' => $market[0],
                'pair' => $market[1],
            ]);
        }
    }
}
