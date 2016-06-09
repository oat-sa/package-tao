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
 */

/**
 * Validates that the value is a valid email address.
 *
 * @author Aleh Hutnikau <hutnikau@1pt.com>
 * @package tao
 */
class tao_helpers_form_validators_Email extends tao_helpers_form_Validator
{
    /**
     * Overrides parent default message
     *
     * @return string
     */
    protected function getDefaultMessage()
    {
        return __('This is not a valid email address.');
    }

    /**
     * Validates a value to see if it is a valid email.
     *
     * @access public
     * @author Aleh Hutnikau <hutnikau@1pt.com>
     * @param string $values the value to be validated
     * @return boolean whether the value is a valid email
     */
    public function evaluate($values)
    {
        return (bool) filter_var($values, FILTER_VALIDATE_EMAIL); 
    }

}
