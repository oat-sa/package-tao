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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
use oat\tao\helpers\dateFormatter\EuropeanFormatter;
use oat\tao\helpers\dateFormatter\Formatter;

/**
 * Utility to display dates.
 *
 * @author Joel Bout, <joel@taotesting.com>
 * @package tao
 *         
 */
class tao_helpers_Date
{
    const CONFIG_KEY = 'dateService';

    const FORMAT_LONG = 0;

    const FORMAT_VERBOSE = 1;

    const FORMAT_DATEPICKER = 2;

    const FORMAT_ISO8601 = 3;

    const FORMAT_INTERVAL_LONG = 100;

    const FORMAT_INTERVAL_SHORT = 101;
    
    private static $service;

    static protected function getDateFormatter()
    {
        if (is_null(self::$service)) {
            $ext = common_ext_ExtensionsManager::singleton()->getExtensionById('tao');
            $service = $ext->getConfig(self::CONFIG_KEY);
            self::$service = is_object($service) && $service instanceof Formatter
                ? $service
                : new EuropeanFormatter();
        }

        return self::$service;
    }

    /**
     * Displays a date/time
     * Should in theory be dependant on the users locale and timezone
     * @param mixed $timestamp
     * @param int $format The date format. See tao_helpers_Date's constants.
     * @param DateTimeZone $timeZone user timezone
     * @return string The formatted date.
     * @throws common_Exception when timestamp is not recognized
     */
    static public function displayeDate($timestamp, $format = self::FORMAT_LONG, DateTimeZone $timeZone = null)
    {
        if (is_object($timestamp) && $timestamp instanceof core_kernel_classes_Literal) {
            $ts = $timestamp->__toString();
        } elseif (is_object($timestamp) && $timestamp instanceof DateTime) {
            $ts = self::getTimeStampWithMicroseconds($timestamp);
        } elseif (is_numeric($timestamp)) {
            $ts = $timestamp;
        } elseif (is_string($timestamp) && preg_match('/.\+0000$/', $timestamp)) {
            $ts = self::getTimeStampWithMicroseconds(new DateTime($timestamp, new DateTimeZone('UTC')));
        } elseif (is_string($timestamp) && preg_match('/0\.[\d]+\s[\d]+/', $timestamp)) {
            $ts = self::getTimeStamp($timestamp, true);
        } else {
            throw new common_Exception('Unexpected timestamp');
        }

        return self::getDateFormatter()->format($ts, $format, $timeZone);
    }

    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     * @param unknown $interval            
     * @param unknown $format            
     * @return string|Ambigous <string, string>
     */
    static public function displayInterval($interval, $format = self::FORMAT_INTERVAL_LONG)
    {
        if (is_object($interval)) {
            $intervalObj = $interval;
        } else {
            $intervalObj = new DateTime();
            $intervalObj->setTimestamp($interval);
        }
        $newDate = new \DateTime();
        $intervalObj = $intervalObj instanceof DateTime ? $newDate->diff($intervalObj, true) : $intervalObj;
        if (! $intervalObj instanceof DateInterval) {
            common_Logger::w('Unknown interval format ' . get_class($interval) . ' for ' . __FUNCTION__, 'TAO');
            return '';
        }
        
        $formatStrings = self::getNonNullIntervalFormats($intervalObj);
        if (empty($formatStrings)) {
            $returnValue = __("less than a minute");
        } else {
            $returnValue = '';
            switch ($format) {
                case self::FORMAT_INTERVAL_SHORT:
                    $returnValue = $intervalObj->format(array_shift($formatStrings));
                    break;
                case self::FORMAT_INTERVAL_LONG:
                    $returnValue = self::formatElapsed($intervalObj, $formatStrings);
                    break;
                default:
                    common_Logger::w('Unknown date format ' . $format . ' for ' . __FUNCTION__, 'TAO');
            }
        }
        return $returnValue;
    }

    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     * @param DateInterval $interval            
     * @param unknown $formatStrings            
     * @return string
     */
    static protected function formatElapsed(DateInterval $interval, $formatStrings)
    {
        $string = '';
        while (! empty($formatStrings)) {
            $string .= $interval->format(array_shift($formatStrings)) . (count($formatStrings) == 0 ? '' : (count($formatStrings) == 1 ? __(' and ') : ' '));
        }
        return $string;
    }

    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     * @param DateInterval $interval            
     * @return multitype:string Ambigous <string, string>
     */
    static private function getNonNullIntervalFormats(DateInterval $interval)
    {
        $formats = array();
        if ($interval->y > 0) {
            $formats[] = $interval->y == 1 ? __("%y year") : __("%y years");
        }
        if ($interval->m > 0) {
            $formats[] = $interval->m == 1 ? __("%m month") : __("%m months");
        }
        if ($interval->d > 0) {
            $formats[] = $interval->d == 1 ? __("%d day") : __("%d days");
        }
        if ($interval->h > 0) {
            $formats[] = $interval->h == 1 ? __("%h hour") : __("%h hours");
        }
        if ($interval->i > 0) {
            $formats[] = $interval->i == 1 ? __("%i minute") : __("%i minutes");
        }
        return $formats;
    }

    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     * @param unknown $microtime            
     * @return number
     */
    static function getTimeStamp($microtime, $microseconds = false)
    {
        $parts = array_reverse(explode(" ", $microtime));

        $timestamp = $microseconds && isset($parts[1])
            ? $parts[0] . '.' . str_replace('0.', '', sprintf('%0.6f', $parts[1]))
            : $parts[0];

        return $timestamp;
    }

    static function getTimeStampWithMicroseconds(DateTime $dt)
    {
        return join('.', array($dt->getTimestamp(), $dt->format('u')));
    }
}
