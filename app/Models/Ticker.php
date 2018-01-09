<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticker extends Model
{
    const BULLISH = 'Bullish';
    const BEARISH = 'Bearish';
    const THRESHOLD = '8.00';
    const TIME_FRAME = '';

    public function savePrice($price, $priceSentiment)
    {
        $this->price = $price;
        $this->price_sentiment = $priceSentiment;
        $this->save();
    }

    public function comparePrice($price)
    {
        $thisPrice = floatval($this->price);
        $otherPrice = floatval($price);
        if ($thisPrice == 0) {
            return $otherPrice == 0 ? 0 : ($otherPrice > 0 ? INF : - INF);
        }
        return $otherPrice > $thisPrice ? $otherPrice / $thisPrice - 1 : 1 - $thisPrice / $otherPrice;
    }
}
