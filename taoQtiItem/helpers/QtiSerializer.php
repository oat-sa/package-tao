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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 * 
 */

namespace oat\taoQtiItem\helpers;

use \SimpleXMLElement;

/**
 * @access public
 * @package taoQtiItem
 */
class QtiSerializer
{
    
    /**
     * Parse a generic QTI element node into an array
     * 
     * @param SimpleXMLElement $xml
     * @return array
     */
    public static function parseElementXml(SimpleXMLElement $xml){

        $attributes = array();
        foreach($xml->attributes() as $name => $value){
            $attributes[$name] = (string) $value;
        }

        $returnValue = array(
            'qtiClass' => $xml->getName()
        );

        if(count($attributes)){
            $returnValue['attributes'] = $attributes;
        }

        return $returnValue;
    }
    
    /**
     * Parse a QTI expression node into an array
     * 
     * @see http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10569
     * @param SimpleXMLElement $xml
     * @return array
     */
    public static function parseExpressionXml(SimpleXMLElement $xml){
        $returnValue = self::parseElementXml($xml);
        $value = trim($xml);
        $expressions = array();
        foreach($xml->children() as $child){
            $expressions[] = self::parseExpressionXml($child);
        }
        if(count($expressions)){
            $returnValue['expressions'] = $expressions;
        }
        if(strlen($value)){
            $returnValue['value'] = $value;
        }
        return $returnValue;
    }
    
    /**
     * Parse a QTI responseRule node into an array
     * 
     * @see http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10408
     * @param SimpleXMLElement $xml
     * @return array
     */
    public static function parseResponseRuleXml(SimpleXMLElement $xml){
        $returnValue = self::parseElementXml($xml);
        foreach($xml->children() as $child){
            $returnValue['expression'] = self::parseExpressionXml($child);
            break;
        }
        return $returnValue;
    }
    
    /**
     * Parse a generic QTI element node that contains responseRules as children into an array
     * 
     * @param SimpleXMLElement $xml
     * @return array
     */
    private static function parseResponseRulesContainerXml(SimpleXMLElement $xml){
        $returnValue = self::parseElementXml($xml);
        $responseRules = array();
        foreach($xml->children() as $child){
            $name = $child->getName();
            $methodName = 'parse'.ucfirst($name).'Xml';

            if(method_exists(__CLASS__, $methodName)){
                $responseRules[] = self::$methodName($child);
            }else{
                $responseRules[] = self::parseResponseRuleXml($child);
            }
        }
        $returnValue['responseRules'] = $responseRules;
        return $returnValue;
    }
    
    /**
     * Parse a QTI responseProcessing node into an array
     * 
     * @see http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10712
     * @param SimpleXMLElement $xml
     * @return array
     */
    public static function parseResponseProcessingFragmentXml(SimpleXMLElement $xml){
        return self::parseResponseRulesContainerXml($xml);
    }
    
    /**
     * Parse a QTI responseProcessing node into an array
     * 
     * @see http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10413
     * @param SimpleXMLElement $xml
     * @return array
     */
    public static function parseResponseIfXml(SimpleXMLElement $xml){
        $returnValue = self::parseElementXml($xml);
        $i = 0;
        $expression = null;
        $responseRules = array();
        foreach($xml->children() as $child){
            if($i){
                $name = $child->getName();
                $methodName = 'parse'.ucfirst($name).'Xml';
                if(method_exists(__CLASS__, $methodName)){
                    $responseRules[] = self::$methodName($child);
                } else{
                    $responseRules[] = self::parseResponseRuleXml($child);
                }
            }else{
                //the first child is the expression
                $expression = self::parseExpressionXml($child);
            }
            $i++;
        }
        $returnValue['expression'] = $expression;
        $returnValue['responseRules'] = $responseRules;
        return $returnValue;
    }
    
    /**
     * Parse a QTI responseProcessing node into an array
     * 
     * @see http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10419
     * @param SimpleXMLElement $xml
     * @return array
     */
    public static function parseResponseElseXml(SimpleXMLElement $xml){
        return self::parseResponseRulesContainerXml($xml);
    }
    
    /**
     * Parse a QTI responseProcessing node into an array
     * 
     * @see http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10409
     * @param SimpleXMLElement $xml
     * @return array
     */
    public static function parseResponseConditionXml(SimpleXMLElement $xml){
        $returnValue = self::parseElementXml($xml);
        foreach($xml->responseIf as $responseIfXml){
            $returnValue['responseIf'] = self::parseResponseIfXml($responseIfXml);
            break;
        }
        foreach($xml->responseElseIf as $responseIfXml){
            if(!isset($returnValue['responseElseIfs'])){
                $returnValue['responseElseIfs'] = array();
            }
            $returnValue['responseElseIfs'][] = self::parseResponseIfXml($responseIfXml);
        }
        foreach($xml->responseElse as $responseIfXml){
            $returnValue['responseElse'] = self::parseResponseElseXml($responseIfXml);
            break;
        }
        return $returnValue;
    }
    
    /**
     * Parse a QTI responseProcessing node into an array
     * 
     * @see http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10404
     * @param SimpleXMLElement $xml
     * @return array
     */
    public static function parseResponseProcessingXml(SimpleXMLElement $xml){
        return self::parseResponseRulesContainerXml($xml);
    }

}
