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
        return self::createFromArray(self::toArray($ymd));
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

    public static function toArray(string $ymd): array
    {
        [$y, $m, $d] = explode('-', $ymd);
        return [(int)$y, (int)$m, (int)$d];
    }   
    
    public function format(string $fmt='m-d'): string
    {
        $t = mktime(0, 0, 0, $this->m, $this->d, $this->y);
        return date($fmt, $t);
    }

    public function __toString()
    {
        return sprintf(self::YMD_FORMAT, $this->y, $this->m, $this->d);
    }
}