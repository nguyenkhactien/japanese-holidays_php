<?php

namespace JapaneseHolidays;

use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class JapaneseHolidayServiceTest extends TestCase
{
    private $holidayCalculator;

    protected function setUp(): void
    {
        $this->holidayCalculator = new HolidayCalculator();
    }

    public function testGetHolidays()
    {
        $holidays = $this->holidayCalculator->getHolidays(2025);
        $this->assertNotEmpty($holidays);
        $this->assertTrue(count($holidays) >= 15);
    }

    public function testIsHoliday()
    {
        $this->assertTrue($this->holidayCalculator->isHoliday(Carbon::create(2025)));
        $this->assertFalse($this->holidayCalculator->isHoliday(Carbon::create(2025, 1, 2)));
    }

    public function testGetHolidayName()
    {
        $this->assertEquals("元日", $this->holidayCalculator->getHolidayName(Carbon::create(2025)));
        $this->assertEquals('New Year\'s Day', $this->holidayCalculator->getHolidayName(Carbon::create(2025), 'en'));
        $this->assertNull($this->holidayCalculator->getHolidayName(Carbon::create(2025, 1, 2)));
    }

    public function testGetHolidaysBetween()
    {
        $startDate = Carbon::create(2025);
        $endDate = Carbon::create(2025, 3, 31);
        $holidays = $this->holidayCalculator->getHolidaysBetween($startDate, $endDate);
        $this->assertNotEmpty($holidays);
        $this->assertTrue(count($holidays) >= 3);
    }

    public function testCountWorkDays()
    {
        $startDate = Carbon::create(2025);
        $endDate = Carbon::create(2025, 1, 31);
        $workDays = $this->holidayCalculator->countWorkDays($startDate, $endDate);
        $this->assertEquals(21, $workDays);
    }
}