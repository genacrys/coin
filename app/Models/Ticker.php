<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticker extends Model
{
    const BULLISH = 'Bullish';
    const BEARISH = 'Bearish';

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
       return $otherPrice > $thisPrice ? $otherPrice / $thisPrice - 1 : 1 - $thisPrice / $otherPrice;
    }
}
