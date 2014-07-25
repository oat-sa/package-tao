<?php
require_once(dirname(__FILE__)."/taoCommunicationException.php");
require_once(dirname(__FILE__)."/../taoSessionRequiredException.php");

/**
 * Tao base library, handles authentification 
 * requires the constant 'TAO_ROOT_URL' to point to the tao service
 * 
 * @author Joel Bout, <joel.bout@tudor.lu> *
 */
class taoConnection {
	
	const AUTH_EXTENSION	= 'tao';
	const AUTH_MODULE		= 'AuthApi';
	const AUTH_ACTION		= 'login';
	
	private $serverUrl;
	
	private $userUri = null;
	
	private $userToken = null;
	
	public static function spawnAnonymous($serverUrl) {
		return new self($serverUrl);
	}
	
	public static function restore($serverUrl, $userUri, $userToken) {
		$connection = self::spawnAnonymous($serverUrl);
		$connection->userUri = $userUri;
		$connection->userToken = $userToken;
		return $connection;
	}
	
	private function __construct($serverUrl) {
		$this->serverUrl = rtrim($serverUrl, '/');
	}
	
	public function authenticate($login, $password) {
		$data = $this->sendRequest(self::AUTH_EXTENSION, self::AUTH_MODULE, self::AUTH_ACTION, array(
			'username'	=> $login,
			'password'	=> $password
		));
		if (is_null($data) || !isset($data['success']) || !$data['success'] || !isset($data['user'])) {
			return false;
		}
		$this->userUri		= $data['user']['id'];
		$this->userToken	= $data['token'];
		return $data['user'];
	}
	
	public function isAuthenticated() {
		return !is_null($this->userUri) && !is_null($this->userToken);
	}
	
	/**
	 * Destroys the local session, however does not invalidate the token
	 */
	public function dropAuthentication() {
		$this->userUri		= null;
		$this->userToken	= null;
	}
	
	public function getUserUri() {
		return $this->userUri;
	}
	
	public function getUserToken() {
		return $this->userToken;
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
	public function request($extension, $module, $action, $parms = array()) {
		if (!$this->isAuthenticated()) {
			throw new taoSessionRequiredException('user not authenticated');
		}
		$parms['user']	= $this->userUri;
		$parms['token']	= $this->userToken;
		
		$data = $this->sendRequest($extension, $module, $action, $parms);
	 	if (is_array($data) && isset($data['token'])) {
	 		$this->userToken = $data['token']; 
		}
		return $data;
	}

	/**
	 * Internal function that handles the data transfer
	 * 
	 * @param string $extension
	 * @param string $module
	 * @param string $action
	 * @param array $parms
	 */
	private function sendRequest($extension, $module, $action, $parms) {
		
		$url = $this->serverUrl.'/'.$extension.'/'.$module.'/'.$action;
		
		$curlHandler = curl_init();
		curl_setopt($curlHandler, CURLOPT_URL, $url);
		curl_setopt($curlHandler, CURLOPT_POST, 1);
		curl_setopt($curlHandler, CURLOPT_POSTFIELDS, $parms);
		curl_setopt($curlHandler, CURLOPT_RETURNTRANSFER, 1);
		$data = curl_exec($curlHandler);
		
		// evaluate
		if(curl_errno($curlHandler) != 0){
			curl_close($curlHandler);
			throw new taoCommunicationException("Curl request failed with Error No. : ".curl_errno($curlHandler));
		}
		
		$httpCode = curl_getinfo($curlHandler, CURLINFO_HTTP_CODE);
		curl_close($curlHandler);
		
		if ($httpCode == 403) {
			throw new taoSessionRequiredException('User not authorised');
		}
		
		//decode
		$result = json_decode($data, true);
		if (is_null($result)) {
			throw new taoCommunicationException("Non JSON reply");
		}
		return $result;
	}

}
		