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

/**
 * Helper for automatisation of Xsl transformation
 * @access public
 * @author Matteo Melis
 * @package taoItems
 
 */
class taoItems_helpers_Xslt
{
	// namespace for the set parameters function
	const PARAMS_NS = '';
	// xlst namespace short and full
	const XSLT_NS = 'http://www.w3.org/1999/XSL/Transform';
	const XSLT_SHORT_NS = 'xsl';

	/**
	 *	Transform the given xml with the given stylesheet adding params if necessary
	 *
	 * @param string $xml the xml raw or filepath
	 * @param string $xsl the xsl raw or filepath
	 * @param array $params  key = name of param and value = value of param
	 * @return string the result of transformation
	 */
	public static function quickTransform($xml, $xsl, $params = array())
	{
		// load xml
		$xml_dom = self::getDOM($xml);
		// get the xsl preparated
		$xsl_dom = self::prepareXsl($xsl, $params);
		// return the transformation
		return self::transform($xml_dom, $xsl_dom, $params);
	}

	/**
	 * return the xslt processor with the xsl stylesheet loaded
	 * @param DOMDocument $xsl
	 * @return XSLTProcessor
	 */
	public static function getProcessor($xsl_dom) {
		// get the processor and import stylesheet xsl
		$xsl_processor = new XSLTProcessor();
		$xsl_processor->importStylesheet($xsl_dom);
		return $xsl_processor;
	}

	/**
	 * prepare the xsl stylesheet and add params dynamically
	 * @param string $xsl
	 * @param array $params
	 * @return DOMDocument
	 */
	public static function prepareXsl($xsl, $params = array()) {
		// load xsl
		$xsl_dom = self::getDOM($xsl);
		// a way to use param xsl dynamically
		return self::addParams($xsl_dom, $params); // voir avec lionel si peut rester la
	}

	/**
	 *
	 * @param mixed $xml (string or DOMDocument
	 * @param XSLTProcessor $xsl_proc
	 * @param array $params
	 * @return string the result of transformation
	 */
	public static function transform($xml, $xsl, $params = array())
	{
		// if xml is string tranform to DOM
		if(is_string($xml)) {
			$xml = self::getDOM($xml);
		}
		// if xsl is string tranform to DOM
		if(is_string($xsl)) {
			$xsl = self::getDOM($xsl);
		}
		//get the processor with the stylesheet
		$xsl_proc = self::getProcessor($xsl);
		// set values for params
		if (sizeof($params)) {
			$xsl_proc->setParameter(self::PARAMS_NS, $params);
		}
		// transformation
		return $xsl_proc->transformToXML($xml);
	}

	/**
	 * return the DOM corresponding to the parameter
	 * @param string $str
	 * @return DOMDocument
	 */
	public static function getDOM($str)
	{
		//create domdocument
		$dom = new DOMDocument();
		//load by file or string
		is_file($str) ? $dom->load($str) : $dom->loadXML($str);
		// return the DOMDoc
		return $dom;
	}

	/**
	 * insert the tag param for each params noexistent in xsl stylesheet
	 * @param DOMPDocument $dom
	 * @return DOMDocument
	 */
	private static function addParams($dom, $params)
	{
		if(!sizeof($params)) {
			return $dom;
		}
		// get xpath
		$xpath = new DOMXPath($dom);
		//registering namespace xsl
		$xpath->registerNamespace(self::XSLT_SHORT_NS, self::XSLT_NS);
		// get the params tag direct child of the stylesheet (don't touch other params as template for example)
		$items = $xpath->query('/' . self::XSLT_SHORT_NS . ':stylesheet|' . self::XSLT_SHORT_NS . ':transform/' . self::XSLT_SHORT_NS . ':param');
		// list existing to don't duplicate it
		$existing = array();
		foreach ($items as $param) {
			$existing[] = $param->getAttribute('name');
		}
		// for each item in param
		foreach ($params as $name => $val) {
			// if not exist in dom
			if(!in_array($name, $existing)) {
				// we add it to the dom
				$node = $dom->createElementNS(self::XSLT_NS, self::XSLT_SHORT_NS . ":param");
				$node->setAttribute('name', $name);
				// log to say param added dynamically
				common_Logger::w('Parameters "' . $name . '"added automatically by the ' . get_class() . ':addParams \'s function because it was missing in the xsl stylesheet');
			}
		}
		return $dom;
	}
}
?>