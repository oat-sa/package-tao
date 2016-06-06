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

use oat\taoQtiItem\model\qti\interaction\SliderInteraction;
use oat\taoQtiItem\model\qti\interaction\BlockInteraction;

/**
 * QTI Choice Interaction
 *
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package taoQTI
 * @see http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10384
 
 */
class SliderInteraction extends BlockInteraction
{

    /**
     * the QTI tag name as defined in QTI standard
     *
     * @access protected
     * @var string
     */
    protected static $qtiTagName = 'sliderInteraction';
    static protected $choiceClass = '';
    static protected $baseType = 'float';

    protected function getUsedAttributes(){
        return array_merge(
                parent::getUsedAttributes(), array(
            'oat\\taoQtiItem\\model\\qti\\attribute\\LowerBoundSliderInteraction',
            'oat\\taoQtiItem\\model\\qti\\attribute\\UpperBoundSliderInteraction',
            'oat\\taoQtiItem\\model\\qti\\attribute\\Step',
            'oat\\taoQtiItem\\model\\qti\\attribute\\StepLabel',
            'oat\\taoQtiItem\\model\\qti\\attribute\\Orientation',
            'oat\\taoQtiItem\\model\\qti\\attribute\\Reverse'
                )
        );
    }
    
    public function getBaseType(){
        
        $returnValue = parent::getBaseType();

        $response = $this->getResponse();
        if(!is_null($response)){
            $authorizedBaseType = array(
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