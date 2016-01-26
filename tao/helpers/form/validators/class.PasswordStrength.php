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
 * @author Konstantin Sasim <sasim@1pt.com>
 * @license GPLv2
 * @package tao
 *
 */

use oat\generis\model\user\PasswordConstraintsService;

class tao_helpers_form_validators_PasswordStrength extends tao_helpers_form_Validator
{
    /**
     * Short description of method evaluate
     *
     * @access public
     * @param  values
     * @return boolean
     */
    public function evaluate($values)
    {
        $returnValue = PasswordConstraintsService::singleton()->validate($values);

        if( !$returnValue && !isset($this->options['message']) ){
            $this->setMessage(implode( ', ', PasswordConstraintsService::singleton()->getErrors()));
        }

        return (bool) $returnValue;
    }

}