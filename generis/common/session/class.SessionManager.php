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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */

/**
 * Represents a Session on Generis.
 *
 * @access private
 * @author Joel Bout, <joel@taotesting.com>
 * @package generis
 
 */
abstract class common_session_SessionManager
{

    const PHPSESSION_SESSION_KEY = 'common_session_Session';

    private static $session = null;

    /**
     * Retrurns the current session
     * 
     * @throws common_exception_Error
     * @return common_session_Session
     */
    public static function getSession()
    {
        if (is_null(self::$session)) {
            if (PHPSession::singleton()->hasAttribute(self::PHPSESSION_SESSION_KEY)) {
                $session = PHPSession::singleton()->getAttribute(self::PHPSESSION_SESSION_KEY);
                if (! $session instanceof common_session_Session) {
                    throw new common_exception_Error('Non session stored in php-session');
                }
                self::$session = $session;
            } else {
                self::$session = new common_session_AnonymousSession();
            }
        }
        return self::$session;
    } 
    
    /**
     * Starts a new session and stores it in the session if stateful
     * 
     * @param common_session_Session $session
     * @return boolean
     */
    public static function startSession(common_session_Session $session) {

        self::$session = $session;
        // do not start session in cli mode (testcase script)
        if(PHP_SAPI != 'cli'){
            if ($session instanceof common_session_StatefulSession) {
                
                // start session if not yet started
                if (session_id() === '') {
                    session_name(GENERIS_SESSION_NAME);
                    session_start();
                } else {
                    // prevent session fixation.
                    session_regenerate_id();
                }
                
                PHPSession::singleton()->setAttribute(self::PHPSESSION_SESSION_KEY, $session);
            } else {
                PHPSession::singleton()->removeAttribute(self::PHPSESSION_SESSION_KEY);
            }
        }
        return true;
    }
    
    /**
     * Ends the session by replacing it with an anonymous session
     * 
     * @return boolean
     */
    public static function endSession() {

        // clean session data.
        if (session_id() != ''){
            session_destroy();
        }
        
        return self::startSession(new common_session_AnonymousSession());
    }
    
    /**
     * Is the current session anonymous or associated to a user?
     * 
     * @return boolean
     */
    public static function isAnonymous() {
        return is_null(self::getSession()->getUserUri());
    }    
    
}
