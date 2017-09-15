<?php

namespace App\Console\Commands;

use App\Models\Ticker;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

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
            $save = function ($ticker, $result) {
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
            };
            if ($ticker->opening !== '') {
                $closed = $ticker->compare($result, 'closed');
                $threshold = $ticker->threshold;
                if (abs($closed) * 100 >= $threshold) { // Percentage
                    foreach (User::all() as $user) {
                        Mail::to($user->email)->send(new \App\Mail\Ticker($ticker, $result));
                    }
                    $save($ticker, $result);
                }
            } else {
                $save($ticker, $result);
            }
        }
    }
}
