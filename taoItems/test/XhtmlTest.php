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
* Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
*               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
*
*/
use oat\tao\test\TaoPhpUnitTestRunner;
include_once dirname(__FILE__) . '/../includes/raw_start.php';

/**
 * This class aims at testing tao_helpers_Xhtml.
 *
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 * @package taoItems
 
 */
class XhtmlTestCase extends TaoPhpUnitTestRunner {

	public function testGetScriptElements() {
		$file = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'samples' . DIRECTORY_SEPARATOR . 'xhtml' . DIRECTORY_SEPARATOR . 'raw.html';
		try{
			$dom = new DOMDocument('1.0', TAO_DEFAULT_ENCODING);
			if (@$dom->load($file)){
				$this->assertEquals(2, count(taoItems_helpers_Xhtml::getScriptElements($dom, '/^jquery-/')));
				$this->assertEquals(0, count(taoItems_helpers_Xhtml::getScriptElements($dom, '/^jQuery-/')));
			}
			else{
				$this->assertTrue(false, "An error occured while loading '${file}'.");
			}
		}
		catch (DOMException $e){
			$this->assertTrue(false, "An error occured while parsing '${file}'.");
		}
	}
	
	public function testHasScriptElements(){
		$file = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'samples' . DIRECTORY_SEPARATOR . 'xhtml' . DIRECTORY_SEPARATOR . 'raw.html';
		try{
			$dom = new DOMDocument('1.0', TAO_DEFAULT_ENCODING);
			if (@$dom->load($file)){
				$this->assertNotEquals(taoItems_helpers_Xhtml::getScriptElements($dom, '/^jquery-/'),false);
				$this->assertNotNull(taoItems_helpers_Xhtml::getScriptElements($dom, '/^jquery-/'),false);
			}
			else{
				$this->assertTrue(false, "An error occured while loading '${file}'.");
			}
		}
		catch (DOMException $e){
			$this->assertTrue(false, "An error occured while parsing '${file}'.");
		}
	}
	
	public function testRemoveScriptElements(){
		$file = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'samples' . DIRECTORY_SEPARATOR . 'xhtml' . DIRECTORY_SEPARATOR . 'raw.html';
		try{
			$dom = new DOMDocument('1.0', TAO_DEFAULT_ENCODING);
			if (@$dom->load($file)){
				$this->assertEquals(2, taoItems_helpers_Xhtml::removeScriptElements($dom, '/^jquery-/'));
				$this->assertFalse(taoItems_helpers_Xhtml::hasScriptElements($dom, '/jquery/'));
			}
			else{
				$this->assertTrue(false, "An error occured while loading '${file}'.");
			}
		}
		catch (DOMException $e){
			$this->assertTrue(false, "An error occured while parsing '${file}'.");
		}
	}
	
	public function testAddScriptElement(){
		$file = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'samples' . DIRECTORY_SEPARATOR . 'xhtml' . DIRECTORY_SEPARATOR . 'raw.html';
		try{
			$dom = new DOMDocument('1.0', TAO_DEFAULT_ENCODING);
			if (@$dom->load($file)){
				// -- Append
				taoItems_helpers_Xhtml::addScriptElement($dom, 'scripts/taoMatching.js');
				$addedElements = taoItems_helpers_Xhtml::getScriptElements($dom, '/^taomatching.js/iu');
				$this->assertEquals(1, count($addedElements));
				$added = $addedElements[0];
				
				// Was it really appended?
				$xpath = new DOMXPath($dom);
				$heads = $xpath->query('/html/head');
				foreach ($heads as $head){
					$this->assertTrue($head->lastChild === $added);
					break;
				}
				
				// -- Prepend
				taoItems_helpers_Xhtml::addScriptElement($dom, 'http://www.taotesting.com/scripts/wfapi.min.js', $append = false);
				$addedElements = taoItems_helpers_Xhtml::getScriptElements($dom, '/wfapi\.min/');
				$this->assertEquals(1, count($addedElements));
				$added = $addedElements[0];

				// Was it really prepended?
				$xpath = new DOMXPath($dom);
				$heads = $xpath->query('/html/head');
				foreach ($heads as $head){
					$children = $head->getElementsByTagName('script');
					$this->assertTrue($children->item(0) === $added);
				}
			}
			else{
				$this->assertTrue(false, "An error occured while loading '${file}'.");
			}
		}
		catch (DOMException $e){
			$this->assertTrue(false, "An error occured while parsing '${file}'.");
		}
	}
}