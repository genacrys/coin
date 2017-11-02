<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticker extends Model
{
    const DELIMITER = ', ';

    public function compare(array $other, $field)
    {
       $thisValue = floatval(explode(self::DELIMITER, $this[$field])[0]);
       $otherValue = floatval(explode(self::DELIMITER, $other[$field[0]])[0]);
       return $otherValue > $thisValue ? $otherValue / $thisValue - 1 : 1 - $thisValue / $otherValue;
    }
}
