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
    const HTTP_GET = 'GET';
    const HTTP_POST = 'POST';
    const HTTP_PUT ='PUT';
    const HTTP_DELETE = 'DELETE';
    const HTTP_HEAD = 'HEAD';
        
    protected $parameters = array();
    protected $rawParameters = array();
    protected $method;
	
    protected $headers =array();
    protected $files;
	
	public function __construct()
    {
        $this->parameters = array_merge($_GET, $_POST);
        $this->rawParameters = $this->parameters;
        $this->secureParameters();
        
        if (function_exists('apache_request_headers')) {
            // apache
            $this->headers = array();
            foreach (apache_request_headers() as $key => $value) {
                $this->headers[strtolower($key)] = $value;
            }
        } else {
            $this->headers = array();
            foreach ($_SERVER as $name => $value) {
                if (substr($name, 0, 5) == 'HTTP_') {
                    $this->headers[str_replace(' ', '-', strtolower(str_replace('_', ' ', substr($name, 5))))] = $value;
                }
            }
        }
        
        $this->files = $_FILES;
        $this->method = $this->defineMethod();
	}
	
	public function getParameter($name)
	{        
        if (isset($this->headers[strtolower($name)])) {
		
		    $headerValues = explode(',', $this->headers[strtolower($name)]);
		    return (count($headerValues)==1) ? reset($headerValues) : $headerValues;
		}

        //comapre $_POST[$string];
        //The coreFw is encoding with html entities ???
        if (isset($this->files[$name])) {
            return file_get_contents($this->files[$name]["tmp_name"]);

        }
		return (isset($this->parameters[$name])) ? $this->parameters[$name] : null;
	}
	
	public function hasParameter($name)
	{
		return (isset($this->parameters[$name]) || isset($this->headers[strtolower($name)]) || isset($this->files[$name]));
	}

    /**
     * Append parameters, only for internal use. Must not be exposed into the Action/Module
     * @param array $parameters
     */
    public function addParameters($parameters)
    {
        $this->parameters = array_merge($parameters, $this->parameters);
        $this->rawParameters = $this->parameters;
    }

    public function getParameters()
    {
		return $this->parameters;
	}

    public function getHeader($string)
    {
	
	    //could be improved using the x- prefix
	    return isset($this->headers[strtolower($string)]) ? $this->headers[strtolower($string)] : false;
	}
        
    public function getHeaders()
    {
        return $this->headers;
    }

	public function hasHeader($string)
    {
	     return isset($this->headers[strtolower($string)]);
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
		return $this->getMethod() == self::HTTP_GET;
	}
	
	public function isPost()
	{
		return $this->getMethod() == self::HTTP_POST;
 	}
 	
 	public function isPut()
 	{
 		return $this->getMethod() == self::HTTP_PUT;
 	}
 	
 	public function isDelete()
 	{
 		return $this->getMethod() == self::HTTP_DELETE;
 	}
 	
 	public function isHead()
 	{
 		return $this->getMethod() == self::HTTP_HEAD;
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
 				$method = self::HTTP_GET;
 				break;
 			
 			case 'POST':
 				$method = self::HTTP_POST;
 				break;
 			
 			case 'PUT':
 				$method = self::HTTP_PUT;
 				break;
 			
 			case 'DELETE':
 				$method = self::HTTP_DELETE;
 				break;
 			
 			case 'HEAD':
 				$method = self::HTTP_HEAD;
 				break;
 		}
 		
 		return $method;
 	}
 	
 	protected function secureParameters()
 	{
 		$errorManager = CfwError::getInstance();
 		
 		foreach ($this->parameters as $key => &$param)
 			$param = $errorManager->secure($param, $key);
 	}
}
?>
