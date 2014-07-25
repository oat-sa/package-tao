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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */


/**
 * Represents a old generis session.
 * 
 * Please use the frmework session common\session\SessionManager instead
 *
 * @access private
 * @author patrick@taotesting.com
 * @package generis
 * @deprecated
 */
class core_kernel_classes_Session
{

    /**
     * The single instance of core_kernel_classes_Session
     *
     * @access private
     * @var Session
     */
    private static $instance = null;

    /**
     * returns the current user session
     * 
     * @return common_session_Session
     */
    private static function getCurrentUserSession() {
        return common_session_SessionManager::getSession();
    }
    
    /**
     * Obtain a single core_kernel_classes_Session instance.
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @return core_kernel_classes_Session
     */
    public static function singleton()
    {
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
    }

    /**
     * This function is used to reset the session values.
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @return void
     */
    public function reset()
    {
		common_Logger::d('resetting session');
		common_session_SessionManager::endSession();

		$this->update();
    }

    /**
     * Creates a new instance of core_kernel_classes_Session
     *
     * @access private
     * @author Joel Bout, <joel@taotesting.com>
     */
    private function __construct()
    {
    }

    /**
     * Get the local namespace (model) in use by the authenticated user.
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return string
     * @deprecated
     */
    public function getNameSpace()
    {
        return common_ext_NamespaceManager::singleton()->getLocalNamespace()->getUri();
    }

    /**
     *Returns the login of the currently authenticated user.
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return string
     */
    public function getUserLabel()
    {
        return $this->getCurrentUserSession()->getUserLabel();
    }

    /**
     * Get the URI identifying the currently authenticated user in persistent memory.
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @return string
     */
    public function getUserUri()
    {
        return $this->getCurrentUserSession()->getUserUri();
    }
    
    /**
     * Sets the current session
     * @param common_session_Session $session
     * @return boolean
     */
    public function setSession(common_session_Session $session)
    {
        return common_session_SessionManager::startSession($session);
    }    
    
    /**
     * Refreshes the user session to empty the cashed
     * roles and data 
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     */
    public function refresh()
    {
        $session = $this->getCurrentUserSession();
        if ($session instanceof common_session_StatefulSession) {
            $session->refresh();
        }
    }
    
    /**
     * Updates the session by reloading references to models.
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @deprecated
     */
    public function update()
    {
        common_Logger::w('The function '.__FUNCTION__.' is deprecated');
        core_kernel_persistence_smoothsql_SmoothModel::forceReloadModelIds();
    }

    /**
     * Obtain the language to use for data access in persistent memory.
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @return string
     */
    public function getDataLanguage()
    {
        return (string) self::getCurrentUserSession()->getDataLanguage();
    }

    /**
     * returns the language code associated with user interactions
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @return string
     */
    public function getInterfaceLanguage()
    {
        return self::getCurrentUserSession()->getInterfaceLanguage();
    }

    /**
     * returns the roles of the current user
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @return array An array of core_kernel_classes_Resource
     */
    public function getUserRoles()
    {
        return self::getCurrentUserSession()->getUserRoles();
    }

}

?>