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
// This file is derived from dotclear 2 (GPL v2) (c) Olivier Meunier and contributors 
// TODO Does dotclear integration involve extra headers ?

/**
 * Context class
 * TODO Context class documentation.
 * 
 * @author Jérome Bogaerts <jerome.bogaerts@tudor.lu> <jerome.bogaerts@gmail.com>
 * 
 */
class Context
{
	private $request;
	private $response;
	
	private $extensionName;
	private $moduleName;
	private $actionName;
	
	private $viewData;
	private $behaviors;
	
//	/**
//	 * The specifiers are a list of <i>sub context</i> that specialize the global context.
//	 * 
//	 * @var array
//	 */
//	private $specifiers = array();
	
	/**
	 * store the current instance
	 * @var Context
	 */
	private static $instance = null;
	
	/**
	 * Constructor. Please use only getInstance to retrieve the single instance.
	 * 
	 * @see Context#getInstance
	 */
	private function __construct() {
		
		$this->request			= new Request();
		$this->response			= new Response();
		
		$this->viewData			= array();
		$this->behaviors		= array();
		
		if (PHP_SAPI != 'cli') {
			try {
				$resolver = new Resolver();
				$this->extensionName	= $resolver->getExtensionFromURL();
				$this->moduleName 		= Camelizer::firstToUpper($resolver->getModule());
				$this->actionName 		= Camelizer::firstToLower($resolver->getAction());
			} catch (ResolverException $re) {
				$this->extensionName = 'tao';
			}
		}
		
	}
	
	/**
	 * Get the singleton instance of the Context
	 * @return Context
	 */
	public static function getInstance()
	{
		if (!self::$instance) {	
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	public function getRequest()
	{
		return $this->request;
	}
	
	public function getResponse()
	{
		return $this->response;
	}
	
	public function getSession()
	{
		return PHPSession::singleton();
	}
	
	public function setExtensionName($extensionName)
	{
		$this->extensionName = $extensionName;
	}
	
	public function getExtensionName()
	{
		return $this->extensionName;
	}

	public function setModuleName($moduleName)
	{
		$this->moduleName = $moduleName;
	}
	
	public function getModuleName()
	{
		return $this->moduleName;
	}
	
	public function setActionName($actionName)
	{
		$this->actionName = $actionName;
	}
	
	public function getActionName()
	{
		return $this->actionName;
	}
	
	public function setData($key, $data)
	{
		$this->viewData[$key] = $data; 
	}
	
	public function getData($key)
	{
		return isset($this->viewData[$key]) ? $this->viewData[$key] : null;
	}
	
	public function getDataCollection()
	{
		return $this->viewData;
	}
	
	/**
	Adds a new behavior to behaviors stack. <var>$func</var> must be a valid
	and callable callback.
	
	@param	behavior	<b>string</b>		Behavior name
	@param	func		<b>callback</b>	Function to call
	*/
	public function addBehavior($behavior,$func)
	{
		if (is_callable($func)) {
			$this->behaviors[$behavior][] = $func;
		}
	}

	/**
	Tests if a particular behavior exists in behaviors stack.

	@param	behavior	<b>string</b>	Behavior name
	@return	<b>boolean</b>
	*/
	public function hasBehavior($behavior)
	{
		return isset($this->behaviors[$behavior]);
	}

	/**
	Get behaviors stack (or part of).

	@param	behavior	<b>string</b>		Behavior name
	@return	<b>array</b>
	*/
	public function getBehaviors($behavior='')
	{
		if (empty($this->behaviors)) return null;

		if ($behavior == '') {
			return $this->behaviors;
		} elseif (isset($this->behaviors[$behavior])) {
			return $this->behaviors[$behavior];
		}
		
		return array();
	}
	
	/**
	Calls every function in behaviors stack for a given behavior and returns
	concatened result of each function.
	
	Every parameters added after <var>$behavior</var> will be pass to
	behavior calls.
	// FIXME: i am not sure if all results should be concatenated. Perhaps a better option is to return an array of results and let the user aggregate them.
	
	
	@param	behavior	<b>string</b>	Behavior name
	@return	<b>string</b> Behavior concatened result
	*/
	public function callBehavior($behavior)
	{
		if (isset($this->behaviors[$behavior]))
		{
			$args = func_get_args();
			array_shift($args);
			
			$res = '';
			
			foreach ($this->behaviors[$behavior] as $f) {
				$res .= call_user_func_array($f,$args);
			}
			
			return $res;
		}
	}
	//@}		
}
?>