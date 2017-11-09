<?php

namespace App\Console\Commands;

use App\Models\Ticker;
use App\Models\User;
use Illuminate\Console\Command;

class TrackTicker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'track:ticker';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Track: Ticker';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        foreach (Ticker::all() as $ticker) {
            $price = '0';
            if ($ticker->exchange == 'Kraken') {
                $data = json_decode(file_get_contents('https://api.kraken.com/0/public/Ticker?pair=' . $ticker->pair), true);
                $price = reset($data['result'])['c'][0];
            } else if ($ticker->exchange == 'Bitfinex') {
                $data = json_decode(file_get_contents('https://api.bitfinex.com/v2/ticker/t' . $ticker->pair), true);
                $price = $data[6];
            } else if ($ticker->exchange == 'Bitflyer') {
                $data = json_decode(file_get_contents('https://lightning.bitflyer.jp/api/market/statistics?account_id=&lang=en&v=1'), true);
                $price = $data[$ticker->pair]['LTP'];
            } else {
                continue;
            }
            if ($ticker->price !== '') {
                $change = $ticker->comparePrice($price);
                if (abs($change) * 100 >= floatval($ticker->threshold)) { // Percentage
                    $url = env('SLACK_WEBHOOK_URL', '');
                    $text = $ticker->exchange . ': ' . $ticker->pair .
                        '. Sentiment: ' . number_format($change * 100, 2) .
                        '%. New price: ' . number_format(floatval($price), 2) . '.';
                    $message = json_encode(array(
                        'text' => $text
                    ));
                    $curl = curl_init($url);
                    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $message);
                    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                        'Content-Type: application/json',
                        'Content-Length: ' . strlen($message)
                    ));
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_exec($curl);
                    curl_close($curl);
                    $this->save($ticker, $price);
                }
            } else {
                $this->save($ticker, $price);
            }
        }
    }

    private function save($ticker, $price)
    {
        $ticker->price = $price;
        $ticker->save();
    }
}
