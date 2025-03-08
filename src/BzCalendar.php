<?php
namespace ksu\bizcal;

use Generator;

class BzCalendar extends BzYear
{
    public array $periods = [];
    public mixed $use_period;
    public array $offdays;  // 'yyyy-mm-dd'=>'offday name'
    public array $bizdays; // 'yyyy-mm-dd'=>'bizday name'
    public string $priority = 'bizday';
    public BzDay $today;

    public function  setToday(BzDay $s)
    {

    }

    public function setBizPeriod(mixed $name)
    {
        
    }
    public function  useBizPeriod(mixed $name)
    {
        
    }


    public function  offdays(BzDay $s = null, BzDay $t = null)
    {

    }
    public function  bizdays(BzDay $s = null, BzDay $t = null)
    {
        
    }

    public function  isOffday(BzDay $s = null)
    {

    }
    public function  isBizday(BzDay $s = null)
    {
        
    }

    public function  setOffdays(array $days)
    {

    }
    public function  setBizdays(array $days)
    {
        
    }

    public function  nextOffday(BzDay $s, int $n)
    {
        
    }

    public function  nextBizday(BzDay $s, int $n)
    {
        
    }



}
