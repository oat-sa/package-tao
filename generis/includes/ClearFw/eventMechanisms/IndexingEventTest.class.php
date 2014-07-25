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
 * Copyright (c) 2006-2009 (original work) Public Research Centre Henri Tudor (under the project FP6-IST-PALETTE);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php
# ***** BEGIN LICENSE BLOCK *****
# This file is part of "ClearFw".
# Copyright (c) 2007 CRP Henri Tudor and contributors.
# All rights reserved.
#
# "ClearFw" is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License version 2 as published by
# the Free Software Foundation.
# 
# "ClearFw" is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# 
# You should have received a copy of the GNU General Public License
# along with "ClearFw"; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
# ***** END LICENSE BLOCK *****
/*
 * Created on Apr 10, 2007
 * @author Yannick Naudet
 * @last_modif Apr 15, 2007
 */
require_once dirname(__FILE__).'/../config.php';
require_once dirname(__FILE__).'/../activeGeneris/libActiveGeneris.php';

class IndexingEventTest {

	private $classif1;
    private $classif2;
    private $classif3;
    private $facets;
    
    private $event;
    private $dispatcher;
    
    public function __construct() {
    	$this->testIndexingDone();
    	$this->testDoIndexing();
    }
	
    public function testInit() {
    	$this->facets = array("facet1", "facet2", "facet3");
    	
    	$this->classif1 = new FakeClassifier("Classif_1", $this->facets[0]);
    	$this->classif2 = new FakeClassifier("Classif_2", $this->facets[1]);
    	$this->classif3 = new FakeClassifier("Classif_3", $this->facets[2]);
     	
    	$this->event = null;
    	$this->dispatcher = IndexingEventDispatcher::getInstance();
    }
    
    public function testReset() {
    	$this->classif1->__destruct();
    	$this->classif1 = null;
    	$this->classif2->__destruct();
    	$this->classif2 = null;
    	$this->classif3->__destruct();
    	$this->classif3 = null;
    	$this->facets = null;
     }
    
    public function testIndexingDone() {
    	$this->testInit();	  
    	
    	// manual classification
    	$index = array();
    	$index[$this->facets[0]] = "valeur_f1";
    	$index[$this->facets[1]] = "valeur_f2";
    	$index[$this->facets[2]] = "valeur_f3";
    	// notification
    	$this->event = new IndexingEvent("dataX", $index, $this);
    	$this->dispatcher->fireIndexingEvent($this->event);
    	
    	$this->testReset();
    }
    
    public function testDoIndexing() {
    	$this->testInit();
    	
    	// ask for prediction
    	$index = array();
    	$newEvt = null;	  
    	$this->event = new IndexingEvent("dataY", $index, $this);
    	$newEvt = $this->dispatcher->requestIndexing($this->event);

    	assert($this->event === $newEvt);
    	echo "\nDump of original event ($this->event):\n";
    	$this->event->toString();
    	echo "\nDump of returned event ($newEvt) at the end of indexing request:\n";
    	$newEvt->toString();
    	 		
    	$this->testReset();
    }
}

class FakeClassifier implements IndexingEventListener {
	
	private $dispatcher;
	
	private $name;
	
	private $facet;
	
	public function __construct($name, $facet) {
		$this->dispatcher = IndexingEventDispatcher::getInstance();
		$this->dispatcher->addEventListener($this);
		$this->name = $name;
		$this->facet = $facet;
 	}
 	
 	public function __destruct() {
 		// All objects needs to be properly removed from the listener lists when being destroyed...
 		$this->dispatcher->removeEventListener($this);
 	}
 	
 	public function getName() {
 		return $this->name;
 	}
 	
	/**
 	 * When an indexing has been performed, go into learning phase...
 	 * @see IndexingEventListener#indexingDone(IndexingEvent)
 	 */
 	public function indexingDone(IndexingEvent $evt) {
 		echo "\n$this->name ($this), received the following event:\n";
 		$evt->toString();
 	}
 	
 	/**
 	 * When the classifier is requested to make a prediction... 
 	 */
 	public function doIndexing(IndexingEvent $evt) {
 		echo "\n$this->name ($this), received prediction request...\n";
 		$evt->setFacetValue($this->facet, "new value from $this->name");
 		echo "Event dump:\n";
 		$evt->toString();
 	}
}

new IndexingEventTest();

?>