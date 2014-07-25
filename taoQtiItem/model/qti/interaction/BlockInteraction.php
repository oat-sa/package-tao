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

use oat\taoQtiItem\model\qti\interaction\BlockInteraction;
use oat\taoQtiItem\model\qti\interaction\Interaction;
use oat\taoQtiItem\model\qti\Item;
use oat\taoQtiItem\model\qti\interaction\Prompt;

/**
 * The QTI block interaction is a subclass of the main QTI Interaction class
 *
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package taoQTI
 * @see http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10267
 
 */
abstract class BlockInteraction extends Interaction
{

    /**
     * The prompt of the block interaction
     *
     * @access protected
     * @var Prompt
     */
    protected $prompt = null;

    /**
     * Create a new block interaction, init the prompt block
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @param  array options
     * @return mixed
     */
    public function __construct($attributes = array(), Item $relatedItem = null, $serial = ''){
        parent::__construct($attributes, $relatedItem, $serial);
        $this->prompt = new Prompt(array(), $relatedItem);
    }

    /**
     * Return the ContainerStatic reprensenting the prompt
     * 
     * @return oat\taoQtiItem\model\qti\container\ContainerStatic
     */
    public function getPrompt(){
        return $this->prompt->getBody();
    }
    
    public function getPromptObject(){
        return  $this->prompt;
    }

    public function setPrompt($body){
        $this->prompt->getBody()->edit($body);
    }

    public function toArray($filterVariableContent = false, &$filtered = array()){
        $returnValue = parent::toArray($filterVariableContent, $filtered);
        $returnValue['prompt'] = $this->getPrompt()->toArray($filterVariableContent, $filtered);
        return $returnValue;
    }

    public static function getTemplateQti(){
        return static::getTemplatePath().'interactions/qti.blockInteraction.tpl.php';
    }

    protected function getTemplateQtiVariables(){
        $variables = parent::getTemplateQtiVariables();
        if(trim($this->getPrompt()->getBody()) !== ''){
            //prompt is optional:
            $variables['prompt'] = $this->prompt->toQTI();
        }
        return $variables;
    }

}