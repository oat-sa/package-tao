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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA ;
 */
/**
 * @author Jean-Sébastien Conan <jean-sebastien.conan@vesperiagroup.com>
 */

namespace oat\taoTests\models\runner\time;

/**
 * Class InconsistentRangeException
 *
 * Thrown when a time range is inconsistent.
 * By example when the TimePoint do not have the same target or are not correctly positioned.
 *
 * @package oat\taoTests\models\runner\time
 */
class InconsistentRangeException extends TimeException
{
    public function __construct($message = null, $code = 0, Exception $previous = null)
    {
        if (!$message) {
            $message = 'A time range must be defined by two TimePoint with the same target and the START cannot be after the END!';
        }

        parent::__construct($message, $code, $previous);
    }
}