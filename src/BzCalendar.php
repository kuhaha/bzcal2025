<?php
namespace ksu\bizcal;

use Generator;

class BzCalendar extends BzYear
{
    const BIZDAYNAME = '営業日';
    const OFFDAYNAME = '休業日';
    public array $periods;
    public mixed $use_period;
    public array $offdays; // 'yyyy-mm-dd'=>'offday name'
    public array $bizdays; // 'yyyy-mm-dd'=>'bizday name'
    // default is bizday unless otherwise defined when $priority = 'bizday' 
    // default is offday unless otherwise defined when $priority = 'offday'
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

    public function  offDays(?BzDay $s, ?BzDay $t)
    {
        return $this->filterDays('offday', $s, $t);
    }
    public function  bizDays(?BzDay $s, ?BzDay $t)
    {
        return $this->filterDays('bizday', $s, $t);
    }
    public function  filterDays(string $name, ?BzDay $s, ?BzDay $t): array
    {
        $s = $s ?? $this->firstDay();
        $t = $t ?? $this->lastDay();
        $days = [];
        foreach ($this->days($s, $t) as $day){   
            $bzday = $name=='bizday' ? $this->isBizday($day) : $this->isOffday();
           if ($bzday){
                $days["$day"] = $bzday;
           }
        }
        return $days;
    }

    public function  isBizday(?BzDay $day): bool|string
    { 
        $day = $day ?? $this->today;
        $ymd = strval($day);
        $offday = $this->offdays[$ymd] ?? '';
        $bizday = $this->bizdays[$ymd] ?? '';
        if ($this->priority=="bizday"){
            if ($bizday) return $bizday;
            if (!$offday) return self::BIZDAYNAME;
        }else{
            if ($offday) return false;
            if ($bizday) return $bizday;
        } 
        return false;
    }
    public function  isOffday(?BzDay $day): bool|string
    { 
        $day = $day ?? $this->today;
        $ymd = strval($day);
        $offday = $this->offdays[$ymd] ?? '';
        $bizday = $this->bizdays[$ymd] ?? '';
        if ($this->priority=="bizday"){
            if ($bizday) return false;
            if ($offday) return $offday;
        }else{
            if ($offday) return $offday;
            if (!$bizday) return self::OFFDAYNAME;
        } 
        return false;
    }

    public function  setOffdays(array $days)
    {

        return $this;
    }
    public function  setBizdays(array $days)
    {
        
        return $this;        
    }

    public function  nextOffday(BzDay $s, int $n)
    {
        
    }

    public function  nextBizday(BzDay $s, int $n)
    {
        
    }
}
