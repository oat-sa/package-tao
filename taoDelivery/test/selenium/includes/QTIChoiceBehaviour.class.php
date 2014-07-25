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
?>
<?php
require_once(dirname(__FILE__) . '/QTIInteractionBehaviour.class.php');

class QTIChoiceBehaviour extends QTIInteractionBehaviour {
	
	public function run() {
		$selenium = $this->getSelenium();
		
		// Select the correct behaviour regarding the item nature.
		$xPath = $this->getXPath();
		$interactionIndex = $this->getIndex() + 1;
    	$optionsCount = $selenium->getXpathCount("${xPath}[${interactionIndex}]/ul[@class='qti_choice_list']/li");
    	$indexToClick = rand(1, $optionsCount);
    	$selenium->click("//ul[@class='qti_choice_list']/li[${indexToClick}]");
    	
    	// Valdiate
    	$this->validateItem();
	}
	
	public function getXPath() {
		return "//div[@class='qti_widget qti_choice_interaction  qti_simple_interaction']";
	}
}
?>