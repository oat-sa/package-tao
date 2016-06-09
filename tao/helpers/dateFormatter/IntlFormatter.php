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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 *
 *
 */

namespace oat\tao\helpers\dateFormatter;

use common_session_SessionManager;
use DateTimeZone;
use IntlDateFormatter;
use oat\oatbox\Configurable;

/**
 * Utility to display dates.
 *
 * @author Joel Bout, <joel@taotesting.com>
 * @package tao
 *
 */
class IntlFormatter extends Configurable implements Formatter
{
    public function format($timestamp, $format, DateTimeZone $timeZone = null)
    {
        $locale = common_session_SessionManager::getSession()->getInterfaceLanguage();
        if(is_null($timeZone)){
            $timeZone = new DateTimeZone(common_session_SessionManager::getSession()->getTimeZone());
        }

        switch ($format) {
            case \tao_helpers_Date::FORMAT_LONG:
                $dateFormat = IntlDateFormatter::SHORT;
                $timeFormat = IntlDateFormatter::MEDIUM;
                break;
            case \tao_helpers_Date::FORMAT_DATEPICKER:
            case \tao_helpers_Date::FORMAT_ISO8601:
                // exception
                $altFormatter = new EuropeanFormatter();
                return $altFormatter->format($timestamp, $format);
            case \tao_helpers_Date::FORMAT_VERBOSE:
                $dateFormat = IntlDateFormatter::LONG;
                $timeFormat = IntlDateFormatter::MEDIUM;
                break;
            default:
                throw new \common_Exception('Unexpected date format "' . $format . '"');
        }

        $formatter = new IntlDateFormatter($locale, $dateFormat, $timeFormat, $timeZone);

        return $formatter->format($timestamp);
    }
}
