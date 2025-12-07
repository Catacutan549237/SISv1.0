<?php

namespace App\Helpers;

class ScheduleHelper
{
    /**
     * Parse schedule string into time ranges and days
     * Format: "130A-330A M-F" or "900M-1000M MWF"
     * 
     * Time format: HHMM[M|A|E]
     * M = Morning (before 12pm)
     * A = Afternoon (12pm - 5pm)
     * E = Evening (5pm onwards)
     * 
     * Days: M-F (Monday to Friday), MWF, TTH, etc.
     */
    public static function parseSchedule($schedule)
    {
        if (empty($schedule) || $schedule === 'TBA') {
            return null;
        }

        // Extract time range and days
        // Example: "130A-330A M-F" or "900M-1000M MWF"
        $pattern = '/(\d+[MAE])-(\d+[MAE])\s+(.+)/';
        
        if (!preg_match($pattern, $schedule, $matches)) {
            return null;
        }

        $startTime = self::parseTime($matches[1]);
        $endTime = self::parseTime($matches[2]);
        $days = self::parseDays($matches[3]);

        if ($startTime === null || $endTime === null || empty($days)) {
            return null;
        }

        return [
            'start' => $startTime,
            'end' => $endTime,
            'days' => $days,
        ];
    }

    /**
     * Parse time string to minutes since midnight
     * Format: HHMM[M|A|E]
     * 
     * Examples:
     * 900M = 9:00 AM = 540 minutes
     * 130A = 1:30 PM = 810 minutes
     * 700E = 7:00 PM = 1140 minutes
     */
    private static function parseTime($timeStr)
    {
        $pattern = '/^(\d+)([MAE])$/';
        
        if (!preg_match($pattern, $timeStr, $matches)) {
            return null;
        }

        $time = $matches[1];
        $period = $matches[2];

        // Extract hours and minutes
        $hours = (int) substr($time, 0, -2);
        $minutes = (int) substr($time, -2);

        // Adjust based on period
        if ($period === 'A') {
            // Afternoon: 12pm - 5pm
            if ($hours < 12) {
                $hours += 12;
            }
        } elseif ($period === 'E') {
            // Evening: 5pm onwards
            if ($hours < 12) {
                $hours += 12;
            }
        }
        // M (Morning) stays as is (AM hours)

        return ($hours * 60) + $minutes;
    }

    /**
     * Parse days string into array of day codes
     * 
     * Examples:
     * "M-F" = ['M', 'T', 'W', 'TH', 'F']
     * "MWF" = ['M', 'W', 'F']
     * "TTH" = ['T', 'TH']
     */
    private static function parseDays($daysStr)
    {
        $daysStr = strtoupper(trim($daysStr));

        // Handle M-F (Monday to Friday)
        if ($daysStr === 'M-F') {
            return ['M', 'T', 'W', 'TH', 'F'];
        }

        // Handle individual days
        $days = [];
        $i = 0;
        $len = strlen($daysStr);

        while ($i < $len) {
            // Check for TH (Thursday)
            if ($i + 1 < $len && substr($daysStr, $i, 2) === 'TH') {
                $days[] = 'TH';
                $i += 2;
            } else {
                $days[] = $daysStr[$i];
                $i++;
            }
        }

        return $days;
    }

    /**
     * Check if two schedules conflict
     */
    public static function hasConflict($schedule1, $schedule2)
    {
        $parsed1 = self::parseSchedule($schedule1);
        $parsed2 = self::parseSchedule($schedule2);

        // If either schedule is null/TBA, no conflict
        if ($parsed1 === null || $parsed2 === null) {
            return false;
        }

        // Check if they share any common days
        $commonDays = array_intersect($parsed1['days'], $parsed2['days']);
        
        if (empty($commonDays)) {
            return false; // No common days, no conflict
        }

        // Check if time ranges overlap
        // Conflict if: start1 < end2 AND start2 < end1
        $timeOverlap = ($parsed1['start'] < $parsed2['end']) && 
                       ($parsed2['start'] < $parsed1['end']);

        return $timeOverlap;
    }

    /**
     * Format time in minutes to readable format
     */
    public static function formatTime($minutes)
    {
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        
        $period = $hours < 12 ? 'AM' : 'PM';
        $displayHours = $hours > 12 ? $hours - 12 : ($hours == 0 ? 12 : $hours);
        
        return sprintf('%d:%02d %s', $displayHours, $mins, $period);
    }
}
