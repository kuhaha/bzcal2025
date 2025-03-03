<?php
namespace ksu\bizcal;

class BzMonth 
{
    const DATE_FORMAT_YM = "%d-%02d";
    public int $y; 
    public int $m;
    public int $firstwday;
    public int $lastday;    

    function __construct(int $year, int $month)
    {
        $t = mktime(0, 0, 0, $month, 1, $year);        
        $this->y = date('Y', $t);
        $this->m = date('n', $t);
        $this->lastday = date('t', $t);
        $this->firstwday = date('w', $t);
    }

    public static function createFromArray(array $arr): BzMonth
    {
        return new BzMonth($arr[0], $arr[1]);
    }
   
    public static function createFromString(string $ym): BzMonth
    {
        $arr = explode('-', $ym);
        return self::createFromArray($arr);
    }
  
    public function next(int $n = 1): BzMonth
    {
        return new BzMonth($this->y, $this->m + $n);
    }

    public function diff(BzMonth $other): int
    {
        return ($this->y - $other->y) * 12 + $this->m - $other->m; 
    }

    public function leq(BzMonth $other): bool
    {
        return $this->diff($other) <= 0;
    }

    public function week(int $n = 1): BzWeek
    {
        $w = $this->firstwday;
        $day = $this->day()->next(- $w);// first sunday
        return new BzWeek($day->next(7*$n-7));
    }

    public function weeks()
    {
        foreach (range(1,5) as $n){
            $week = $this->week($n);
            $lastday = $this->day($this->lastday);
            if ($week->startDay->leq($lastday))
                yield $week; 
        }        
    }

    public function day(int $d = 1): BzDay
    {
        return new BzDay($this->y, $this->m, $d);
    }

    /** days(): day generator of this month  */
    public function days()
    {
        for ($d = 1; $d <= $this->lastday; $d++){
            yield $this->day($d);
        }
    }

    /**
     * d2w(), return the day of week for the $day of the month   
     */
    public function d2w(int $day): int
    {
        return ( $this->firstwday + $day - 1)  % 7 ;
    }
  
    /**
     * w2d(), return the day of month for the $n'th day of week ($w) 
     **/
    public function w2d(int $w, int $n): int
    {
        $n = $w >= $this->firstwday ? $n - 1 : $n; 
        $d = $n * 7 + $w - $this->firstwday + 1;
        return ($d <= $this->lastday) ? $d : -1; 
    }

    /**
     * w2days(), return all days of the $n'th day of week ($w) for each $n in array $ns 
     **/
    public function w2days(int $w, int ...$ns): array
    {
        $ns = empty($ns) ? range(1,5) : $ns;        
        $days = [];
        foreach ($ns as $n) {
            $d = $this->w2d($w, $n);
            if ($d > 0) $days[] = $d;
        }
        return $days;
    }

    public function __toString(): string
    {
        return  sprintf(self::DATE_FORMAT_YM, $this->y, $this->m);
    }
}