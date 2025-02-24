<?php
namespace ksu\bizcal;

use ksu\bizcal\BizYear;
use ksu\bizcal\Month;
use ksu\bizcal\Day;
use ksu\bizcal\HolidayDef as BzDef;

class BizHoliday
{
    private array $holidays = [];
    public BizYear $bzcal;

     public function __construct(int $year, int $first_month)
    {
        $this->bzcal = new BizYear($year, $first_month);
    }

    public static function createFromBizYear(BizYear $bizcal){
        return new BizYear($bizcal->y, $bizcal->firstMonth->m);
    } 
    
    public function holidays(int $month = 0)
    {
        if ($month > 0){
            $cb = function ($ymd) use ($month){
                [, $m, ] = BizDay::toArray($ymd);
                return $m==$month;
            };
            return array_filter($this->holidays, $cb, ARRAY_FILTER_USE_KEY);
        }
        return $this->holidays;
    }

    public function parse(array $holiday_defs = []): self
    {
        $this->holidays = []; //reset holodays
        $year = $this->bzcal->firstMonth->y;
        if ($year >= BzDef::HOLIDAY_SINCE){
            $holiday_defs = $holiday_defs ? $holiday_defs : BzDef::HOLIDAY_DEF; 
            $this->parseYear($holiday_defs)->suppHolidays()->bridgeHolidays();
        }
        return $this;
    }

    private function parseYear(array $holiday_defs = []): self
    {
        $holiday_defs = $holiday_defs ? $holiday_defs : BzDef::HOLIDAY_DEF; 
        for ($mon =$this->bzcal->firstMonth; $mon->leq($this->bzcal->lastMonth); $mon=$mon->next()){
            $year = $mon->y;
            $month = $mon->m;
            $month_defs = $holiday_defs[$month]??[]; 
            if (!$month_defs) continue;
            $month_holidays = $this->parseMonth($year, $month, $month_defs);
            $this->holidays = array_merge($this->holidays, $month_holidays);
        }
        ksort($this->holidays);
        return $this;
    }

    private function suppHolidays(): self
    {
        $ex_holidays = [];  // 振替休日：substitute holidays for holidays on Sunday
        foreach (array_keys($this->holidays) as $date){
            $day = BizDay::createFromString($date);
            if ($day->wday > 0) continue;
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
            $day1 = BizDay::createFromString($days[$i]);
            $day2 = BizDay::createFromString($days[$i+1]);
            $day = $day1->sandwich($day2); 
            if ($day){
                $ex_holidays["$day"] = BzDef::HOLIDAY_NAME['BridgeHoliday'];
            }
        }
        $this->holidays = array_merge($this->holidays, $ex_holidays);
        ksort($this->holidays);
        return $this;
    }

    /** 
     * parse holiday definitions and return an array of holidays for this month 
     **/
    private function parseMonth(int $year, int $month, array $month_defs): array
    {
        $holidays = [];
        foreach ($month_defs as $def){
            if ($this->validate($def, $year) === false) continue;
            $day = $this->parseDay($year, $month, $def[BzDef::_DAY]);                   
            if ($day > 0){ 
                $date = BizDay::toString($year, $month, $day);
                $holidays[$date] = BzDef::HOLIDAY_NAME[$def[BzDef::_ID]];
            }                        
        }
        return $holidays;
    }

    /** 
     * parse day definition and calculate a holiday 
     **/
    private function parseDay(int $year, int $month, mixed $day_def): int 
    {
        $biz_month = new BizMonth($year, $month);
        if (is_integer($day_def)) return $day_def;
        if (is_array($day_def)) return $biz_month->w2mday($day_def[0], $day_def[1]);
        if (in_array($day_def, ['springEquinox', 'autumnEquinox']))
            return $this->equinox($day_def);
        return -1; 
    }

    /** caculate spring and autumn equinox days  
     *  valid for years between 1851 and 2150. return -1 otherwise   
     **/
    private function equinox(string $holiday='springEquinox') : int
    {
        $year = $this->bzcal->y;
        $year = ($holiday=="springEquinox" and 3 < $this->bzcal->firstMonth->m) ? $year + 1 : $year;
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
        
        if (!in_array($holiday, ['springEquinox', 'autumnEquinox'])){
            throw new \Exception("Unknown holiday : " . $holiday);
        }    
        $alpha = ($holiday=='springEquinox') ? $delta[0] : $delta[1];
        return (int)floor($alpha + 0.242194 * ($year - 1980) - floor(($year - 1980) / 4));
    } 

    public function isHoliday(BizDay $day): bool
    {
        return array_key_exists("$day", $this->holidays);
    }

    /** check if $day definition is valid for this year  
     * 
     **/
    private function validate(array $day_def, int $year) : bool
    {
        $valid = true;
        if (isset($day_def[BzDef::_BET])){
            $valid = $valid && self::between($year, $day_def[BzDef::_BET]);
        }
        if (isset($day_def[BzDef::_EXP])){
            $valid = $valid && !in_array($year, $day_def[BzDef::_EXP]);
        }
        if (isset($day_def[BzDef::_ONL])){
            $valid = $valid && in_array($year, $day_def[BzDef::_ONL]);
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