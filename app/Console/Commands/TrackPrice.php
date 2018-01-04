<?php

namespace App\Console\Commands;

use App\Models\Ticker;

class TrackPrice extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'track:price';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Track: Price';

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
        $result = json_decode(file_get_contents('https://api.cryptowat.ch/markets/prices'), true)['result'];
        foreach (Ticker::all() as $ticker) {
            $price = $result[strtolower($ticker->exchange) . ':' . strtolower(str_replace('_', '', $ticker->pair))];
            if ($ticker->price !== '') {
                $change = $ticker->comparePrice($price);
                $priceSentiment = $change > 0 ? Ticker::BULLISH : Ticker::BEARISH;
                if (abs($change) * 100 >= floatval($ticker->price_threshold)) { // Percentage
                    if ($priceSentiment != $ticker->price_sentiment) {
                        $text = $ticker->pair . ' (' . $ticker->exchange . '). '
                            . 'Sentiment: ' . $priceSentiment . '. '
                            . 'New price: ' . number_format(floatval($price), 2) . '.';
                        $this->sendSlackMessage($text);
                    }
                    $ticker->savePrice($price, $priceSentiment);
                }
            } else {
                $ticker->savePrice($price, '');
            }
        }
    }
}
