<?php
namespace ksu\bizcal;

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

    public static function create(int $year, int $month, int $day): BzWeek
    {
        $day = new BzDay( $year, $month, $day); 
        return new BzWeek($day);
    }

    public static function createFromArray(array $arr): BzWeek
    {
        return new BzWeek($arr[0], $arr[1], $arr[2]);
    }
   
    public static function createFromString(string $ymd): BzWeek
    {
        $arr = explode('-', $ymd);
        return self::createFromArray($arr);
    }
}