<?php
include '../vendor/autoload.php';

use ksu\bizcal\BzYear;
use ksu\bizcal\BzMonth;

$hline = function($n, $c='=') { 
    return str_pad('', $n, $c); 
};
$str_hline = $hline(38, "=*") . PHP_EOL;

header("Content-Type: text/plain");
[$y, $m] = [2031, 4];
$cal = new BzYear($y, $m);
echo $cal, PHP_EOL;
foreach (range(1,4) as $n){
    $_cal = $cal->next($n);
    echo $_cal, PHP_EOL;
}

echo $str_hline;
echo "no yyyy-mm, fw, days", PHP_EOL;
echo $str_hline;
$mon = $cal->startMonth;
foreach (range(1,16) as $i){
    echo "{$i}, {$mon}, {$mon->firstwday}, {$mon->lastday}", PHP_EOL;
    $mon = $mon->next();
}

echo $str_hline;
echo "\$mon = BzMonth::createFromArray([2024, 4])", PHP_EOL;

$mon = BzMonth::createFromArray([2024, 4]);
echo "{$mon}, {$mon->firstwday}, {$mon->lastday}", PHP_EOL;

$mon1 = $mon;

echo $str_hline;
echo "\$mon = BzMonth::createFromString('2025-10')", PHP_EOL;

$mon = BzMonth::createFromString('2025-10');
echo "{$mon}, {$mon->firstwday}, {$mon->lastday}", PHP_EOL;
echo "'{$mon}' is {$mon->diff($mon1)} months after '{$mon1}'", PHP_EOL;

echo $str_hline;
echo "Year::Week", PHP_EOL;
echo "\$cal->weeks(): ", PHP_EOL;
foreach ($cal->weeks() as $week){
    echo "[{$week->startDay}, {$week->lastDay}]", PHP_EOL;
}

echo $str_hline;
echo "Month::Week", PHP_EOL;

echo "\$mon->week(\$n): loop", PHP_EOL;
foreach (range(1,6) as $n){
    $week = $mon->week($n);
    echo "[{$week->startDay}, {$week->lastDay}]", PHP_EOL; 
}

echo "\$mon->weeks(): generator", PHP_EOL;
foreach ($mon->weeks() as $week){
    echo "[{$week->startDay}, {$week->lastDay}]", PHP_EOL;
}

echo $str_hline;
$day = $mon->day(10);
echo "Month::Day", PHP_EOL;
echo "\$day = \$mon->day(10)";
echo " = {$day}, {$day->w}", PHP_EOL;

$day = $mon->day(33);
echo "\$day = \$mon->day(33)";
echo " = {$day}, {$day->w}", PHP_EOL;

echo $str_hline;
$mon = $cal->month(1);
$day = $mon->day();
echo "Anyday: {$day}, {$day->w}", PHP_EOL;
$day = $day->next(-$day->w);
echo "Sunday: {$day}, {$day->w}", PHP_EOL;



