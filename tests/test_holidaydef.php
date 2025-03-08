<?php
include '../vendor/autoload.php';

use ksu\bizcal\Holiday;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;

header("Content-Type: text/plain");

try {
    $holidaydefs = Yaml::parseFile('../src/HolidayDefs.yaml');
    $holidaynames = Yaml::parseFile('../src/HolidayNames.yaml');
    print_r($holidaynames);
} catch (ParseException $exception) {
    printf('Unable to parse the YAML string: %s', $exception->getMessage());
}

$hline = function($n, $c='=') { 
    return str_pad('', $n, $c); 
};
$str_hline = $hline(38, "=*") . PHP_EOL;

echo $str_hline;
[$y, $m] = [2019, 4];
$holiday = new Holiday($y, $m);
echo $holiday->bzyear, PHP_EOL;
echo "{$y}年度", PHP_EOL;
$holidays = $holiday->parse($holidaydefs, $holidaynames)->holidays();
print_r($holidays);

echo "5月", PHP_EOL;
$holidays = $holiday->holidays(5);
print_r($holidays);

echo $str_hline;
[$y, $m] = [2031, 4];
$y = $_GET['y'] ?? $y;
$m = $_GET['m'] ?? $m;
$holiday = new Holiday($y, $m);
echo $holiday->bzyear, PHP_EOL;
echo "{$y}度", PHP_EOL;
$holidays = $holiday->parse()->holidays();
print_r($holidays);

echo "5月", PHP_EOL;
$holidays = $holiday->holidays(5);
print_r($holidays);
