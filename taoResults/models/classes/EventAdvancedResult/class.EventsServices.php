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

error_reporting(E_ALL);

/**
 * TAO - taoResults\models\classes\EventAdvancedResult\class.EventsServices.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 24.01.2011, 11:45:52 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Younes Djaghloul, <younes.djaghloul@tudor.lu>
 * @package taoResults
 * @subpackage models_classes_EventAdvancedResult
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 10-13-1--65-426da5fe:12d7ece565e:-8000:0000000000002AAF-includes begin
// section 10-13-1--65-426da5fe:12d7ece565e:-8000:0000000000002AAF-includes end

/* user defined constants */
// section 10-13-1--65-426da5fe:12d7ece565e:-8000:0000000000002AAF-constants begin
// section 10-13-1--65-426da5fe:12d7ece565e:-8000:0000000000002AAF-constants end

/**
 * Short description of class
 *
 * @access public
 * @author Younes Djaghloul, <younes.djaghloul@tudor.lu>
 * @package taoResults
 * @subpackage models_classes_EventAdvancedResult
 */
class taoResults_models_classes_EventAdvancedResult_EventsServices
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute currentArray
     *
     * @access private
     * @var array
     */
    private $currentArray = array();

    /**
     * Short description of attribute currentXML
     *
     * @access public
     * @var string
     */
    public $currentXML = '';

    /**
     * Short description of attribute currentSimpleXml
     *
     * @access public
     * @var array
     */
    public $currentSimpleXml = array();

    /**
     * Short description of attribute initialXml
     *
     * @access public
     * @var string
     */
    public $initialXml = '';

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Younes Djaghloul, <younes.djaghloul@tudor.lu>
     * @param  string xml
     * @return mixed
     */
    public function __construct($xml = null)
    {
        // section 10-13-1--65-426da5fe:12d7ece565e:-8000:0000000000002AF5 begin
        // section 10-13-1--65-426da5fe:12d7ece565e:-8000:0000000000002AF5 end
    }

    /**
     * Coverts a simple array to an XML document
     *
     * @access public
     * @author Younes Djaghloul, <younes.djaghloul@tudor.lu>
     * @param  array arrInput
     * @param  string rootName
     * @param  string principaleNodetName
     * @return string
     */
    public function simpleArrayToXml($arrInput, $rootName, $principaleNodetName)
    {
        $returnValue = (string) '';

        // section 10-13-1--65-426da5fe:12d7ece565e:-8000:0000000000002B18 begin
        // section 10-13-1--65-426da5fe:12d7ece565e:-8000:0000000000002B18 end

        return (string) $returnValue;
    }

    /**
     * apply xPath and return an array od simpleXml Element
     *
     * @access public
     * @author Younes Djaghloul, <younes.djaghloul@tudor.lu>
     * @param  string query
     * @param  string xmlSource
     * @return array
     */
    public function queryXml($query, $xmlSource = 'currentXml')
    {
        $returnValue = array();

        // section 10-13-1--65-426da5fe:12d7ece565e:-8000:0000000000002B1E begin
        // section 10-13-1--65-426da5fe:12d7ece565e:-8000:0000000000002B1E end

        return (array) $returnValue;
    }

    /**
     * query a simple array
     *
     * @access public
     * @author Younes Djaghloul, <younes.djaghloul@tudor.lu>
     * @param  string query
     * @param  array arraySource
     * @return array
     */
    public function queryArray($query, $arraySource)
    {
        $returnValue = array();

        // section 10-13-1--65-426da5fe:12d7ece565e:-8000:0000000000002B26 begin
        // section 10-13-1--65-426da5fe:12d7ece565e:-8000:0000000000002B26 end

        return (array) $returnValue;
    }

    /**
     * set Xml element Value
     *
     * @access private
     * @author Younes Djaghloul, <younes.djaghloul@tudor.lu>
     * @param  string elementName
     * @param  string elementValue
     * @param  string query
     * @param  string typeOfElement
     * @return string
     */
    private function setElementValue($elementName, $elementValue, $query, $typeOfElement = 'attribute')
    {
        $returnValue = (string) '';

        // section 10-13-1--65-426da5fe:12d7ece565e:-8000:0000000000002B2E begin
        // section 10-13-1--65-426da5fe:12d7ece565e:-8000:0000000000002B2E end

        return (string) $returnValue;
    }

    /**
     * Set the value of the attribute. Returns XML
     *
     * @access public
     * @author Younes Djaghloul, <younes.djaghloul@tudor.lu>
     * @param  string attributeName
     * @param  string attributeValue
     * @param  string query
     * @return string
     */
    public function setAttributesValue($attributeName, $attributeValue, $query)
    {
        $returnValue = (string) '';

        // section 10-13-1--65-426da5fe:12d7ece565e:-8000:0000000000002B3A begin
        // section 10-13-1--65-426da5fe:12d7ece565e:-8000:0000000000002B3A end

        return (string) $returnValue;
    }

    /**
     * Set the value of nodes according to query. returns XML
     *
     * @access public
     * @author Younes Djaghloul, <younes.djaghloul@tudor.lu>
     * @param  string nodeName
     * @param  string nodeValue
     * @param  string query
     * @return string
     */
    public function setNodesValue($nodeName, $nodeValue, $query)
    {
        $returnValue = (string) '';

        // section 10-13-1--65-426da5fe:12d7ece565e:-8000:0000000000002B40 begin
        // section 10-13-1--65-426da5fe:12d7ece565e:-8000:0000000000002B40 end

        return (string) $returnValue;
    }

    /**
     * Short description of method addAttributeForAllNodes
     *
     * @access public
     * @author Younes Djaghloul, <younes.djaghloul@tudor.lu>
     * @param  string attName
     * @param  string attValue
     * @return string
     */
    public function addAttributeForAllNodes($attName, $attValue = '')
    {
        $returnValue = (string) '';

        // section 10-13-1--65-426da5fe:12d7ece565e:-8000:0000000000002B47 begin
        // section 10-13-1--65-426da5fe:12d7ece565e:-8000:0000000000002B47 end

        return (string) $returnValue;
    }

    /**
     * Short description of method getValueOfVariable
     *
     * @access public
     * @author Younes Djaghloul, <younes.djaghloul@tudor.lu>
     * @param  string index
     * @param  string variableName
     * @return string
     */
    public function getValueOfVariable($index, $variableName)
    {
        $returnValue = (string) '';

        // section 10-13-1--65-426da5fe:12d7ece565e:-8000:0000000000002B4D begin
        // section 10-13-1--65-426da5fe:12d7ece565e:-8000:0000000000002B4D end

        return (string) $returnValue;
    }

} /* end of class taoResults_models_classes_EventAdvancedResult_EventsServices */

?>