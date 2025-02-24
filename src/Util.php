<?php

class Util
{
    static function xrange(int $start, int $limit, int $step)
    {
        $sign =  $limit >= $start ? +1 :-1;
        for ($i = 0; $i <= abs($start - $limit); $i += abs($step)) {
            yield ($start +  $sign * $i);
        }
    }
}