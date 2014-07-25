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
 * A class for event dispatchers. It should be extended by any dispatcher class.
 * The role of an event dispatcher is:
 * 1) to maintain a list of listeners for specific events 
 * (beware: the list managed in AbstractEventDispatcher does not differenciate the kinds of listeners / events)
 * 2) to fire events to the listening objects registered in the lsitener list.
 */
abstract class AbstractEventDispatcher {
 
 	/**
 	 * The list of listeners managed by this dispatcher (implemented as a ArrayObject).
 	 * Accessible via the getEventListenerList() function by event dispatcher classes.
 	 */
 	private $eventListenerList = null;
 						
	/**
	 * Adds a listener to the list managed by this dispatcher.
	 * Beware: do not forget to remove it from the list (using the removeEventListener()) when it is destroyed!
	 * @param EventListener $listener The object to add as a listener: 
	 * An instance of a class implementing EventListener.
	 */
	public function addEventListener(EventListener $listener) {
		$logger = new Logger('eventMechanisms');

		
		//First check that the object is not already registered
		$it = $this->getEventListenerList()->getIterator();
    	$it->rewind();
    	$found = false;
    	foreach($it as $key=>$value) {
        	if($it->offSetGet($key) === $listener) {
        		// this should not happen
        		$logger->error("Listener $listener already registered...", __FILE__, __LINE__);
        		$found = true;
        		break;
        	}
        }
        // Then register the object if needed
        if (!$found) {
        	$this->getEventListenerList()->append($listener);
        }
	}
	
	/**
	 * @return The list of listeners managed by this dipatcher.
	 */
	protected function getEventListenerList() {
		if ($this->eventListenerList == null) {
			$this->eventListenerList = array();
		}
		return new ArrayObject($this->eventListenerList);
	}
	
	/**
	 * Removes a listener from the list managed by this dispatcher.
	 * @param EventListener $listener The listener object to remove from the list:
	 * An instance of a class implementing EventListener.
	 */
	public function removeEventListener(EventListener $listener) {
// FIXME : generates a Notice and not really necessary...
//		$it = $this->getEventListenerList()->getIterator();
//    	$it->rewind();
//    	
//    	$listenerName4Log = $listener;
//    	if (method_exists($listener, 'getName')) {
//    		$listenerName4Log = $listener->getName();	
//    	}
//    	foreach($it as $key=>$value) {
//        	//FIXME: maybe add also a test on object equality (using a equals() function)?
//        	if($it->offSetGet($key) === $listener) {
//        		// FIXME: log the echo...
//        		//echo "Removing $listenerName4Log from list...\n";
//        		$it->offSetUnset($key);
//        	}
//        }
	}
	
	/**
	 * Resets (to null) the listener list managed by this dispatcher.
	 */
	public function resetListenerList() {
		$this->eventListenerList = null;
	}
	 
 }
?>
