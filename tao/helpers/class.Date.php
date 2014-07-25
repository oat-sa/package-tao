<?php
/*  
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

/**
 * Utility to display dates.
 *
 * @author Joel Bout, <joel@taotesting.com>
 * @package tao
 
 */
class tao_helpers_Date
{
	const FORMAT_LONG		= 0;
	const FORMAT_VERBOSE	= 1;
	const FORMAT_DATEPICKER	= 2;
	
    /**
     * Dispalys a date/time
     * Should in theorie be dependant on the users locale and timezone
     *
     * @param  mixed timestamp
     * @param  int format The date format. See tao_helpers_Date's constants.
     * @return string The formatted date.
     */
    public static function displayeDate($timestamp, $format = self::FORMAT_LONG)
    {
    	$returnValue = '';
        
    	if (is_object($timestamp) && $timestamp instanceof core_kernel_classes_Literal) {
            $dateTime = new DateTime();
            $dateTime->setTimestamp($timestamp->__toString());
        } else if (is_object($timestamp) && $timestamp instanceof DateTime){
            $dateTime = $timestamp;
        } else {
            $dateTime = new DateTime();
            $dateTime->setTimestamp($timestamp);
        }
    	$dateTime->setTimezone(new DateTimeZone(common_session_SessionManager::getSession()->getTimeZone()));
    	switch ($format) {
    		case self::FORMAT_LONG :
    			$returnValue = $dateTime->format('d/m/Y H:i:s');
    			break;
    		case self::FORMAT_DATEPICKER :
    			$returnValue = $dateTime->format('Y-m-d H:i');
    			break;
    		case self::FORMAT_VERBOSE :
    			$returnValue = $dateTime->format('F j, Y, g:i:s a');
    			break;
    		default:
    			common_Logger::w('Unkown date format '.$format.' for '.__FUNCTION__, 'TAO');
    	}
    	return $returnValue;
    }

   static function getTimeStamp($microtime)
    {
    list($usec, $sec) = explode(" ", $microtime);
    return ((float)$sec);
    }

}