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
/**
 * Request class
 * TODO Request class documentation.
 * 
 * @author J�r�me Bogaerts <jerome.bogaerts@tudor.lu> <jerome.bogaerts@gmail.com>
 */
class Request
{	
        protected $parameters = array();
        protected $rawParameters = array();
        protected $method;
	
	public function __construct()
	{
		$this->parameters = array_merge($_GET, $_POST);
                $this->rawParameters = array_merge($_GET, $_POST);
		$this->secureParameters();
		
		$this->method = $this->defineMethod();
	}
	
	public function getParameter($name)
	{
		return (isset($this->parameters[$name])) ? $this->parameters[$name] : null;
	}
	
	public function hasParameter($name)
	{
		return isset($this->parameters[$name]);
	}
	
	public function getParameters(){
		return $this->parameters;
	}
	
	public function hasCookie($name)
	{
		return isset($_COOKIE[$name]);
	}
	
	public function getCookie($name)
	{
		return $_COOKIE[$name];
	}
	
	public function getMethod()
	{
		return $this->method;
	}
	
	public function isGet()
	{
		return $this->getMethod() == HTTP_GET;
	}
	
	public function isPost()
	{
		return $this->getMethod() == HTTP_POST;
 	}
 	
 	public function isPut()
 	{
 		return $this->getMethod() == HTTP_PUT;
 	}
 	
 	public function isDelete()
 	{
 		return $this->getMethod() == HTTP_DELETE;
 	}
 	
 	public function isHead()
 	{
 		return $this->getMethod() == HTTP_HEAD;
 	}
 	
 	public function getUserAgent()
 	{
 		return $_SERVER['USER_AGENT'];
 	}
 	
 	public function getQueryString()
 	{
 		return $_SERVER['QUERY_STRING'];
 	}
 	
 	public function getRequestURI()
 	{
 		return $_SERVER['REQUEST_URI'];
 	}
        
        /**
         * Get the parameters as they was sent. <br>
         * <strong>Use this method only if you know what you're doing, otherwise use {@link Request::getParameters} instead.</strong>
         * @return array the request parameters.
         */
        public function getRawParameters(){
            return $this->rawParameters;
        }
        
        /**
         * Does the request contains the type in the Accept header?
         * @param string $type - ie text/html, application/json, etc.
         * @return true is the type is contained in the header
         */
        public function accept($mime){
            
            //extract the mime-types
            $accepts = array_map(function($value) {
                if (strpos($value, ';')) {
                    //remove the priority ie. q=0.3
                    $value = substr($value, 0, strrpos($value, ';'));
                }
                return trim($value);
            }, explode(',', $_SERVER['HTTP_ACCEPT']));

            foreach ($accepts as $accept) {
                if ($accept == $mime) {
                    return true;
                }
                //check the star type
                if (preg_match("/^\*\//", $accept)) {
                    return true;
                }
                //check the star sub-type
                if (preg_match("/\/\*$/", $accept)) {
                    $acceptType = substr($accept, 0, strpos($accept, '/'));
                    $checkType = substr($mime, 0, strpos($mime, '/'));
                    if ($acceptType == $checkType) {
                        return true;
                    }
                }
            }
            return false;
        }
 	
 	private function defineMethod()
 	{	
 		$methodAsString = $_SERVER['REQUEST_METHOD'];
 		
 		switch ($methodAsString)
 		{
 			case 'GET':
 				$method = HTTP_GET;
 				break;
 			
 			case 'POST':
 				$method = HTTP_POST;
 				break;
 			
 			case 'PUT':
 				$method = HTTP_PUT;
 				break;
 			
 			case 'DELETE':
 				$method = HTTP_DELETE;
 				break;
 			
 			case 'HEAD':
 				$method = HTTP_HEAD;
 				break;
 		}
 		
 		return $method;
 	}
 	
 	protected function secureParameters()
 	{
 		$errorManager = Error::getInstance();
 		
 		foreach ($this->parameters as $key => &$param)
 			$param = $errorManager->secure($param, $key);
 	}
}
?>