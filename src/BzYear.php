<?php
namespace ksu\bizcal;

class BzYear
{
    const DATE_FORMAT_Y2Y = "[%s, %s]";
    public int $y;
    public BzMonth $startMonth;
    public BzMonth $lastMonth;

    function __construct(int $year, int $m = 1)
    {
        ($m > 0 and $m < 13) or 
            new \InvalidArgumentException("Month must be btween 1 to 12!");

        $this->y = $year;
        $this->startMonth = new BzMonth($year, $m);
        $this->lastMonth = new BzMonth($year, $m + 11);
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
    function months()
    {
        for ($mon =$this->startMonth; $mon->leq($this->lastMonth); $mon=$mon->next()){
            yield $mon;
        }
    }

    public function weeks()
    {
        $day1 = $this->startMonth->day();
        $d = $this->lastMonth->lastday;
        $day2 = $this->lastMonth->day($d);
        for ($day=$day1->next(- $day1->w); $day->leq($day2); $day=$day->next(7)){
            yield new BzWeek($day);
        } 
    }

    public function __toString()
    {
        return sprintf(self::DATE_FORMAT_Y2Y, $this->startMonth, $this->lastMonth);
    }
}