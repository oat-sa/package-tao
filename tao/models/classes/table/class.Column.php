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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

/**
 * Short description of class tao_models_classes_table_Column
 *
 * @abstract
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package tao
 
 */
abstract class tao_models_classes_table_Column
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute label
     *
     * @access public
     * @var string
     */
    public $label = '';

    // --- OPERATIONS ---

    /**
     * Short description of method buildColumnFromArray
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  array array
     * @return tao_models_classes_table_Column
     */
    public static function buildColumnFromArray($array)
    {
        $returnValue = null;

        
        $type = $array['type'];
        unset($array['type']);
        $returnValue = $type::fromArray($array);
        

        return $returnValue;
    }

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string label
     * @return mixed
     */
    public function __construct($label)
    {
        
        $this->label = $label;
        
    }

    /**
     * Override this function with a concrete implementation
     *
     * @access protected
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  array array
     * @return tao_models_classes_table_Column
     */
    protected static function fromArray($array)
    {
        $returnValue = null;

        
        

        return $returnValue;
    }

    /**
     * Short description of method getLabel
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function getLabel()
    {
        $returnValue = (string) '';

        
        $returnValue = $this->label;
        

        return (string) $returnValue;
    }

    /**
     * Short description of method toArray
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return array
     */
    public function toArray()
    {
        $returnValue = array();

        
        $returnValue['type'] = get_class($this);
        $returnValue['label'] = $this->label;
        

        return (array) $returnValue;
    }

    /**
     * Short description of method getDataProvider
     *
     * @abstract
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return tao_models_classes_table_DataProvider
     */
    public abstract function getDataProvider();

} /* end of abstract class tao_models_classes_table_Column */

?>