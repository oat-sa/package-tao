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

use oat\oatbox\Configurable;
use DateTime;
use DateTimeZone;
use common_Logger;
use common_session_SessionManager;
/**
 * Utility to display dates.
 *
 * @author Joel Bout, <joel@taotesting.com>
 * @package tao
 *         
 */
class EuropeanFormatter extends Configurable implements Formatter
{

    public function format($timestamp, $format, DateTimeZone $timeZone = null)
    {
        $dateTime = new DateTime();
        $dateTime->setTimestamp($timestamp);
		if(is_null($timeZone)){
			$timeZone = new DateTimeZone(common_session_SessionManager::getSession()->getTimeZone());
		}
        $dateTime->setTimezone($timeZone);
        
        switch ($format) {
        	case \tao_helpers_Date::FORMAT_LONG:
        	    $formatString = 'd/m/Y H:i:s';
        	    break;
        	case \tao_helpers_Date::FORMAT_DATEPICKER:
        	    $formatString = 'Y-m-d H:i';
        	    break;
        	case \tao_helpers_Date::FORMAT_VERBOSE:
        	    $formatString = 'F j, Y, g:i:s a';
        	    break;
			case \tao_helpers_Date::FORMAT_ISO8601:
				$milliseconds = str_replace('0.', '', sprintf('%0.3f', fmod($timestamp, 1)));
				$formatString = 'Y-m-d\TH:i:s.'.$milliseconds;
				break;
        	default:
        	    common_Logger::w('Unknown date format ' . $format . ' for ' . __FUNCTION__, 'TAO');
        	    $formatString = '';
        }
        
        return $dateTime->format($formatString);
    }

}
