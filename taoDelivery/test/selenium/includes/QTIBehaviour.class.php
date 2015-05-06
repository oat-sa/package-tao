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
require_once(dirname(__FILE__) . '/ItemBehaviour.class.php');

class QTIBehaviour extends ItemBehaviour {
	
	private $interactions = array();
	
	public function __construct($selenium) {
		parent::__construct($selenium);
		$this->discoverInteractions();
	}
	
	public function getInteractions() {
		return $this->interactions;
	}
	
	public function setInteractions(array $interactions) {
		$this->interactions = $interactions;
	}
	
	public function run() {
		$selenium = $this->getSelenium();
		
		foreach ($this->getInteractions() as $i) {
			$i->run();
		}
	}
	
	private function discoverInteractions() {
		$selenium = $this->getSelenium();
		
		if (($count = $selenium->getXpathCount(QTIChoiceBehaviour::getXPath())) > 0) {
			for ($i = 0; $i < $count; $i++) {
				$this->addInteraction(new QTIChoiceBehaviour($selenium, $i));
			}
		}
		
		if (($count = $selenium->getXpathCount(QTIMatchBehaviour::getXPath())) > 0) {
			for ($i = 0; $i < $count; $i++) {
				$this->addInteraction(new QTIMatchBehaviour($selenium, $i));
			}
		}
	}
	
	private function addInteraction(QTIInteractionBehaviour $behaviour) {
		$interactions = $this->getInteractions();
		$interactions[] = $behaviour;
		$this->setInteractions($interactions);
	}
}
?>