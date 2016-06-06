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
 * Short description of class tao_helpers_form_validators_Url
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 
 */
class tao_helpers_form_validators_Url
    extends tao_helpers_form_Validator
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  array options
     * @return mixed
     */
    public function __construct($options = array())
    {
    	parent::__construct($options);
    }

    /**
     * @param string $value
     * @return bool
     */
    public function evaluate($value)
    {
        //backward compatible behavior:
        //scheme should be prepended if not found (pattern includes spelling errors)
        if( preg_match('/^[a-zA-Z]{1,10}[:\/]{1,3}/',$value) === false ){
            $value = 'http://' . $value;
        }

        $returnValue = !(filter_var($value, FILTER_VALIDATE_URL) === false);

        //'isset' is backward compatible behavior
        if( !isset( $this->options['allow_parameters'] ) ){
            $returnValue = $returnValue && (strpos($value, '?') === false);
        }

        return $returnValue;
    }

    /**
     * Default error message
     *
     * @return string
     */
    protected function getDefaultMessage()
    {
        return __('Provided URL is not valid');
    }

}

?>