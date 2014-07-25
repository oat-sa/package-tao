<?php
require_once(dirname(__FILE__)."/taoConnection.php");

/**
 * Tao base library, handles authentification 
 * requires the constant 'TAO_ROOT_URL' to point to the tao service
 * 
 * @author Joel Bout, <joel.bout@tudor.lu> *
 */
class taoLibrary {
	
	/**
	 * extension used by default
	 * @var string
	 */
	const EXTENSION		= 'tao';
	 
	/**
	 * module used by this library for authentication
	 * @var string
	 */
	const MODULE		= 'AuthApi';
	
	/**
	 * Key used by persistent sessions
	 * 
	 * @var string
	 */
	const PHPSESSION_KEY	= 'TAO_LIB_SESSION';
	
	/**
	 * Current connection to the server
	 * @var taoConnection
	 */
	private static $connection = null;
	
	/**
	 * Whenever or not to store the authentification in the php session
	 * @var boolean
	 */
	private static $storeInSession = true;
	
	/**
	 * Gets the current active connection
	 * @return taoConnection
	 */
	private function getConnection() {
		if (is_null(self::$connection)) {
			if (isset($_SESSION[self::PHPSESSION_KEY]) && $_SESSION[self::PHPSESSION_KEY] != false) {
				self::$connection = taoConnection::restore(
					TAO_ROOT_URL,
					$_SESSION[self::PHPSESSION_KEY]['user'],
					$_SESSION[self::PHPSESSION_KEY]['token']
				);
				self::$storeInSession = true;
			} else {
				self::$connection = taoConnection::spawnAnonymous(TAO_ROOT_URL);
			}
		}
		return self::$connection;
	}
	
	/**
	 * test whenever we have a valid session
	 * 
	 * @return boolean
	 */
	public function hasSession() {
		return $this->getConnection()->isAuthenticated();
	}
	
	/**
	 * get the current's user URI
	 * 
	 * @throws taoSessionRequiredException if no session has been started
	 * @return string uri of the connected user
	 */
	public function getSessionUser() {
		return $this->getConnection()->getUserUri();
	}
		
	/**
	 * starts a new session using the credentials provided and
	 * provides the following user informations:
	 * 'id','login','first_name','last_name','email','lang','roles'
	 *  
	 * @param string $login
	 * @param string $password
	 * @return mixed array of user information if successful, else FALSE
	 */
	public function startSession($login, $password) {
		$data = $this->getConnection()->authenticate($login, $password);
		if ($data === false) {
			return $this->handleError('Login failed');
		}
		$_SESSION[self::PHPSESSION_KEY] = array(
			'user' => $this->getConnection()->getUserUri(),
			'token' => $this->getConnection()->getUserToken()
		);
		self::$storeInSession = true;
		//@todo store session
		return $data;
	}
	
	/**
	 * Test if a new session could be started with
	 * the given credentials, without changing the current session
	 * 
	 * @param string $login
	 * @param string $password
	 * @return array informations on the user identified by the login or false
	 */
	public function testSession($login, $password) {
		$tmpConnection = taoConnection::spawnAnonymous(TAO_ROOT_URL);
		return $tmpConnection->authenticate($login, $password);
	}
	
	/**
	 * Destroys the local session, however does not invalidate the token
	 */
	public function closeSession() {
		$this->getConnection()->dropAuthentication();
		if (isset($_SESSION[self::PHPSESSION_KEY])) {
			unset($_SESSION[self::PHPSESSION_KEY]);
		}
	}
	
	/*
	 * Request handling 
	 */
	
	/**
	 * Sends a request to the tao server, requires a session
	 * 
	 * @param string $module
	 * @param string $action
	 * @param array $parms
	 * @param string $extension
	 * @return mixed the decoded result of the request
	 * @throws taoSessionRequiredException if no session has been started
	 */
	protected function request($module, $action, $parms = array(), $extension = self::EXTENSION) {
		try {
			$data = $this->getConnection()->request($extension, $module, $action, $parms);
			$_SESSION[self::PHPSESSION_KEY]['token'] = $this->getConnection()->getUserToken();
			return $data;
		} catch (taoCommunicationException $e) {
			return $this->handleError($e->getMessage());
		}
	}
	
	/**
	 * this function can be overridden for custom error handling
	 * 
	 * @param string $errorMessage
	 */
	protected function handleError($errorMessage) {
		$this->closeSession();
		return false;
	}
	
	/**
	 * Restores a session that was initialised elsewhere.
	 * The initialized session is stateless and can be used in a REST context 
	 * 
	 * @param string $taoUrl link to the tao Server
	 * @param string $userUri
	 * @param string $token
	 * @param boolean $skipVerify
	 */
	public function initRestSession($taoUrl, $userUri, $token, $skipVerify = false) {
		$valid = true;
		self::$connection = taoConnection::restore($taoUrl, $userUri, $token);
		if (!$skipVerify) {
			try {
				$this->request(self::MODULE, 'keepAlive');
				// if no exception the session/token is valid
			} catch (taoSessionRequiredException $e) {
				// not a valid session/token
				$valid = false;
			}
		}
		return $valid;
	}
	
}
		