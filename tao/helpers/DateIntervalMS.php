<?php

/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 *
 */

namespace oat\tao\helpers;

/**
 * Helps manipulate intervals of dates with microseconds, extended from built in DateInterval
 *
 * Class DateIntervalMS
 * @author Ivan Klimchuk <klimchuk@1pt.com>
 * @package oat\tao\helpers
 */
class DateIntervalMS extends \DateInterval
{
    public $u = 0;

    protected static $interval_spec_regex = "/
        ^ ## start of the string
        P ## first character must be a P
        (?:(?P<y>\d+)Y)? ## year
        (?:(?P<m>\d+)M)? ## month
        (?:(?P<d>\d+)D)? ## day
        (?:T ## T delineates between day and time information
            (?:(?P<h>\d+)H)? ## hour
            (?:(?P<i>\d+)M)? ## minute
            (?:(?P<s>\d+(?:\.\d+)?)S)? ## seconds as float.
            )? ## closes 'T' subexpression
        $ ## end of the string
        /x";

    /**
     * Formatting for current date interval
     * @param $format
     * @return string
     */
    public function format($format)
    {
        $format = str_replace('%U', sprintf("%06d", $this->u), $format);
        $format = str_replace('%u', sprintf("%d", intval($this->u)), $format);

        return parent::format($format);
    }

    /**
     * Convert microseconds to seconds in float type
     * @param $microseconds
     * @return float
     */
    public static function microsecondsToSeconds($microseconds)
    {
        $microseconds = intval($microseconds);
        $seconds = round($microseconds / 1000000, 6);

        return $seconds;
    }

    /**
     * Convert seconds with microseconds as fraction to amount of microseconds
     * @param $seconds
     * @return int
     */
    public static function secondsToMicroseconds($seconds)
    {
        $seconds = round($seconds, 6);
        $microseconds = intval($seconds * 1000000);

        return $microseconds;
    }

    /**
     * Build valid DateInterval format
     * @param array $parts
     * @return string
     */
    private static function getLegacySpec(array $parts)
    {
        $spec = "P";
        $spec .= $parts['y'] !== "" ? "{$parts['y']}Y" : "";
        $spec .= $parts['m'] !== "" ? "{$parts['m']}M" : "";
        $spec .= $parts['d'] !== "" ? "{$parts['d']}D" : "";
        if ($parts['h'] . $parts['i'] . $parts['s'] !== "") {
            $spec .= "T";
            $spec .= $parts['h'] !== "" ? "{$parts['h']}H" : "";
            $spec .= $parts['i'] !== "" ? "{$parts['i']}M" : "";
            $spec .= $parts['s'] !== "" ? "{$parts['s']}S" : "";
        }
        if ($spec === "P") {
            $spec = "";
        }

        return $spec;
    }

    /**
     * Custom construct with support microseconds
     * @param string $interval_spec
     */
    public function __construct($interval_spec)
    {
        if (!preg_match(static::$interval_spec_regex, $interval_spec, $parts)) {
            throw new \UnexpectedValueException(sprintf("%s::%s: Unknown or bad format (%s)", get_called_class(), '__construct', $interval_spec));
        }

        if (isset($parts['s'])) {
            $preciseSeconds = floatval($parts['s']);
            $microseconds = static::secondsToMicroseconds(fmod($preciseSeconds, 1.0));
            $seconds = floor($preciseSeconds);
            $this->u = $microseconds;
            $parts['s'] = $seconds;
        }

        $legacy_spec = static::getLegacySpec($parts);

        parent::__construct($legacy_spec);
    }

}
