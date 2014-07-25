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
 * Created on Apr 12, 2007
 * @author Yannick Naudet
 * @last_modif Apr 15, 2007
 */
 

/**
 * A class for indexing events.
 */ 
class IndexingEvent extends Event {
 	
 	/** URI of the data that has been indexed */
 	private $dataURI = null;
 	
 	/** Array containing (facet, values) pairs */
 	private $indexVector = null;
 	
 	/**
 	 * Creates an IndexingEvent object, specifying the URI of the data being indexed 
 	 * or that will be indexed, and the indexing facet vector.
 	 * @param string $dataURI The URI of the data that has been indexed, or to index.
 	 * @param array $indexVector The indexing facet ector associated to the concerned data
 	 */
 	public function __construct($dataURI, $indexVector = array(), $source = null) {
		parent::__construct($source);
		$this->dataURI = $dataURI;
		$this->indexVector = $indexVector;
 	}
 	
 	/**
 	 * @return String The URI of the data that has been indexed.
 	 */
 	public function getDataURI() {
 		return $this->dataURI;
 	}
 	
 	/**
 	 * @return Array The indexing vector associated to the concerned data.
 	 */
 	public function getIndexVector() {
 		return $this->indexVector;
 	}
 	
 	/**
 	 * Sets the value for a given facet in the indexVector.
 	 * @param string $facetName The name of a facet.
 	 * @param object $value The value of the facet to put in the index vector.
 	 */
 	public function setFacetValue($facetName, $value) {
 		if (!isset($this->indexVector)) {
 			$this->indexVector = array();	
 		} else {
 			$logger = new Logger('eventMechanisms');
 			$logger->warning('replacing old facet value', __FILE__, __LINE__); 			
 		}
 		
 		$this->indexVector[$facetName] = $value;
 	}
 	
 	/**
 	 * Outputs a dump of the object content.
 	 */
 	public function toString() {
 		printf("IndexingEvent (source: %s):\n", $this->getSource());
 		printf("* data URI: %s\n", $this->getDataURI());
 		printf("* index vector: \n");
 		foreach($this->getIndexVector() as $facet => $value) {
 			printf("[facet, value] = [%s, %s]\n", $facet, $value);
 		}
 		printf("\n"); 		
 	}
 	
}
?>