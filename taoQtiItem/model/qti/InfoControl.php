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
 */

namespace oat\taoQtiItem\model\qti;

use oat\taoQtiItem\model\qti\Element;
use oat\taoQtiItem\model\qti\ParserFactory;
use \DOMElement;

/**
 * Class representing QTI standatd InfoControl
 * 
 * @see http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10433a
 * @access public
 * @author Sam, <sam@taotestin.com>
 * @package taoQtiItem
 
 */
abstract class InfoControl extends Element
{

    /**
     * the QTI tag name as defined in QTI standard
     *
     * @access protected
     * @var string
     */
    protected static $qtiTagName = 'infoControl';
    
    protected $typeIdentifier = '';//to be set in advance, read only, non editable
    protected $markup = '';
    
    public function getUsedAttributes(){
        return array(
            'oat\\taoQtiItem\\model\\qti\\attribute\\Title'
        );
    }
    
    public function getMarkup(){
        return $this->markup;
    }

    public function setMarkup($markup){
        $this->markup = (string) $markup;
    }

    public function toArray($filterVariableContent = false, &$filtered = array()){
        
        $returnValue = parent::toArray($filterVariableContent, $filtered);
        
        $returnValue['typeIdentifier'] = $this->typeIdentifier;
        $returnValue['markup'] = $this->markup;
        
        return $returnValue;
    }

    public static function getTemplateQti(){
        return static::getTemplatePath().'interactions/qti.infoControlInteraction.tpl.php';
    }

    protected function getTemplateQtiVariables(){
        
        $variables = parent::getTemplateQtiVariables();
        
        $variables['typeIdentifier'] = $this->typeIdentifier;
        $variables['markup'] = $this->markup;
        
        return $variables;
    }
    
    public function feed(ParserFactory $parser, DOMElement $data){
        
        $markup = $parser->getBodyData($data->item(0), true);
        $this->setMarkup($markup);
    }

}