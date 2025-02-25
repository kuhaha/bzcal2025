<?php
namespace ksu\bizcal;

class BzYear
{
    const YR_FORMAT = "[%s, %s]";
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

    function month(int $m): BzMonth
    {  
        ($m > 0 and $m < 13) or 
            new \InvalidArgumentException("Month must be btween 1 to 12!");
        $year = ($m < $this->startMonth->m) ? $this->y + 1 : $this->y;
        return new BzMonth($year, $m);
    }

    function next(int $n = 1): BzYear
    {
        return new BzYear($this->y + $n, $this->startMonth->m);
    }

    public function __toString()
    {
        return sprintf(self::YR_FORMAT, $this->startMonth, $this->lastMonth);
    }
}