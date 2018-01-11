<?php

namespace App\Console\Commands;

use App\Models\Ticker;

class TrackMacd extends BaseCommand
{
    const SCHEDULE_FREQUENCY = 30 * 60; // In seconds, @see: Kernel->schedule
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
            if ($ticker['macd_time_frames'] == '') {
                continue;
            }
            $data = $this->fetchData($ticker);
            $intervalCount = env('MONOTONIC_INTERVAL_COUNT', 0);
            $lastShortHistograms = array_slice($this->calculateMacdHistograms($data[0]), - $intervalCount, $intervalCount);
            if (time() - $lastShortHistograms[$intervalCount - 1]['time'] < self::SCHEDULE_FREQUENCY) {
                $lastDifferences = array_map(function ($h) { return $h['macd'] - $h['signal']; }, $lastShortHistograms);
                $direction = $this->checkMonotonicity($lastDifferences);
                if ($direction !== null) {
                    $lastLongHistogram = array_slice($this->calculateMacdHistograms($data[1]), - 1, 1)[0];
                    $momentum = $lastLongHistogram['macd'] - $lastLongHistogram['signal'] > 0;
                    $attachments = [[
                        'fallback' => strtoupper($ticker['pair']) . '. '
                            . ($direction ? 'Increasing' : 'Decreasing') . '.',
                        'text' => '*' . strtoupper($ticker['pair']) . '* (_' . ucfirst($ticker['exchange']) . '_). '
                            . 'Direction: ' . ($direction ? '🔼' : '🔽') . '.',
                        'color' => ($momentum == $direction) ? ($direction ? 'good' : 'danger') : 'warning',
                        'mrkdwn_in' => ['text'],
                    ]];
                    $this->sendSlackMessage('', $attachments);
                }
            }
        }
    }

    /**
     * @return boolean|null `null` if not monotonic at all, `true` if monotonically increasing or `false` if monotonically decreasing
     */
    private function checkMonotonicity($interval)
    {
        $monotonicity = null; // Not monotonic
        $count = count($interval);
        if ($count < 2) {
            return null;
        }
        for ($i = 0; $i <= count($interval) - 2; $i++) {
            $m = ($interval[$i + 1] > $interval[$i]) ? true : false;
            $monotonicity = ($i == 0) ? $m : ($monotonicity == $m ? $monotonicity : null);
        }
        return $monotonicity;
    }

    private function fetchData($ticker)
    {
        $timeFrames = explode(Ticker::DELIMITER, $ticker['macd_time_frames']);
        $result = json_decode(file_get_contents(
            'https://api.cryptowat.ch/markets/'
            . $ticker['exchange'] . '/'
            . $ticker['pair'] . '/ohlc'
            . '?periods=' . $timeFrames[0] . ',' . $timeFrames[1]
            . '&before=' . time()
        ), true)['result'];
        return [$result[$timeFrames[0]], $result[$timeFrames[1]]];
    }

    private function calculateMacdHistograms($candles)
    {
        // Initialize
        $fastMultiplier = 2 / (self::FAST_PERIOD + 1);
        $slowMultiplier = 2 / (self::SLOW_PERIOD + 1);
        $signalMultiplier = 2 / (self::SIGNAL_PERIOD + 1);
        // Fast length and Slow length Emas
        $prices = array_map(function ($c) { return ['time' => $c[0], 'value' => $c[4]]; }, $candles); // Get closes
        $fast = $this->calculateSma($prices, self::FAST_PERIOD - 1, self::SLOW_PERIOD - 1);
        $slow = $this->calculateSma($prices, 0, self::SLOW_PERIOD - 1);
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
        $signal = $this->calculateSma($macds, 0, self::SIGNAL_PERIOD - 1);
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

    private function calculateSma($prices, $start, $end)
    {
        $sma = 0;
        for ($i = $start; $i <= $end; $i++) {
            $sma += floatval($prices[$i]['value']);
        }
        return $sma;
    }
}
