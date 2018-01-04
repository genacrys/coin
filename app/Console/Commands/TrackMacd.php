<?php

namespace App\Console\Commands;

use App\Models\Ticker;

class TrackMacd extends BaseCommand
{
    const SCHEDULE_FREQUENCY = 5 * 60; // @see: Kernel->schedule
    const FAST_PERIOD = 12; // Fast length period
    const SLOW_PERIOD = 26; // Slow length period
    const SIGNAL_PERIOD = 9; // Signal smoothing period

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'track:macd';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Track: Macd';

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
            // Get only last 3 histograms
            $lastHistograms = array_slice($this->calculateMacdHistograms($ticker), -3, 3);
            $lastDifferences = array_map(function ($h) { return $h['macd'] - $h['signal']; }, $lastHistograms);
            if (time() - $lastHistograms[2]['time'] < self::SCHEDULE_FREQUENCY) {
                $isCrossover = $lastDifferences[1] * $lastDifferences[2] < 0;
                $isDivergence = ($lastDifferences[0] * $lastDifferences[1] < 0)
                    && ($lastDifferences[1] * $lastDifferences[2] > 0)
                    && (abs($lastDifferences[1]) < abs($lastDifferences[2]));
                if ($isCrossover || $isDivergence) {
                    $momentum = $lastHistograms[2]['macd'] > 0; // Upside or Downside
                    $direction = $lastDifferences[2] > 0; // Increasing or Decreasing
                    $attachments = [[
                        'fallback' => $ticker->pair . '. '
                            . ($isCrossover ? 'Crossover' : 'Divergence') . '. '
                            . ($momentum ? 'Upside' : 'Downside') . '. '
                            . ($direction ? 'Increasing' : 'Decreasing') . '.',
                        'text' => '*' . $ticker->pair . '* (_' . $ticker->exchange . '_). '
                            . 'Form: _' . ($isCrossover ? 'Crossover' : 'Divergence') . '_. '
                            . 'Momentum: ' . ($momentum ? '🔼' : '🔽') . '. '
                            . 'Direction: ' . ($direction ? '🔼' : '🔽') . '.',
                        'color' => ($momentum != $direction) ? ($direction ? 'good' : 'danger') : 'warning',
                        'mrkdwn_in' => ['text'],
                    ]];
                    $this->sendSlackMessage('', $attachments);
                }
            }
        }
    }

    private function calculateMacdHistograms($ticker)
    {
        // Get data
        $candles = json_decode(file_get_contents(
            'https://api.cryptowat.ch/markets/'
            . strtolower($ticker->exchange) . '/'
            . strtolower(str_replace('_', '', $ticker->pair)) . '/ohlc'
            . '?periods=' . $ticker->macd_time_frame
            . '&before=' . time()
        ), true)['result'][$ticker->macd_time_frame];
        // Initialize
        $fastMultiplier = 2 / (self::FAST_PERIOD + 1);
        $slowMultiplier = 2 / (self::SLOW_PERIOD + 1);
        $signalMultiplier = 2 / (self::SIGNAL_PERIOD + 1);
        $calculateSma = function($prices, $start, $end) {
            $sma = 0;
            for ($i = $start; $i <= $end; $i++) {
                $sma += floatval($prices[$i]['value']);
            }
            return $sma;
        };
        // Fast length and Slow length Emas
        $prices = array_map(function ($c) { return ['time' => $c[0], 'value' => $c[4]]; }, $candles); // Get closes
        $fast = $calculateSma($prices, self::FAST_PERIOD - 1, self::SLOW_PERIOD - 1);
        $slow = $calculateSma($prices, 0, self::SLOW_PERIOD - 1);
        $macds = [];
        foreach (array_slice($prices, self::SLOW_PERIOD) as $price) {
            $fast = ($price['value'] - $fast) * $fastMultiplier + $fast;
            $slow = ($price['value'] - $slow) * $slowMultiplier + $slow;
            $macds[] = [
                'time' => $price['time'],
                'value' => $fast - $slow,
            ];
        }
        // Signal smoothing Ema
        $signal = $calculateSma($macds, 0, self::SIGNAL_PERIOD - 1);
        $macdHistograms = [];
        foreach (array_slice($macds, self::SIGNAL_PERIOD) as $macd) {
            $signal = ($macd['value'] - $signal) * $signalMultiplier + $signal;
            $macdHistograms[] = [
                'time' => $macd['time'],
                'macd' => $macd['value'],
                'signal' => $signal,
            ];
        }
        // Return
        return $macdHistograms;
    }
}
