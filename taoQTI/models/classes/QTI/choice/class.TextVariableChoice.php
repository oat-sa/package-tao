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
 * Copyright (c) 2013 (original work) Open Assessment Techonologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */

/**
 * A choice is a kind of interaction's proposition.
 *
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package taoQTI
 * @subpackage models_classes_QTI
 */
abstract class taoQTI_models_classes_QTI_choice_TextVariableChoice extends taoQTI_models_classes_QTI_choice_Choice
{

    protected $text = '';

    public function getContent(){
        return $this->text;
    }

    public function setContent($content){
        if(empty($content)){
            $content = strval($content);
        }
        if(is_string($content)){
            $this->text = $content;
        }elseif($content instanceof taoQTI_models_classes_QTI_OutcomeDeclaration){
            //@todo: check validity
            $this->text = $content;
        }else{
            throw new InvalidArgumentException('a TextVariable Choice can only accept QTI_Outcome or text content');
        }
    }

    protected function getTemplateQtiVariables(){
        //use the default qti.element.tpl.php
        $variables = parent::getTemplateQtiVariables();
        $variables['body'] = (string) $this->text;
        return $variables;
    }

    public function toArray(){
        $returnValue = parent::toArray();
        $returnValue['text'] = (string) $this->text;
        return $returnValue;
    }

}