<?php
namespace ksu\bizcal;

use ksu\bizcal\BzYear;
use ksu\bizcal\BzMonth;
use ksu\bizcal\BzDay;
use ksu\bizcal\HolidayDef as BzDef;

class Holiday
{
    private array $holidays = [];
    public BzYear $bzyear;

     public function __construct(int $year, int $first_month)
    {
        $this->bzyear = new BzYear($year, $first_month);
    }

    public static function createFromBzYear(BzYear $bz){
        return new Holiday($bz->y, $bz->startMonth->m);
    } 
    
    public function holidays(int $month = 0)
    {
        if ($month > 0){
            $callback = function ($ymd) use ($month){
                [, $m, ] = explode('-', $ymd);
                return $m == $month;
            };
            return array_filter($this->holidays, $callback, ARRAY_FILTER_USE_KEY);
        }
        return $this->holidays;
    }

    public function parse(array $holiday_defs = [], array $holiday_names=[]): self
    {
        $this->holidays = []; //reset holodays
        $year = $this->bzyear->startMonth->y;
        if ($year >= BzDef::HOLIDAY_SINCE){
            $holiday_defs = $holiday_defs ? $holiday_defs : BzDef::HOLIDAY_DEF;
            $this->parseYear($holiday_defs)->suppHolidays()->bridgeHolidays();
        }
        return $this;
    }

    private function parseYear(array $holiday_defs = []): self
    {
        $holiday_defs = $holiday_defs ? $holiday_defs : BzDef::HOLIDAY_DEF; 
        foreach ($this->bzyear->months() as $mon){
            $y = $mon->y;
            $m = $mon->m;
            if ( !isset($holiday_defs[$m])) continue;
            $month_holidays = $this->parseMonth($y, $m, $holiday_defs[$m]);
            $this->holidays = array_merge($this->holidays, $month_holidays);
        }
        ksort($this->holidays);
        return $this;
    }

    /** 
     * parse holiday definitions and return an array of holidays for this month 
     **/
    private function parseMonth(int $year, int $month, array $month_defs): array
    {
        $holidays = [];
        foreach ($month_defs as $m_def){
            $id = $m_def[BzDef::_ID];
            foreach ($m_def[BzDef::_DAYS] ?? [$m_def] as $def){ 
                if ($this->validate($def, $year) === false) continue;                
                $day = $this->parseDay($year, $month, $def);                   
                if ($day > 0){ 
                    $date = BzDay::toString($year, $month, $day);
                    $holidays[$date] = BzDef::HOLIDAY_NAME[$id];
                }                        
            }
        }
        return $holidays;
    }

    /** 
     * parse day definition and calculate a holiday 
     **/
    private function parseDay(int $year, int $month, mixed $def): int 
    {
        $biz_month = new BzMonth($year, $month);
        if (isset($def[BzDef::_DAY]) and is_integer($def[BzDef::_DAY])) return $def[BzDef::_DAY];
        if (isset($def[BzDef::_DOW]) and is_array($def[BzDef::_DOW])) 
            return $biz_month->w2d($def[BzDef::_DOW][0], $def[BzDef::_DOW][1]);
        if (isset($def[BzDef::_FNC]) and $def[BzDef::_FNC]=='equinox')
            return $this->equinox($month);
        return -1; 
    }


    private function suppHolidays(): self
    {
        $ex_holidays = [];  // 振替休日：substitute holidays for holidays on Sunday
        foreach (array_keys($this->holidays) as $date){
            $day = BzDay::createFromString($date);
            if ($day->w > 0) continue;
            while($this->isHoliday($day)){
                $day = $day->next();
            }// "$day" : calls __toString(),for 'yyyy-mm-dd' formatted string
            $ex_holidays["$day"] = BzDef::HOLIDAY_NAME['SubstituteHoliday'];       
        }
        $this->holidays = array_merge($this->holidays, $ex_holidays);
        ksort($this->holidays);
        return $this;
    }

    private function bridgeHolidays(): self
    {
        $ex_holidays = []; // 国民の祝日： bridge holiday sandwiched by two holidays 
        $days = array_keys($this->holidays);
        for ($i=0; $i < count($days)-1; $i++){
            $day1 = BzDay::createFromString($days[$i]);
            $day2 = BzDay::createFromString($days[$i+1]);
            $day = $day1->sandwich($day2); 
            if ($day){
                $ex_holidays["$day"] = BzDef::HOLIDAY_NAME['BridgeHoliday'];
            }
        }
        $this->holidays = array_merge($this->holidays, $ex_holidays);
        ksort($this->holidays);
        return $this;
    }

    
    /** caculate spring and autumn equinox days  
     *  valid for years between 1851 and 2150. return -1 otherwise   
     **/
    private function equinox(int $m) : int
    {
        $year = $this->bzyear->y;
        $year = ($m==3 and 3 < $this->bzyear->startMonth->m) ? $year + 1 : $year;
        if (!$this->between($year, [1851, 2150])){
            return -1;
        }
        $delta = [20.8431, 23.2488]; // default for [1980, 2099]
        if (self::between($year, [1851, 1899]))
            $delta = [19.8277, 22.2588];
        if (self::between($year, [1900, 1979]))
            $delta = [20.8357, 23.2588];
        if (self::between($year, [2100, 2150]))
            $delta = [21.8510, 24.2488];
        
        $alpha = ($m==3) ? $delta[0] : $delta[1];
        return (int)floor($alpha + 0.242194 * ($year - 1980) - floor(($year - 1980) / 4));
    } 

    public function isHoliday(BzDay $day): bool
    {
        return array_key_exists("$day", $this->holidays);
    }

    /** check if $day definition is valid for this year  
     * 
     **/
    private function validate(array $day_def, int $year) : bool
    {
        $valid = true;
        if (isset($day_def[BzDef::_SNC])){
            $valid = $valid && $year>=$day_def[BzDef::_SNC];
        }
        if (isset($day_def[BzDef::_BET])){
            $valid = $valid && self::between($year, $day_def[BzDef::_BET]);
        }
        if (isset($day_def[BzDef::_EXC])){
            $valid = $valid && !in_array($year, $day_def[BzDef::_EXC]);
        }
        if (isset($day_def[BzDef::_INC])){
            $valid = $valid && in_array($year, $day_def[BzDef::_INC]);
        }
        return $valid;
    }

    private static function between(int $a, array $range): bool 
    {
        if (sizeof($range) < 2)
            throw new \Exception("Illegal arguments! the second argument should be an array of size 2");

        return ($range[0] <= $a and $a <= $range[1]);
    }
}