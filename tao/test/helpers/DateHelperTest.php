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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */
namespace oat\tao\helpers\test;

use oat\tao\test\TaoPhpUnitTestRunner;
use tao_helpers_Date;
use DateInterval;
use DateTime;

class DateHelperTest extends TaoPhpUnitTestRunner
{

    /**
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testDisplayDate()
    {
        $mybirthday = DateTime::createFromFormat('Y-m-d H:i', '1980-02-01 10:00');
        $this->assertEquals('01/02/1980 10:00:00', tao_helpers_Date::displayeDate($mybirthday));
        $this->assertEquals('01/02/1980 10:00:00', tao_helpers_Date::displayeDate($mybirthday, tao_helpers_Date::FORMAT_LONG));
        $this->assertEquals('1980-02-01 10:00', tao_helpers_Date::displayeDate($mybirthday, tao_helpers_Date::FORMAT_DATEPICKER));
        $this->assertEquals('February 1, 1980, 10:00:00 am', tao_helpers_Date::displayeDate($mybirthday, tao_helpers_Date::FORMAT_VERBOSE));
        
        $mybirthdayTs = $mybirthday->getTimeStamp();
        $this->assertEquals('01/02/1980 10:00:00', tao_helpers_Date::displayeDate($mybirthdayTs));
        $this->assertEquals('01/02/1980 10:00:00', tao_helpers_Date::displayeDate($mybirthdayTs, tao_helpers_Date::FORMAT_LONG));
        $this->assertEquals('1980-02-01 10:00', tao_helpers_Date::displayeDate($mybirthdayTs, tao_helpers_Date::FORMAT_DATEPICKER));
        $this->assertEquals('February 1, 1980, 10:00:00 am', tao_helpers_Date::displayeDate($mybirthdayTs, tao_helpers_Date::FORMAT_VERBOSE));
        
        $literal = new \core_kernel_classes_Literal($mybirthdayTs);
        $this->assertEquals('01/02/1980 10:00:00', tao_helpers_Date::displayeDate($literal));
        $this->assertEquals('01/02/1980 10:00:00', tao_helpers_Date::displayeDate($literal, tao_helpers_Date::FORMAT_LONG));
        $this->assertEquals('1980-02-01 10:00', tao_helpers_Date::displayeDate($literal, tao_helpers_Date::FORMAT_DATEPICKER));
        $this->assertEquals('February 1, 1980, 10:00:00 am', tao_helpers_Date::displayeDate($literal, tao_helpers_Date::FORMAT_VERBOSE));
    }

    /**
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testgetTimeStamp()
    {
        $microtime = "0.60227900 1425372507";
        $this->assertEquals(1425372507, tao_helpers_Date::getTimeStamp($microtime));
    }

    /**
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testDisplayInterval()
    {
        $now = new DateTime();
        
        $duration = new DateInterval('P1M'); // 1 month
        $now->add($duration);
        
        $this->assertEquals('1 month', tao_helpers_Date::displayInterval($duration));
        $this->assertEquals('1 month', tao_helpers_Date::displayInterval($duration, tao_helpers_Date::FORMAT_INTERVAL_LONG));
        
        $this->assertEquals('1 month', tao_helpers_Date::displayInterval($duration, tao_helpers_Date::FORMAT_INTERVAL_SHORT));
        
        $microtime = tao_helpers_Date::getTimeStamp(microtime());
        
        $this->assertEquals('1 hour', tao_helpers_Date::displayInterval($microtime - 3600, tao_helpers_Date::FORMAT_INTERVAL_SHORT));
        $this->assertEquals('1 minute', tao_helpers_Date::displayInterval($microtime - 60, tao_helpers_Date::FORMAT_INTERVAL_SHORT));
        $this->assertEquals('less than a minute', tao_helpers_Date::displayInterval($microtime - 10, tao_helpers_Date::FORMAT_INTERVAL_SHORT));
        
        $this->assertEquals('', tao_helpers_Date::displayInterval(new \core_kernel_classes_Literal('test'), tao_helpers_Date::FORMAT_INTERVAL_SHORT));
        $this->assertEquals('', tao_helpers_Date::displayInterval($microtime - 3600, 'bad format'));
    }
}