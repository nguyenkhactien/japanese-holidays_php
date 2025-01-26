# Japanese Holidays

A PHP package to calculate Japanese national holidays.

## Installation

Install the package via Composer:

```bash
composer require tiennk1995dev/japanese-holidays
```

## Usage

```php
use JapaneseHolidays\HolidayCalculator;
use Carbon\Carbon;

$calculator = new HolidayCalculator();
$holidays = $calculator->getHolidays(2025);

foreach ($holidays as $holiday) {
    echo $holiday['date']->format('Y-m-d') . ' - ' . $holiday['name'] . ' (' . $holiday['nameEn'] . ')' . PHP_EOL;
}

if ($calculator->isHoliday(Carbon::create(2025))) {
    echo 'January 1, 2025 is a holiday.';
}

$name = $calculator->getHolidayName(Carbon::create(2025), 'en');
echo 'The holiday on January 1, 2025 is: ' . $name;

$start = Carbon::create(2025);
$end = Carbon::create(2025, 3, 31);
$holidaysBetween = $calculator->getHolidaysBetween($start, $end);
echo 'There are ' . $holidaysBetween->count() . ' holidays between ' . $start->format('Y-m-d') . ' and ' . $end->format('Y-m-d') . '.';

$workDays = $calculator->countWorkDays($start, $end);
echo 'There are ' . $workDays . ' work days between ' . $start->format('Y-m-d') . ' and ' . $end->format('Y-m-d') . '.';
```

## Testing

Run the tests using PHPUnit:

```bash
vendor/bin/phpunit tests
```

## License

The MIT License (MIT). See [LICENSE](LICENSE) for more information.