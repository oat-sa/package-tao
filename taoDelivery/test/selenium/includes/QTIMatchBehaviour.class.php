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

class QTIMatchBehaviour extends QTIInteractionBehaviour {
	
	public function run() {
		// We have to check one answer for each row.
		// What is the rowCount? colCount?
		
		$selenium = $this->getSelenium();
		$xPath = $this->getXPath();
		$interactionIndex = $this->getIndex() + 1;
		$xPathRows = "/ul[@class='choice_list choice_list_rows']/li";
		$xPathCols = "/ul[@class='choice_list choice_list_cols']/li";
		
		$rowCount = $selenium->getXpathCount("${xPath}[${interactionIndex}]${xPathRows}");
    	$colCount = $selenium->getXpathCount("${xPath}[${interactionIndex}]${xPathCols}");
    	
    	// Now we need to click at index (rowX, colRandom)
    	for ($i = 0; $i < $rowCount; $i++) {
    		$randomCol = rand(0, $colCount - 1);
    		$selenium->click("//div[@id='match_node_${i}_${randomCol}']");
    	}
    	
    	// Validate
    	$this->validateItem();
	}
	
	public function getXPath() {
		return "//div[@class='qti_widget qti_match_interaction ']";
	}
}
?>