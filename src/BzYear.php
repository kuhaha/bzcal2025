<?php
namespace ksu\bizcal;

use Generator;

class BzYear
{
    const DATE_FORMAT_Y2Y = "[%s, %s]";
    public int $y;
    public BzMonth $startMonth;
    public BzMonth $lastMonth;

    function __construct(int $y, int $m = 1)
    {
        ($m > 0 and $m < 13) or 
            new \InvalidArgumentException("Month must be btween 1 to 12!");

        $this->y = $y;
        $this->startMonth = new BzMonth($y, $m);
        $this->lastMonth = new BzMonth($y, $m + 11);
    }

    function next(int $n = 1): BzYear
    {
        return new BzYear($this->y + $n, $this->startMonth->m);
    }

    function month(int $m = 1): BzMonth
    {  
        ($m > 0 and $m < 13) or 
            new \InvalidArgumentException("Month must be btween 1 to 12!");
        $year = ($m < $this->startMonth->m) ? $this->y + 1 : $this->y;
        return new BzMonth($year, $m);
    }

    /** months(): month generator of this year  */
    function months(): Generator
    {
        for ($mon =$this->startMonth; $mon->leq($this->lastMonth); $mon=$mon->next()){
            yield $mon;
        }
    }

    public function weeks(): Generator
    {
        $day1 = $this->startMonth->day();
        $last = $this->lastMonth->lastday;
        $day2 = $this->lastMonth->day($last);
        for ($day = $day1; $day->leq($day2); $day = $day->next(7)){
            yield new BzWeek($day);
        } 
    }
    public function firstDay() : BzDay 
    {
        return $this->startMonth->day();    
    }
    public function lastDay() : BzDay 
    {
        return $this->lastMonth->day(-1);    
    }
    

    public function days(BzDay $s, BzDay $t) : Generator 
    {
        $s = $s->leq($this->firstDay()) ? $this->firstDay(): $s;
        $t = $this->lastDay()->leq($t) ? $this->lastDay(): $t;
        for ($day=$s; $day->leq($t); $day=$day->next()){
            yield $day;
        }

    }

    public function __toString()
    {
        return sprintf(self::DATE_FORMAT_Y2Y, $this->startMonth, $this->lastMonth);
    }
}