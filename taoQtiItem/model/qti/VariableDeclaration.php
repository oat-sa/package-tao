<?php
/*
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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */

namespace oat\taoQtiItem\model\qti;

use oat\taoQtiItem\model\qti\IdentifiedElement;

/**
 * An outcome is a data build in item output. The SCORE is one of the most
 * outcomes.
 *
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package taoQTI
 * @see http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10037
 
 */
abstract class VariableDeclaration extends IdentifiedElement
{

    /**
     * Short description of attribute defaultValue
     *
     * @access protected
     * @var string
     */
    protected $defaultValue = null;

    protected function getUsedAttributes(){
        return array(
            'oat\\taoQtiItem\\model\\qti\\attribute\\Cardinality',
            'oat\\taoQtiItem\\model\\qti\\attribute\\BaseType'
        );
    }

    /**
     * Short description of method getDefaultValue
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @return string
     */
    public function getDefaultValue(){
        return $this->defaultValue;
    }

    /**
     * Short description of method setDefaultValue
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @param  string value
     * @return mixed
     */
    public function setDefaultValue($value){
        $this->defaultValue = $value;
    }
    
    /**
     * Overwrite parent because of infinite call loop
     * @param string $className
     * @return array
     */
    public function getComposingElements($className = ''){
        return array();
    }

}