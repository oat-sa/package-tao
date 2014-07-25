<?php
/**  
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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */

/**
 * A controller to bypass some restrictions on cookies
 * 
 * @author Joel Bout <joel@taotesting.com>
 * @package taoLti
 
 */
class taoLti_actions_CookieUtils extends tao_actions_CommonModule
{
    /**
     * Verifies whenever or not the cookie was set correctly
     * Redirects the user to his destination if it was
     * or prompts the user to restore the session if it wasn't
     */
	public function verifyCookie() {
	    $url = $this->getRequestParameter('redirect');
	    $session = $this->getRequestParameter('session');
	    if (session_id() == $session) {
	        $this->redirect($url);
	    } else {
	        $this->setData('session', $session);
	        $this->setData('redirect', $url);
	        $this->setView('cookieError.tpl');
	    }
	}
	
	/**
	 * Closses the current session, restores the session provided
	 * in the parameter session, regenerates a new sessionid and
	 * redirects the user to the original address
	 */
	public function restoreSession() {
	    $sessId = $this->getRequestParameter('session');
	    $url = $this->getRequestParameter('redirect');
	    if (session_id() != $sessId) {  
            common_Logger::i('Changing session to '.$sessId);
            session_unset();
            session_destroy();
            
            session_id($sessId);
            session_start();
            if (session_id() != $sessId) {
               $this->returnError(__('Unable to restore Session'));
            }
            session_regenerate_id(true);
            common_Logger::d('regenerated session to id \''.session_id().'\'');
	    } else {
	        common_Logger::w('Restore session called with correct session id \''.session_id().'\'');
	    }
	    $this->redirect($url);
	}
}