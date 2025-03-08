<?php
namespace ksu\bizcal;

use Generator;

class BzWeek
{
    use RangeXYZ;
    
    public BzDay $startDay; 
    public BzDay $lastDay; 

    // create a week that contains the given `$day` 
    function __construct(BzDay $day)
    {
        $this->startDay = $day->next(- $day->w); // reset to Sunday  
        $this->lastDay = $this->startDay->next(6);
    }
   
    public static function createFromString(string $ymd): BzWeek
    {
        $arr = explode('-', $ymd);
        return new BzWeek(BzDay::createFromArray($arr));
    }

    /** days(): day generator of this month  */
    public function days(): Generator
    {
        for ($day = $this->startDay; $day->leq($this->lastDay); $day=$day->next()){
            yield $day;
        }
    }

}