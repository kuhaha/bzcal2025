<?php
namespace ksu\bizcal;

use Generator;

class BzCalendar extends BzYear
{
    public array $periods;
    public mixed $use_period;
    public array $offdays; // 'yyyy-mm-dd'=>'offday name'
    public array $bizdays; // 'yyyy-mm-dd'=>'bizday name'
    public string $priority = 'bizday';
    public BzDay $today;

    function __construct(int $y, int $m)
    {
        parent::__construct($y, $m);
        $this->offdays = [];
        $this->bizdays = [];
        $this->today = new BzDay($y, $m, 1);
        $this->periods = [];
        $this->use_period = null;
    }
    public function  setToday(BzDay $day)
    {
        $this->today = $day;
    }

    public function setBizPeriod(mixed $name, BzDay $s, BzDay $t)
    {
        $this->periods[$name] = [$s, $t];
    }
    public function  useBizPeriod(mixed $name)
    {
        if (isset($this->periods[$name]))
            $this->use_period = $name;
        return $this;
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
