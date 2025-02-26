<?php
namespace ksu\bizcal;

class BzDay
{
    const YMD_FORMAT = "%d-%02d-%02d";
    public int $y; 
    public int $m;
    public int $d;
    public int $w;

    function __construct(int $year, int $month, int $day)
    {
        $t = mktime(0, 0, 0, $month, $day, $year);        
        $this->y = date('Y', $t);
        $this->m = date('n', $t);
        $this->d = date('d', $t);
        $this->w = date('w', $t);
    }

    public static function createFromArray(array $arr): BzDay
    {
        return new BzDay($arr[0], $arr[1], $arr[2]);
    }
   
    public static function createFromString(string $ymd): BzDay
    {
        $arr = explode('-', $ymd);
        return self::createFromArray($arr);
    }
   
    public function next(int $n = 1): BzDay
    {
        return new BzDay($this->y, $this->m, $this->d + $n);
    }

    /**
     * check if $this day is equal to $other day
     */
    public function eq (BzDay $other): bool
    {
        return ($this->y == $other->y and $this->m == $other->m and $this->d == $other->d);
    }

    /**
     * check if $this day is less than or equal to $other day
     */
    public function leq (BzDay $other): bool
    {
        if ($this->y < $other->y) return true;
        if ($this->y == $other->y and $this->m < $other->m) return true;
        if ($this->y == $other->y and $this->m == $other->m and $this->d <= $other->d) return true;
        return false;
    }

    /**
     * check if $this day is between $day1 and $day2
     */
    public function between(BzDay $day1, BzDay $day2): bool
    {
        return $day1->leq($this) and $this->leq($day2);
    }

    /**
     * check if there is exactly one day between $this and $other day
     */
    public function sandwich(BzDay $other): mixed
    {
        if  ($other->eq($this->next(2))) return $this->next();
        return false;
    }

    public static function toDate($y, $m, $d)
    {
        return sprintf(self::YMD_FORMAT, $y, $m, $d);
    }
   
    public function __toString()
    {
        return self::toDate($this->y, $this->m, $this->d);
    }
}