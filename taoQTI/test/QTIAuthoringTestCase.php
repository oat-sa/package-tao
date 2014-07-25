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

require_once dirname(__FILE__) . '/../../tao/test/TaoTestRunner.php';
require_once dirname(__FILE__) . '/../../tao/lib/htmlpurifier/HTMLPurifier.auto.php';
include_once dirname(__FILE__) . '/../includes/raw_start.php';

/**
 *
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoQTI
 * @subpackage test
 */
class QTIAuthoringTestCase extends UnitTestCase {
	
	/**
	 * tests initialization
	 * load qti service
	 */
	public function setUp(){
		TaoTestRunner::initTest();
	}
	
	public function log($msg){
		common_Logger::d('*********************************', array('QTIdebug'));
		common_Logger::d($msg, array('QTIdebug'));
	}
	
	public function _testEncodedData(){
		
		//html editor to QTI XML :
		
		
		//XML to html editor
		$prompt = '<div align="left"><img alt="Earth" src="img/earth.png"/><br/>
		&#xA0; Earth9</div><div align="left"><br/></div><div align="left"><div align="left">&lt;choiceInteraction shuffle="false"
		maxChoices="1"
		responseIdentifier="RESPONSE"&gt;&lt;prompt&gt;&lt;div
		align="left"&gt;&lt;img alt="Earth"
		src="img/earth.png"/&gt;&lt;br/&gt;</div><div align="left">&#xA0;
		Earth9&lt;/div&gt;&lt;/prompt&gt;&lt;/choiceInteraction&gt;</div></div>

		<mytag>yeah<mytag>';
		$this->log($prompt);
		
		$prompt = tao_helpers_Display::htmlToXml($prompt);//setPrompt(), setData();
		$this->log($prompt);
		
		$prompt = tao_helpers_Display::htmlToXml($prompt);//setPrompt(), setData();
		$this->log($prompt);
		
		$prompt = taoQTI_helpers_qti_ItemAuthoring::filterData($prompt);
		$this->log($prompt);
		
		$prompt = taoQTI_helpers_qti_ItemAuthoring::filterData($prompt);
		$this->log($prompt);
		
		$prompt = tao_helpers_Display::htmlToXml($prompt);//setPrompt(), setData();
		$this->log($prompt);
		
		$prompt = tao_helpers_Display::htmlToXml($prompt);//setPrompt(), setData();
		$this->log($prompt);
		
		$prompt = taoQTI_helpers_qti_ItemAuthoring::filterData($prompt);
		$this->log($prompt);
		
		$prompt = taoQTI_helpers_qti_ItemAuthoring::filterData($prompt);
		$this->log($prompt);
		
		$prompt = _dh($prompt);
		$this->log($prompt);
		
	}
	
	private function logCompare($str1, $str2){
		$this->log($str1);
		$this->log($str2);
	}
	
	public function _testFilterInvalidQTIhtml(){
		
		$purifier = taoQTI_helpers_qti_ItemAuthoring::getQTIhtmlPurifier();
		
		/*
		 * edited image tag
		 * tag name to lower case
		 * text attribute timmed
		 * strip undefined attr
		 * auto close atomic inline tag 
		 */
		$raw_html = '<div><IMG alt=\'Earth\' src="  img/earth.png"  myAttr="drop me!" ></div>';
		$purified_html = $purifier->purify($raw_html);
		$this->assertEqual($purified_html, '<div><img alt="Earth" src="img/earth.png" /></div>');
		
		/*
		 * auto close tags for block elements
		 */
		$raw_html = '<div>a<div>b<div>c<div>d<p>e<p>f';
		$purified_html = $purifier->purify($raw_html);
		$this->assertEqual($purified_html, '<div>a<div>b<div>c<div>d<p>e</p><p>f</p></div></div></div></div>');
		
		/*
		 * strip invalid tag (font)
		 * strip closing-only tags (code)
		 */
		$raw_html = '<p align="LEFT" style="margin-bottom: 0cm; line-height: 0.5cm;"><font color="#444444">Invalid</font></code>';
		$purified_html = $purifier->purify($raw_html);
		$this->assertEqual($purified_html, '<p align="left" style="margin-bottom:0cm;line-height:.5cm;">Invalid</p>');
		$this->logCompare($raw_html, $purified_html);
		
		return;
		
		$raw_html = '<div align="left"><img alt="Earth" src="img/earth.png" /><br/>
			&#xA0; Earth9</div><div align="left"><br/></div><div align="left"><div align="left">&lt;choiceInteraction shuffle="false"
			maxChoices="1"
			responseIdentifier="RESPONSE"&gt;&lt;prompt&gt;&lt;div
			align="left"&gt;&lt;img alt="Earth"
			src="img/earth.png"/&gt;&lt;br/&gt;</div><div align="left">&#xA0;
			Earth9&lt;/div&gt;&lt;/prompt&gt;&lt;/choiceInteraction&gt;</div></div>
			
			<div>
			<div>
			<p>&nbsp;<br></p>
			<div>

			<h1 class="western">A. Big features</h1>
			<h2 class="western">Missing interactions</h2>
			<p align="LEFT" style="margin-bottom: 0cm; line-height: 0.5cm;">
			<font color="#444444"><font face="Helvetica, Arial, sans-serif"><font size="2" style="font-size: 9pt"><b>Class</b></font></font></font><font color="#444444">&nbsp;</font><font color="#444444"><font face="Helvetica, Arial, sans-serif"><font size="2" style="font-size: 9pt">:&nbsp;</font></font></font><code class="western"><font color="#444444"><font size="2" style="font-size: 9pt">graphicGapMatchInteraction
			: </font></font></code><code class="western"><font color="#000000"><font face="Helvetica, Arial, sans-serif"><font size="2" style="font-size: 9pt">runtime
			buggy !! : </font></font></font></code>
			</p>
			<p align="LEFT" style="margin-bottom: 0cm; line-height: 0.5cm;">
			<code class="western"><font color="#444444"><font face="Helvetica, Arial, sans-serif"><font size="2" style="font-size: 9pt"><b>Class</b></font></font></font></code><code class="western"><font color="#444444"><font size="2" style="font-size: 9pt">&nbsp;</font></font></code><code class="western"><font color="#444444"><font face="Helvetica, Arial, sans-serif"><font size="2" style="font-size: 9pt">:&nbsp;</font></font></font></code><code class="western"><font color="#444444"><font size="2" style="font-size: 9pt">positionObjectInteraction
			: </font></font></code><code class="western"><font color="#000000"><font face="Helvetica, Arial, sans-serif"><font size="2" style="font-size: 9pt">if
			we consider the positionObjectStage as the interaction and
			positionObjectInteraction as its "choice" then we can
			manage it quickly by adding some crappy "if
			positionObjectInteraction then …"</font></font></font></code></p>
			<p align="LEFT" style="margin-bottom: 0cm; line-height: 0.5cm;">
			<code class="western"><font color="#444444"><font face="Helvetica, Arial, sans-serif"><font size="2" style="font-size: 9pt"><b>Class</b></font></font></font></code><code class="western"><font color="#444444"><font size="2" style="font-size: 9pt">&nbsp;</font></font></code><code class="western"><font color="#444444"><font face="Helvetica, Arial, sans-serif"><font size="2" style="font-size: 9pt">:&nbsp;</font></font></font></code><code class="western"><font color="#444444"><font size="2" style="font-size: 9pt">mediaInteraction
			: new in QTI 2.1</font></font></code></p>
			<p align="LEFT" style="margin-bottom: 0cm; line-height: 0.5cm;">
			<code class="western"><font color="#444444"><font face="Helvetica, Arial, sans-serif"><font size="2" style="font-size: 9pt"><b>Class</b></font></font></font></code><code class="western"><font color="#444444"><font size="2" style="font-size: 9pt">&nbsp;</font></font></code><code class="western"><font color="#444444"><font face="Helvetica, Arial, sans-serif"><font size="2" style="font-size: 9pt">:&nbsp;</font></font></font></code><code class="western"><font color="#444444"><font size="2" style="font-size: 9pt">drawingInteraction
			: ???</font></font></code></p>
			<p align="LEFT" style="margin-bottom: 0cm; line-height: 0.5cm;">
			<code class="western"><font color="#444444"><font face="Helvetica, Arial, sans-serif"><font size="2" style="font-size: 9pt"><b>Class</b></font></font></font></code><code class="western"><font color="#444444"><font size="2" style="font-size: 9pt">&nbsp;</font></font></code><code class="western"><font color="#444444"><font face="Helvetica, Arial, sans-serif"><font size="2" style="font-size: 9pt">:&nbsp;</font></font></font></code><code class="western"><font color="#444444"><font size="2" style="font-size: 9pt">uploadInteraction
			: </font></font></code><code class="western"><font color="#000000"><font face="Helvetica, Arial, sans-serif"><font size="2" style="font-size: 9pt">Not
			working on runtime : may not be so hard to be implemented, provided
			the way it should be stored in the result extension is clearly
			defined</font></font></font></code></p>
			<p align="LEFT" style="margin-bottom: 0cm; line-height: 0.5cm;">
			<code class="western"><font color="#444444"><font face="Helvetica, Arial, sans-serif"><font size="2" style="font-size: 9pt"><b>Class</b></font></font></font></code><code class="western"><font color="#444444"><font size="2" style="font-size: 9pt">&nbsp;</font></font></code><code class="western"><font color="#444444"><font face="Helvetica, Arial, sans-serif"><font size="2" style="font-size: 9pt">:&nbsp;</font></font></font></code><code class="western"><font color="#444444"><font size="2" style="font-size: 9pt">endAttemptInteraction
			: </font></font></code><code class="western"><font color="#000000"><font face="Helvetica, Arial, sans-serif"><font size="2" style="font-size: 9pt">In
			an adaptative item, need to bind this interaction to trigger response
			processing (corresponding to an end of attempt)</font></font></font></code><code class="western"><font color="#000000"><font size="2" style="font-size: 9pt">&nbsp;</font></font></code></p>
			<h2 class="western">Missing attributes</h2>
			<p>Loads...</p>
			<h2 class="western"><br></h2></div><br>

				b<br>ee
			<aside>dsdsd</aside>
			<div bla=\'aaa\' id=\'aaa\' title="pas cool">yeah
			<mytag>yeah<mytag>';
		$this->log($raw_html);
		
		$html = $purifier->purify($raw_html);
		$this->log($html);
	}
	
}
