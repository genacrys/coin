<?php

namespace App\Console\Commands;

use App\Models\Ticker;
use App\Models\User;
use Illuminate\Console\Command;

class MailTicker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:ticker';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send mail: Ticker';

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
            $data = json_decode(file_get_contents('https://api.kraken.com/0/public/Ticker?pair=' . $ticker->pair), true);
            $flatten = function ($value) {
                return is_array($value) ? implode(Ticker::DELIMITER, $value) : $value;
            };
            $result = array_map($flatten, reset($data['result']));
            if ($ticker->opening !== '') {
                $closed = $ticker->compare($result, 'closed');
                $threshold = $ticker->threshold;
                if (abs($closed) * 100 >= $threshold) { // Percentage
                    $url = env('SLACK_WEBHOOK_URL', '');
                    $text = 'Kraken: ' . $ticker->pair .
                        '. Sentiment: ' . number_format($ticker->compare($result, 'closed') * 100, 2) .
                        '%. New closed: ' . number_format(floatval($result['c']), 2) . '.';
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
                    $this->save($ticker, $result);
                }
            } else {
                $this->save($ticker, $result);
            }
        }
    }

    private function save($ticker, $result)
    {
        $ticker->ask = $result['a'];
        $ticker->bid = $result['b'];
        $ticker->closed = $result['c'];
        $ticker->volume = $result['v'];
        $ticker->price = $result['p'];
        $ticker->trades = $result['t'];
        $ticker->low = $result['l'];
        $ticker->high = $result['h'];
        $ticker->opening = $result['o'];
        $ticker->save();
    }
}
