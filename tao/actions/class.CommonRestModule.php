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
 * 
 *
 * @author patrick implements the restcontroller module type with an HTTP digest login/Basic protocol
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package tao
 
 * @TODO
 * ADD Param multi values -- OK
 * CHECK Param value is a uri -- OK
 * ADD x- prefix for non standard http params
 * ADD Accept-Language header x-lg parameters for the context
 * FIX DIGEST auth method
 * ADD Requirements for properties that have to be skipped (password of users)
 */
abstract class tao_actions_CommonRestModule extends tao_actions_CommonModule {

	const realm = GENERIS_INSTANCE_NAME;
	private $acceptedMimeTypes = array("application/json", "text/xml", "application/xml", "application/rdf+xml");
	private $authMethod = "Basic"; //{auth, Basic}
	private $responseEncoding = "application/json";  //the default content type if nothing specified in the Accept {application/json, text/xml, application/xml}
	private $currentUser = null;

	private $headers = null;
	private $files= null;
	
	public function __construct(){
	    parent::__construct();
	    
	    $authAdapter = new tao_models_classes_HttpBasicAuthAdapter(common_http_Request::currentRequest());
	    try {
	        $user = $authAdapter->authenticate();
    	    $session = new common_session_RestSession($user);
    	    \common_session_SessionManager::startSession($session);
	    } catch (common_user_auth_AuthFailedException $e) {
	        $this->requireLogin();
	    } 
	     
/*
	    $this->headers = tao_helpers_Http::getHeaders();
	    $this->files = tao_helpers_Http::getFiles();
*/
	    if ($this->hasHeader("Accept")){
		try {
		    $this->responseEncoding = (tao_helpers_Http::acceptHeader($this->acceptedMimeTypes, $this->getHeader("Accept")));
		   
		} 
		//may return a 406 not acceptable
		catch (common_exception_ClientException $e) {
		    $this->returnFailure($e);
		}
	    }
	    if ($this->hasHeader("Accept-Language")){
		try {
		    
		} //may return a 406 not acceptable
		catch (common_exception_ClientException $e) {
		    $this->returnFailure($e);
		}
	    }

	     header('Content-Type: '.$this->responseEncoding);
	    //check auth method requested
	    /**/
	}
        /*redistribute actions*/
	public function index(){
	    $uri = null;
	    if ($this->hasRequestParameter("uri")){
		$uri = $this->getRequestParameter("uri");
		if (!(common_Utils::isUri($uri))) {
                $this->returnFailure(new common_exception_InvalidArgumentType());}
	    }
	    switch ($this->getRequestMethod()) {
		case "GET":{$this->get($uri);break;}
		//update
		case "PUT":{$this->put($uri);break;}
		//create
		case "POST":{$this->post();break;}
		case "DELETE":{$this->delete($uri);break;}
		default:{
			throw new common_exception_BadRequest($this->getRequestURI());
		    ;}
	    }
	}
	

/*
	public function hasRequestParameter($string){
	    return parent::hasRequestParameter($string) || isset($this->headers[$string]) || isset($this->files[$string]);
	}
	public function getRequestParameter($string){
	   
	    if (isset($this->headers[$string])) {
		
		$headerValues = explode(',', $this->headers[$string]);
		return (count($headerValues)==1) ? ($this->headers[$string]) : $headerValues;
		}
		//comapre $_POST[$string];
		//The coreFw is encoding with html entities ???
	    if (isset($this->files[$string])) {
		return file_get_contents($this->files[$string]["tmp_name"]);

	    }

	    return parent::getRequestParameter($string);
	}
	protected function getHeader($string){

	    //could be improved using the x- prefix

	     return isset($this->headers[$string]) ? $this->headers[$string] : false;
	}
	protected function hasHeader($string){
	    //could be improved using the x- prefix
	    
	     return isset($this->headers[$string]);
	}
*/

	private function requireLogin(){
	    switch ($this->authMethod){
		case "auth":{
			header('HTTP/1.1 401 Unauthorized');
			header('WWW-Authenticate: Digest realm="'.$this::realm.'",qop="auth",nonce="'.uniqid().'",opaque="'.md5($this::realm).'"');
			break;
		    }
		case "Basic":{
			header('HTTP/1.0 401 Unauthorized');
			header('WWW-Authenticate: Basic realm="'.$this::realm.'"');
			break;
		}
	    }
	    exit(0);
	}
	/**
	 * returnSuccess and returnFailure should be used
	 */
	private function encode($data){
	switch ($this->responseEncoding){
		case "application/rdf+xml":{
		    throw new common_exception_NotImplemented();
		    break;
		}
		case "text/xml":{
		    
		}
		case "application/xml":{
		    return tao_helpers_Xml::from_array($data);
		}
		case "application/json":{
		    return json_encode($data);
		}
		default:{
		    return json_encode($data);
		}
	    }
	}
	/**
	 *  
	 * @param type $errorCode
	 * @param type $errorMsg
	 */
	protected function returnFailure(Exception $exception) {

	    //400 Bad Request
	   if (is_subclass_of($exception, "common_Exception")) {
	       $handler = new tao_helpers_RestExceptionHandler();
	       $handler->handle($exception);
	   }

	    $data = array();
	    $data['success']	=  false;
	    $data['errorCode']	=  $exception->getCode();
	    $data['errorMsg']	=  ($exception instanceof common_exception_UserReadableException) ? $exception->getUserMessage() : $exception->getMessage();
	    $data['version']	= TAO_VERSION;

	    echo $this->encode($data);
	    exit(0);
	}
	protected function returnSuccess($rawData = array()) {
	     $data = array();
	    $data['success']	= true;
	    $data['data']	= $rawData;
	    $data['version']	= TAO_VERSION;
	   
	    echo $this->encode($data);
	    exit(0);
	}
	/**
	 * handle default parameters
	 * should be overriden to declare new and specific expected parameters
	 *
	 *
	 * 
	 */
	protected function getExpectedParameters(){
	    $expectedParameters = array(
		"label" => array(RDFS_LABEL, false),
		"comment" => array(RDFS_COMMENT,false)
	    );
	    return array_merge($this->getCustomParameters(), $expectedParameters);
	}
	/**
	 * Intended to be overridden
	 */
	protected function getParametersAliases(){
	    return array(
		    "label"=> RDFS_LABEL,
		    "comment" => RDFS_COMMENT,
		    "type"=> RDF_TYPE
	    );
	}
	
	/**
	 * Returns all parameters taht are URIs or Aliased with values , throws errors if a mandatory parameter is not found
	 * @return type
	 * @throws common_exception_MissingParameter
	 */
	protected function getParameters(){
		$aliasedParameters = $this->getParametersAliases();
		$effectiveParameters = array();
		foreach ($aliasedParameters as $checkParameterShort =>$checkParameterUri){
		    if ($this->hasRequestParameter($checkParameterShort)){
			   $effectiveParameters[$checkParameterUri] = $this->getRequestParameter($checkParameterShort);
		    }
		    if ($this->hasRequestParameter($checkParameterUri)){
			   $effectiveParameters[$checkParameterUri] = $this->getRequestParameter($checkParameterUri);
		    }
		    if ($this->isRequiredParameter($checkParameterShort) and !(isset($effectiveParameters[$checkParameterUri]))){
		    throw new common_exception_MissingParameter($checkParameterShort, $this->getRequestURI());
		    }
		}
		return array_merge($this->getCustomParameters(), $effectiveParameters);
	}
	/**
	 * Handle extra custom parameters, TODO ppl to be reviewed, need to find a more reliable way and easy for agents.
	 */
	private function getCustomParameters(){
	    $customParameters = array();
	   foreach ($this->getHeaders() as $apacheParamName => $apacheParamValue){
	       if (common_Utils::isUri($apacheParamName)){
		   $customParameters[$apacheParamName] = $apacheParamValue;
	       }
	   }
	   return $customParameters;
	}
	/**
	 * Defines if the parameter is mandatory according to getParametersRequirements (probably overriden) and according to the action type
	 * @param type $parameter the alias name or uri of a parameter
	 */
	private function isRequiredParameter($parameter){

	    $isRequired = false;
	    $method = $this->getRequestMethod();//ppl todo, method retrieval
	    if (isset($requirements[$method])) {
	    $requirements = $this->getParametersRequirements();
	    $aliases = $this->getParametersAliases();
	    

	    //The requirments may have been declared using URIs, loook up for the URI
	    if (isset($aliases[$parameter])) {
		    $isRequired = $isRequired or in_array($aliases[$parameter],$requirements[$method]);
		}
	    
	    $isRequired = $isRequired or in_array($parameter,$requirements[$method]);
	    

	    }
	    return $isRequired;
	}
        /**
         * 
         * @param type $uri
         * @return type
         * @throws common_exception_InvalidArgumentType
         * @throws common_exception_PreConditionFailure
         * @requiresRight uri WRITE
         */

	protected function get($uri = null){
		try {
		    if (!is_null($uri)){
			if (!common_Utils::isUri($uri)){
			    throw new common_exception_InvalidArgumentType();
			}
			if (!($this->service->isInScope($uri))){
			    throw new common_exception_PreConditionFailure("The URI must be a valid resource under the root Class");
			}
			$data = $this->service->get($uri);
		    } else {
			$data = $this->service->getAll();
		    }
		} catch (Exception $e) {
		    return $this->returnFailure($e);
		}
		return $this->returnSuccess($data);
	}
	protected function delete($uri = null){
		try {
		    if (!is_null($uri)){
			if (!common_Utils::isUri($uri)){
			    throw new common_exception_InvalidArgumentType();
			}
			if (!($this->service->isInScope($uri))){
			    throw new common_exception_PreConditionFailure("The URI must be a valid resource under the root Class");
			}
			$data = $this->service->delete($uri);
		    } else {
                //disabled 
                    //$data = $this->service->deleteAll();
		    }
		} catch (Exception $e) {
		    return $this->returnFailure($e);
		}
		return $this->returnSuccess($data);
	}
	protected function post() {
		try {
		    $parameters = $this->getParameters();
		    $data = $this->service->createFromArray($parameters);
		} catch (Exception $e) {
		    return $this->returnFailure($e);
		}
		return $this->returnSuccess($data);
	}
	protected function put($uri){
		try {
			if (!common_Utils::isUri($uri)){
			    throw new common_exception_InvalidArgumentType();
			}
			if (!($this->service->isInScope($uri))){
			    throw new common_exception_PreConditionFailure("The URI must be a valid resource under the root Class");
			}
			$parameters = $this->getParameters(false);
			$data = $this->service->update($uri, $parameters);
		} catch (Exception $e) {
			return $this->returnFailure($e);
		}
		return $this->returnSuccess($data);
	}


	/* commodity as Http-auth (like the rest of the HTTP spec) is meant to be stateless
	 * As per RFC2616 "Existing HTTP clients and user agents typically retain authentication information indefinitely. "
	 * " is a question of getting the browser to forget the credential information, so that the next time the resource is requested, the username and password must be supplied again"
	 * "you can't. Sorry."
	 * Workaround used here for web browsers: provide an action taht sends a 401 and get the the web browsers to log in again
	 * Programmatic agents should send updated credentials directly
	 */
	protected function logout(){
	    $this->requireLogin();
	}
}
?>