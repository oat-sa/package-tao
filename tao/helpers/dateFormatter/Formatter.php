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

/**
 * Interface for the dateFormatter
 *
 * @author Joel Bout, <joel@taotesting.com>
 * @package tao
 *         
 */
interface Formatter 
{
    /**
     * 
     * @param long $timestamp
     * @param string $format see constants of tao_helpers_Date for possible values
     * @param \DateTimeZone $timeZone
     * @return string the formatted date
     * @throws \common_exception_Error
     */
    public function format($timestamp, $format, \DateTimeZone $timeZone);

}