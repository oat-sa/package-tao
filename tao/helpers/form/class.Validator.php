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
 * The validators enable you to perform a validation callback on a form element.
 * It's provide a model of validation and must be overridden.
 *
 * @abstract
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package tao
 
 */
abstract class tao_helpers_form_Validator
{
    // --- ASSOCIATIONS ---
    // generateAssociationEnd :     // generateAssociationEnd : 

    // --- ATTRIBUTES ---

    /**
     * Short description of attribute options
     *
     * @access protected
     * @var array
     */
    protected $options = array();

    /**
     * Short description of attribute message
     *
     * @access protected
     * @var string
     */
    protected $message = '';

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  array $options
     * @return mixed
     */
    public function __construct(array $options = array())
    {
        $this->setOptions($options);
    }

    /**
     * Short description of method getName
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function getName()
    {
        return (string) str_replace('tao_helpers_form_validators_', '', get_class($this));
    }

    /**
     * Short description of method getOptions
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return array
     */
    public function getOptions()
    {
        return (array) $this->options;
    }

    /**
     * Short description of method getMessage
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function getMessage()
    {
        return isset($this->options['message']) ? $this->options['message'] : $this->getDefaultMessage();;
    }

    /**
     * Short description of method getMessage
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function setMessage($message)
    {
        $this->options['message'] = $message;
    }
    
    /**
     * @return string
     */
    protected function getDefaultMessage()
    {
        return __('');
    }

    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    /**
     * Short description of method evaluate
     *
     * @abstract
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  values
     * @return boolean
     */
    public abstract function evaluate($values);

}