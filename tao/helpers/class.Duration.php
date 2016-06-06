<?php

use oat\tao\helpers\DateIntervalMS;

/**
 * Helps you to manipulate durations.
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 * @package tao
 */
class tao_helpers_Duration
{

    /**
     * Converts a time string to an ISO8601 duration
     * @param string $time as hh:mm:ss.micros
     * @return string the ISO duration
     */
    public static function timetoDuration($time)
    {
        $duration = 'PT';

        $regexp = "/^([0-9]{2}):([0-9]{2}):([0-9]{2})(\.[0-9]{1,6})?$/";

        if (preg_match($regexp, $time, $matches)) {
            $duration .= intval($matches[1]) . 'H' . intval($matches[2]) . 'M';
            $duration .= isset($matches[4])
                ? intval($matches[3]) . $matches[4] . 'S'
                : intval($matches[3]) . 'S';
        } else {
            $duration .= '0S';
        }

        return $duration;
    }

    /**
     * Converts  an interval to a time
     * @param DateInterval $interval
     * @return string time hh:mm:ss
     */
    public static function intervalToTime(DateInterval $interval)
    {
        $time = null;

        if (!is_null($interval)) {
            $format = isset($interval->u) ? '%H:%I:%S.%U' : '%H:%I:%S';
            $time = $interval->format($format);
        }

        return $time;
    }

    /**
     * Converts a duration to a time
     * @param string $duration the ISO duration
     * @return string time hh:mm:ss.micros
     */
    public static function durationToTime($duration)
    {
        $time = null;

        try {
            $interval = preg_match('/(\.[0-9]{1,6}S)$/', $duration)
                ? new DateIntervalMS($duration)
                : new DateInterval($duration);
            $time = self::intervalToTime($interval);
        } catch (Exception $e) {
            common_Logger::e($e->getMessage());
        }

        return $time;
    }
}
