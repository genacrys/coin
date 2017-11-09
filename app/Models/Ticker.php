<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticker extends Model
{
    public function comparePrice($price)
    {
       $thisPrice = floatval($this->price);
       $otherPrice = floatval($price);
       return $otherPrice > $thisPrice ? $otherPrice / $thisPrice - 1 : 1 - $thisPrice / $otherPrice;
    }
}
