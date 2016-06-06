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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

/**
 * Utilities on requests
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 
 */
class tao_helpers_Request
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Enables you to know if the request in the current scope is an ajax
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return boolean
     */
    public static function isAjax()
    {
        $returnValue = (bool) false;

        
		if(isset($_SERVER['HTTP_X_REQUESTED_WITH'])){
			if(strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
				$returnValue = true;
			}
		}
        

        return (bool) $returnValue;
    }
    
    /**
     * Returns the current relative call url, without leading slash
     * 
     * @param string $url
     * @throws ResolverException
     * @return string
     */
    public static function getRelativeUrl($url = null) {
        $url = is_null($url) ? '/'.ltrim($_SERVER['REQUEST_URI'], '/') : $url;
        $rootUrlPath	= parse_url(ROOT_URL, PHP_URL_PATH);
        $absPath		= parse_url($url, PHP_URL_PATH);
        if (substr($absPath, 0, strlen($rootUrlPath)) != $rootUrlPath ) {
            throw new ResolverException('Request Uri '.$url.' outside of TAO path '.ROOT_URL);
        }
        return substr($absPath, strlen($rootUrlPath));
    }
    

    /**
     * Perform an HTTP Request on the defined url and return the content
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string url
     * @param  boolean useSession if you want to use the same session in the remotre server
     * @return string
     * @throws Exception
     */
    public static function load($url, $useSession = false)
    {
        $returnValue = (string) '';

        
        
        if(!empty($url)){
	        if($useSession){
	   			session_write_close();
	        }
			
	        $curlHandler = curl_init();
			
	        //if there is an http auth, it's mandatory to connect with curl
			if(USE_HTTP_AUTH){
				curl_setopt($curlHandler, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	            curl_setopt($curlHandler, CURLOPT_USERPWD, USE_HTTP_USER.":".USE_HTTP_PASS);
			}
			curl_setopt($curlHandler, CURLOPT_RETURNTRANSFER, 1);
			
			//to keep the session
	        if($useSession){
				if(!preg_match("/&$/", $url)){
					$url .= '&';
				}
				$url .= 'session_id=' . session_id();
				curl_setopt($curlHandler, CURLOPT_COOKIE, session_name(). '=' . $_COOKIE[session_name()] . '; path=/'); 
	        }
	        
			curl_setopt($curlHandler, CURLOPT_URL, $url);
				
			$returnValue = curl_exec($curlHandler);
			if(curl_errno($curlHandler) > 0){
				throw new Exception("Request error ".curl_errno($curlHandler).": ".  curl_error($curlHandler));
			}
			curl_close($curlHandler);  
        }
        
        

        return (string) $returnValue;
    }

}

?>
