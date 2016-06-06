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

/**
 * Validator to verify the identifiers of the property indexes
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package tao
 */
class tao_helpers_form_validators_IndexIdentifier
    extends tao_helpers_form_Validator
{

    /**
     * evalute the identifier
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  values
     * @return boolean
     */
    public function evaluate($values)
    {
        //evaluate identifier, starts with letter, contains letters, numbers and _, ends with letter, number
        if(preg_match("/^[a-z]+[a-z_0-9]*$/", $values) === 1){
            return true;
        } else {
            if (!isset($this->options['message'])) {
                $message = empty($values)
                    ? __('The index identifier should not be empty')
                    : __('"%s" is not a valid index identifier. It must start with a letter and contain letters, numbers or underscores only', $values)
                ; 
                $this->setMessage($message);
            }
        }
        return false;
    }

}
