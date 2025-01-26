<?php

namespace JapaneseHolidays;

use Carbon\Carbon;

class HolidayCalculator
{
    private int $year;
    private const MIN_YEAR = 1948;
    private const MAX_YEAR = 2150;

    public function getHolidays(int $year): array
    {
        $this->validateYear($year);
        $this->year = $year;

        $holidays = [];
        $holidayMethods = [
            'newYearsDay', 'comingOfAgeDay', 'foundationDay',
            'vernalEquinoxDay', 'showaDayAndEnthronement',
            'constitutionDay', 'greeneryDay', 'childrensDay',
            'marineDay', 'mountainDay', 'respectForAgedDay',
            'autumnalEquinoxDay', 'healthSportsDay',
            'cultureDay', 'laborThanksgivingDay', 'emperorsBirthday'
        ];

        foreach ($holidayMethods as $method) {
            $this->$method($holidays);
        }

        $this->addSpecialHolidays($holidays)
            ->addSubstituteHolidays($holidays)
            ->addCitizensHoliday($holidays);

        usort($holidays, fn($a, $b) => $a['date']->lessThan($b['date']) ? -1 : 1);

        return $holidays;
    }

    private function validateYear(int $year): void
    {
        if ($year < self::MIN_YEAR || $year > self::MAX_YEAR) {
            throw new \InvalidArgumentException("Year must be between " . self::MIN_YEAR . " and " . self::MAX_YEAR);
        }
    }

    private function calculateEquinoxDay(bool $isVernal): Carbon
    {
        $baseYear = 1980;
        $calculations = [
            'vernal' => [
                [1851, 1899, 19.8277],
                [1900, 1979, 20.8357],
                [1980, 2099, 20.8431],
                [2100, PHP_INT_MAX, 21.851]
            ],
            'autumnal' => [
                [1851, 1899, 22.2588],
                [1900, 1979, 23.2588],
                [1980, 2099, 23.2488],
                [2100, PHP_INT_MAX, 24.2488]
            ]
        ];

        $monthOffset = $isVernal ? 3 : 9;
        $periodData = $calculations[$isVernal ? 'vernal' : 'autumnal'];

        foreach ($periodData as [$start, $end, $baseCalculation]) {
            if ($this->year >= $start && $this->year <= $end) {
                $day = (int)($baseCalculation + 0.242194 * ($this->year - $baseYear) - floor(($this->year - $baseYear) / 4));
                return Carbon::create($this->year, $monthOffset, $day);
            }
        }

        throw new \RuntimeException("Unable to calculate equinox day");
    }

    private function newYearsDay(array &$holidays): void
    {
        if ($this->year >= 1949) {
            $holidays[] = [
                'date' => Carbon::create($this->year, 1, 1),
                'name' => '元日',
                'nameEn' => 'New Year\'s Day'
            ];
        }
    }

    private function comingOfAgeDay(array &$holidays): void
    {
        if ($this->year >= 2000) {
            $date = Carbon::create($this->year, 1, 1)->nthOfMonth(2, Carbon::MONDAY);
            $holidays[] = [
                'date' => $date,
                'name' => '成人の日',
                'nameEn' => 'Coming of Age Day'
            ];
        } elseif ($this->year >= 1949) {
            $holidays[] = [
                'date' => Carbon::create($this->year, 1, 15),
                'name' => '成人の日',
                'nameEn' => 'Coming of Age Day'
            ];
        }
    }

    private function foundationDay(array &$holidays): void
    {
        if ($this->year >= 1967) {
            $holidays[] = [
                'date' => Carbon::create($this->year, 2, 11),
                'name' => '建国記念の日',
                'nameEn' => 'National Foundation Day'
            ];
        }
    }

    private function vernalEquinoxDay(array &$holidays): void
    {
        if ($this->year >= 1949) {
            $holidays[] = [
                'date' => $this->calculateEquinoxDay(true),
                'name' => '春分の日',
                'nameEn' => 'Vernal Equinox Day'
            ];
        }
    }

    private function showaDayAndEnthronement(array &$holidays): void
    {
        // Showa Day
        if ($this->year >= 2007) {
            $holidays[] = [
                'date' => Carbon::create($this->year, 4, 29),
                'name' => '昭和の日',
                'nameEn' => 'Showa Day'
            ];
        } elseif ($this->year >= 1989 && $this->year <= 2006) {
            $holidays[] = [
                'date' => Carbon::create($this->year, 4, 29),
                'name' => 'みどりの日',
                'nameEn' => 'Greenery Day'
            ];
        }

        // Enthronement Day (special for 2019)
        if ($this->year === 2019) {
            $holidays[] = [
                'date' => Carbon::create($this->year, 5, 1),
                'name' => '天皇の即位の日',
                'nameEn' => 'Enthronement Day'
            ];
        }
    }

    private function constitutionDay(array &$holidays): void
    {
        if ($this->year >= 1949) {
            $holidays[] = [
                'date' => Carbon::create($this->year, 5, 3),
                'name' => '憲法記念日',
                'nameEn' => 'Constitution Memorial Day'
            ];
        }
    }

    private function greeneryDay(array &$holidays): void
    {
        if ($this->year >= 2007) {
            $holidays[] = [
                'date' => Carbon::create($this->year, 5, 4),
                'name' => 'みどりの日',
                'nameEn' => 'Greenery Day'
            ];
        }
    }

    private function childrensDay(array &$holidays): void
    {
        if ($this->year >= 1949) {
            $holidays[] = [
                'date' => Carbon::create($this->year, 5, 5),
                'name' => 'こどもの日',
                'nameEn' => 'Children\'s Day'
            ];
        }
    }

    private function marineDay(array &$holidays): void
    {
        if ($this->year === 2020) {
            $holidays[] = [
                'date' => Carbon::create($this->year, 7, 23),
                'name' => '海の日',
                'nameEn' => 'Marine Day'
            ];
        } elseif ($this->year >= 2003) {
            $date = Carbon::create($this->year, 7, 1)->nthOfMonth(3, Carbon::MONDAY);
            $holidays[] = [
                'date' => $date,
                'name' => '海の日',
                'nameEn' => 'Marine Day'
            ];
        } elseif ($this->year >= 1996) {
            $holidays[] = [
                'date' => Carbon::create($this->year, 7, 20),
                'name' => '海の日',
                'nameEn' => 'Marine Day'
            ];
        }
    }

    private function mountainDay(array &$holidays): void
    {
        if ($this->year === 2020) {
            $holidays[] = [
                'date' => Carbon::create($this->year, 8, 10),
                'name' => '山の日',
                'nameEn' => 'Mountain Day'
            ];
        } elseif ($this->year >= 2016) {
            $holidays[] = [
                'date' => Carbon::create($this->year, 8, 11),
                'name' => '山の日',
                'nameEn' => 'Mountain Day'
            ];
        }
    }

    private function respectForAgedDay(array &$holidays): void
    {
        if ($this->year >= 2003) {
            $date = Carbon::create($this->year, 9, 1)->nthOfMonth(3, Carbon::MONDAY);
            $holidays[] = [
                'date' => $date,
                'name' => '敬老の日',
                'nameEn' => 'Respect for the Aged Day'
            ];
        } elseif ($this->year >= 1966) {
            $holidays[] = [
                'date' => Carbon::create($this->year, 9, 15),
                'name' => '敬老の日',
                'nameEn' => 'Respect for the Aged Day'
            ];
        }
    }

    private function autumnalEquinoxDay(array &$holidays): void
    {
        if ($this->year >= 1948) {
            $holidays[] = [
                'date' => $this->calculateEquinoxDay(false),
                'name' => '秋分の日',
                'nameEn' => 'Autumnal Equinox Day'
            ];
        }
    }

    private function healthSportsDay(array &$holidays): void
    {
        if ($this->year === 2020) {
            $holidays[] = [
                'date' => Carbon::create($this->year, 7, 24),
                'name' => 'スポーツの日',
                'nameEn' => 'Sports Day'
            ];
        } elseif ($this->year >= 2020) {
            $date = Carbon::create($this->year, 10, 1)->nthOfMonth(2, Carbon::MONDAY);
            $holidays[] = [
                'date' => $date,
                'name' => 'スポーツの日',
                'nameEn' => 'Sports Day'
            ];
        } elseif ($this->year >= 2000) {
            $date = Carbon::create($this->year, 10, 1)->nthOfMonth(2, Carbon::MONDAY);
            $holidays[] = [
                'date' => $date,
                'name' => '体育の日',
                'nameEn' => 'Health and Sports Day'
            ];
        } elseif ($this->year >= 1966) {
            $holidays[] = [
                'date' => Carbon::create($this->year, 10, 10),
                'name' => '体育の日',
                'nameEn' => 'Health and Sports Day'
            ];
        }
    }

    private function cultureDay(array &$holidays): void
    {
        if ($this->year >= 1948) {
            $holidays[] = [
                'date' => Carbon::create($this->year, 11, 3),
                'name' => '文化の日',
                'nameEn' => 'Culture Day'
            ];
        }
    }

    private function laborThanksgivingDay(array &$holidays): void
    {
        if ($this->year >= 1948) {
            $holidays[] = [
                'date' => Carbon::create($this->year, 11, 23),
                'name' => '勤労感謝の日',
                'nameEn' => 'Labor Thanksgiving Day'
            ];
        }
    }

    private function emperorsBirthday(array &$holidays): void
    {
        if ($this->year >= 2020) {
            $holidays[] = [
                'date' => Carbon::create($this->year, 2, 23),
                'name' => '天皇誕生日',
                'nameEn' => 'Emperor\'s Birthday'
            ];
        } elseif ($this->year >= 1989 && $this->year <= 2018) {
            $holidays[] = [
                'date' => Carbon::create($this->year, 12, 23),
                'name' => '天皇誕生日',
                'nameEn' => 'Emperor\'s Birthday'
            ];
        } elseif ($this->year >= 1949 && $this->year <= 1988) {
            $holidays[] = [
                'date' => Carbon::create($this->year, 4, 29),
                'name' => '天皇誕生日',
                'nameEn' => 'Emperor\'s Birthday'
            ];
        }
    }

    private function addSpecialHolidays(array &$holidays): self
    {
        $specialHolidays = [
            [1959, 4, 10, '皇太子明仁親王の結婚の儀', 'Imperial Prince Akihito\'s Wedding Ceremony'],
            [1989, 2, 24, '昭和天皇の大喪の礼', 'State Funeral of Emperor Showa'],
            [1990, 11, 12, '即位礼正殿の儀', 'Ceremony of the Enthronement of the Emperor'],
            [1993, 6, 9, '皇太子徳仁親王の結婚の儀', 'Imperial Prince Naruhito\'s Wedding Ceremony'],
            [2019, 10, 22, '即位礼正殿の儀', 'Ceremony of the Enthronement of the Emperor']
        ];

        foreach ($specialHolidays as [$year, $month, $day, $nameJa, $nameEn]) {
            if ($this->year === $year) {
                $holidays[] = [
                    'date' => Carbon::create($year, $month, $day),
                    'name' => $nameJa,
                    'nameEn' => $nameEn
                ];
            }
        }

        return $this;
    }

    private function addSubstituteHolidays(array &$holidays): self
    {
        foreach ($holidays as $key => $holiday) {
            $date = $holiday['date'];

            // May 3rd or 4th falling on Sunday in years 2007 and after
            if ($this->year >= 2007) {
                if (($date->month === 5 && ($date->day === 3 || $date->day === 4)) && $date->isSunday()) {
                    $holidays[] = [
                        'date' => Carbon::create($this->year, 5, 6),
                        'name' => $holiday['name'] . 'の振替休日',
                        'nameEn' => $holiday['nameEn'] . ' (Substitute Holiday)'
                    ];
                    continue;
                }
            }

            // Substitute holiday after 1973 when holiday falls on Sunday
            if ($this->year >= 1973 && $date->isSunday()) {
                $substituteDate = $date->copy()->addDay();

                // Avoid duplicate holidays
                while ($this->holidayExists($holidays, $substituteDate)) {
                    $substituteDate->addDay();
                }

                $holidays[] = [
                    'date' => $substituteDate,
                    'name' => $holiday['name'] . 'の振替休日',
                    'nameEn' => $holiday['nameEn'] . ' (Substitute Holiday)'
                ];
            }
        }

        return $this;
    }

    private function holidayExists(array $holidays, Carbon $date): bool
    {
        foreach ($holidays as $holiday) {
            if ($holiday['date']->isSameDay($date)) {
                return true;
            }
        }
        return false;
    }

    private function addCitizensHoliday(array &$holidays): self
    {
        if ($this->year >= 1988) {
            usort($holidays, fn($a, $b) => $a['date']->lessThan($b['date']) ? -1 : 1);
            $previousDate = null;

            foreach ($holidays as $holiday) {
                if ($previousDate !== null) {
                    $daysBetween = $previousDate['date']->diffInDays($holiday['date']);

                    if ($daysBetween === 2) {
                        $citizensHolidayDate = $previousDate['date']->copy()->addDay();

                        // Check if it's not already a holiday and not a Sunday
                        if (!$citizensHolidayDate->isSunday() && !$this->holidayExists($holidays, $citizensHolidayDate)) {
                            $holidays[] = [
                                'date' => $citizensHolidayDate,
                                'name' => '国民の休日',
                                'nameEn' => 'Citizens\' Holiday'
                            ];
                        }
                    }
                }
                $previousDate = $holiday;
            }
        }
        return $this;
    }

    public function isHoliday(Carbon $date): bool
    {
        $holidays = $this->getHolidays($date->year);
        foreach ($holidays as $holiday) {
            if ($holiday['date']->isSameDay($date)) {
                return true;
            }
        }
        return false;
    }

    public function getHolidayName(Carbon $date, string $locale = 'ja'): ?string
    {
        $holidays = $this->getHolidays($date->year);
        foreach ($holidays as $holiday) {
            if ($holiday['date']->isSameDay($date)) {
                return $locale === 'en' ? $holiday['nameEn'] : $holiday['name'];
            }
        }
        return null;
    }

    public function getHolidaysBetween(Carbon $start, Carbon $end): array
    {
        $holidays = [];
        for ($year = $start->year; $year <= $end->year; $year++) {
            $yearHolidays = $this->getHolidays($year);
            foreach ($yearHolidays as $holiday) {
                if ($holiday['date']->between($start, $end)) {
                    $holidays[] = $holiday;
                }
            }
        }
        usort($holidays, fn($a, $b) => $a['date']->lessThan($b['date']) ? -1 : 1);
        return $holidays;
    }

    public function countWorkDays(Carbon $start, Carbon $end): int
    {
        $holidays = $this->getHolidaysBetween($start, $end);
        $workDays = 0;
        $currentDate = $start->copy();

        while ($currentDate->lte($end)) {
            $isHoliday = false;
            foreach ($holidays as $holiday) {
                if ($holiday['date']->isSameDay($currentDate)) {
                    $isHoliday = true;
                    break;
                }
            }

            if (!$currentDate->isWeekend() && !$isHoliday) {
                $workDays++;
            }
            $currentDate->addDay();
        }

        return $workDays;
    }
}