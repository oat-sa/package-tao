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
 * Short description of class tao_helpers_form_elements_Checkbox
 *
 * @abstract
 * @access public
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 * @package tao
 
 */
abstract class tao_helpers_form_elements_Checkbox  extends tao_helpers_form_elements_MultipleElement
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute widget
     *
     * @access protected
     * @var string
     */
    protected $widget = 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#CheckBox';

    /**
     * Store options that must be render as readonly
     * @var array
     */
    protected $readOnly = array();

    // --- OPERATIONS ---

    /**
     * Short description of method getRawValue
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return array
     */
    public function getRawValue()
    {
        return $this->values;
    }

    /**
     * @return array
     */
    protected function getReadOnly()
    {
        return $this->readOnly;
    }

    /**
     * @param array $readOnly
     */
    public function setReadOnly( array $readOnly )
    {
        $this->readOnly = $readOnly;
    }

    /**
     *
     * @param $option
     */
    public function addReadOnly( $option )
    {
        $this->readOnly[] = $option;
    }


}

