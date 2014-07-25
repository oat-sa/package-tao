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


/**
 * Singleton dispatcher class for managing indexing listener objects and firing indexing events.
 */
class IndexingEventDispatcher extends AbstractEventDispatcher {

    /** The single instance of IndexingEventDispatcher. */
    private static $instance = null;
 	
 	/** 
 	 * Private constructor. As this is a singleton, the instance is accessible 
 	 * by the getInstance() function.
 	 */
 	private function __construct() {
 	}
 	
 	/**
 	 * @return IndexingEventDispatcher The unique instance of the IndexingEventDispatcher.
 	 */
 	public static function getInstance() {
 		if (!isset(self::$instance)) {
 			self::$instance = new IndexingEventDispatcher();
 		}
 		return self::$instance;
 	}
 	
	/**
	 * @see AbstractEventDispatcher#addEventLsitener(EventListener)
	 * Here, the parameter must be an indexingEvent.
	 */
	public function addEventListener(EventListener $listener) {
		assert (isset($listener) && $listener instanceof IndexingEventListener);
		parent::addEventListener($listener);		
	}
	
	/**
	 * @see AbstractEventDispatcher#removeEventLsitener(EventListener)
	 * Here, the parameter must be an indexingEvent.
	 */
	public function removeEventListener(EventListener $listener) {
		assert (isset($listener) && $listener instanceof IndexingEventListener);
		parent::removeEventListener($listener);				
	}	 	
    
    /**
     * To be used for warning the registered listeners that an indexing has been performed.
     * @param IndexingEvent $evt An indexing event to be dispatched, containing at least the URI 
     * of the concerned data and the indexing vector that has been assigned to it.
     */
    public function fireIndexingEvent(IndexingEvent $evt) {		
   		$it = $this->getEventListenerList()->getIterator();
    	$it->rewind();
    	foreach($it as $key=>$listener) {

        	if (!($listener instanceof IndexingEventListener)) {
        		$logger = new Logger('eventMechanisms');
        		$logger->warn("Dispatcher.fireIndexingEvent: listener $listener is not an IndexingEventLsitener", __FILE__, __LINE__);
    		} else 
    		if (!($listener === $evt->getSource())) {
    			$listener->indexingDone($evt);
    		}
        }
    }
       
    /**
     * To be used when an indexation is requested. The indexing vector contained 
     * in the argument event will be filled sequentially by all the registered listeners 
     * implementing the doIndexing() function.
     * @param IndexingEvent $evt An event containing the URI of the data to be indexed.
     * @return IndexingEvent The input event, which is returned back, with a modified indexing vector.
     */
    public function requestIndexing(IndexingEvent $evt) {
    	$it = $this->getEventListenerList()->getIterator();
    	$it->rewind();

    	foreach($it as $key=>$listener) {

        	if (!($listener instanceof IndexingEventListener)) {
 	      		$logger = new Logger('eventMechanisms');
        		$logger->warn("Dispatcher.requestIndexing: listener $listener is not an IndexingEventLsitener", __FILE__, __LINE__);
   		} else 
    		if (!($listener === $evt->getSource())) {
    			$listener->doIndexing($evt);
    		}
        }
    	return $evt;
    }
}
?>