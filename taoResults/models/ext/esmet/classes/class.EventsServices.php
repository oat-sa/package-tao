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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>

<?php

class eventsServices {

    private $currentArray;
    public $currentXml;
    public $currentSimpleXml;
    private $initialXml;

    public function __construct($xml=null) {
        $this->initialXml = $xml;
        $this->currentXml = $xml;
    }
/*
 * Covert a simple array to an XML document
 */
    public function simpleArrayToXml($arrInput, $rootName, $principaleNodetName) {
        //Create the root
        // we are using simple XML API in this implementation
        $xmlDoc = new SimpleXMLElement("<?xml version='1.0' encoding='utf-8'?><" . $rootName . "/>");
        //parse the array and create the XML
        foreach ($arrInput as $arrayEvent) {
            //ArrayEvent is a tuple of varables, each event has a different variables accorinf to its type
            //ArrayEvent is an associatif array
            $principaleNode = $xmlDoc->addChild($principaleNodetName);
            foreach ($arrayEvent as $varName => $varValue) {
                //add the element
                $principaleNode->addChild($varName, $varValue);
            }
        }
        $this->currentXml = $xmlDoc->asXML();

        return $xmlDoc->asXML();
    }
// end function
    //apply xPath and return an array od simpleXml Element

    public function queryXml($query, $xmlSource='currentXml') {
        $usedXml = $xmlSource;
        if ($xmlSource == 'currentXml') {
            $usedXml = $this->currentXml;
        }
        $xmlDoc = simplexml_load_string($usedXml);
        $afterQuery = $xmlDoc->xpath($query);
        //return the result as an array of SimplXLMelement
        return $afterQuery;
    }

    //query a smple array
    public function queryArray($query, $arraySource) {
        $usedArray = $arraySource;
        $arrayQuery = '//arrayElement[' . $query . ']';

        //convert the array to an xml
        $xml = $this->simpleArrayToXml($usedArray, "root", 'arrayElement');
        //Apply the query on the new XML
        $res = $this->queryXml($arrayQuery, $xml);

        foreach ($res as $tab) {
            //parse the varibales of the tab and build the row
            foreach ($tab as $tabVar) {// we can do Key=>value but we use methodes includes in simpleXml
                $nodeName = $tabVar->getName();
                $nodeValue = (string) $tab->$nodeName; // we have to cast otherwise we get an array not a value
                $arrayRow[$nodeName] = $nodeValue; //'$tab->$nodeName';
            }//
            // uild the global array
            $resArray[] = $arrayRow;
        }

        return $resArray;
    }

    //set Xml element Value
    private function setElementValue($elementName, $elementValue, $query, $typeOfElement='attribute') {

        $usedXml = $this->currentXml;
        //filter the XML
        $xmlDoc = simplexml_load_string($usedXml);
        $filtredNodes = $xmlDoc->xpath($query);
          foreach ($filtredNodes as $node) {
            if ($typeOfElement == "attribute") {
                $node->attributes()->$elementName = $elementValue;
            }

            if ($typeOfElement == "node") {
                $node->$elementName = $elementValue;
            }
        }

        $this->currentXml = $xmlDoc->asXml();

        return $this->currentXml;
    }

//Set the value of the attribute
    public function setAttributesValue($attributeName, $attributeValue, $query) {
        $xml = $this->setElementValue($attributeName, $attributeValue, $query, 'attribute');
        
        return $xml;
    }

//Set the value of nodes according to query
    public function setNodesValue($nodeName, $nodeValue, $query) {
        $xml = $this->setElementValue($nodeName, $nodeValue, $query, 'node');
        return $xml;
    }

//add attribute to xml
    public function addAttributeForAllNodes($attName, $attValue='') {
        $xml = $this->currentXml;
        $xmlDoc = simplexml_load_string($xml);
        foreach ($xmlDoc as $node) {
            $node->addAttribute($attName, $attValue);
        }
        $xml = $xmlDoc->asXML();
        $this->currentXml = $xml;
        return $xml;
    }

}
?>
