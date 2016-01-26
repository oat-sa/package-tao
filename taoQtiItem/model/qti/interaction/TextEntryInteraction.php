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

namespace oat\taoQtiItem\model\qti\interaction;

use oat\taoQtiItem\model\qti\interaction\TextEntryInteraction;
use oat\taoQtiItem\model\qti\interaction\InlineInteraction;

/**
 * QTI Text Entry Interaction
 *
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package taoQTI
 * @see http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10333
 
 */
class TextEntryInteraction extends InlineInteraction
{

    /**
     * the QTI tag name as defined in QTI standard
     *
     * @access protected
     * @var string
     */
    protected static $qtiTagName = 'textEntryInteraction';
    static protected $choiceClass = '';
    static protected $baseType = 'string';

    protected function getUsedAttributes(){
        return array_merge(
                parent::getUsedAttributes(), array(
            'oat\\taoQtiItem\\model\\qti\\attribute\\Base',
            'oat\\taoQtiItem\\model\\qti\\attribute\\StringIdentifier',
            'oat\\taoQtiItem\\model\\qti\\attribute\\ExpectedLength',
            'oat\\taoQtiItem\\model\\qti\\attribute\\PatternMask',
            'oat\\taoQtiItem\\model\\qti\\attribute\\PlaceholderText'
                )
        );
    }

    public function getBaseType(){
        
        $returnValue = parent::getBaseType();

        $response = $this->getResponse();
        if(!is_null($response)){
            $authorizedBaseType = array(
                'string',
                'integer',
                'float'
            );
            $baseType = strtolower($response->getAttributeValue('baseType'));
            if(in_array($baseType, $authorizedBaseType)){
                $returnValue = $baseType;
            }
        }
        
        return $returnValue;
    }

}