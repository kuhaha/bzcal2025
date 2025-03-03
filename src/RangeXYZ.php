<?php
namespace ksu\bizcal;
// X: array, Z: generator
trait RangeXYZ
{
    /**  
      xrange/zrange: returns or generates numbers between start and limit, in given step.
      x/zrange(start:4, limit:12, step:3)  ->  4 7 10
      x/zrange(start:12, lmiit:4, step:2)= x/zrange(start:12, limit:4, step:-2) =  12 10 8 6 4
    */
    // xrange(): returns an array of numbers in the range 
    static function xrange(int $start, int $limit, int $step = 1, bool $randomize = false)
    {
        $sign =  $limit >= $start ? +1 :-1;
        $numbers = range(0, abs($start - $limit), abs($step));
        if ($randomize) shuffle($numbers);
        return array_map(fn(int $i): int => $start + $sign * $i, $numbers);
    }

    // zrange(): generates random samples from a range
    static function zrange(int $start, int $limit, int $step = 1, bool $randomize = false)
    {
        $sign =  $limit >= $start ? +1 :-1;
        $numbers = range(0, abs($start - $limit), abs($step));
        if ($randomize) shuffle($numbers);
        foreach($numbers as $i){
            yield $start +  $sign * $i;
        }
    }

}