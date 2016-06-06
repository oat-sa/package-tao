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

/**
 * A response is on object associated to an interactino containing which are the
 * response into the interaction choices and the score regarding the answers
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoQTI
 * @see http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10073

 */
class Value extends Element
{
    protected function getUsedAttributes(){
        return array(
            'oat\\taoQtiItem\\model\\qti\\attribute\\BaseType',
            'oat\\taoQtiItem\\model\\qti\\attribute\\FieldIdentifier'
        );
    }

    /**
     * the QTI tag name as defined in QTI standard
     *
     * @access protected
     * @var string
     */
    protected static $qtiTagName = 'value';

    /**
     * Element value
     * @access protected
     * @var string
     */
    protected $value;

    public function __toString()
    {
        return $this->value;
    }

    /**
     * Set element value
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * Get element value
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
}