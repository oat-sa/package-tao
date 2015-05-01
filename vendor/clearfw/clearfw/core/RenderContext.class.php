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
 * Renderer class
 * 
 * @author J�r�me Bogaerts <jerome.bogaerts@tudor.lu> <jerome.bogaerts@gmail.com>
 */
class RenderContext
{

	private static $stack = array();
	
	public static function pushContext($variables) {
		$context = new self($variables);
		array_unshift(self::$stack, $context);
	}
	
	public static function popContext() {
		if (empty(self::$stack)) {
			throw new common_exception_Error('Called '.__FUNCTION__.' on an empty stack');
		}
		array_shift(self::$stack);
	}
	
	/**
	 * 
	 * @throws common_exception_Error
	 * @return RenderContext
	 */
	public static function getCurrentContext() {
		if (empty(self::$stack)) {
			throw new common_exception_Error('Called '.__FUNCTION__.' on an empty stack');
		}
		return reset(self::$stack);
	}
	
	/**
	 * @var array associtaiv array of variables that will be replaced in the template
	 */
	private $variables = array();
	
	/**
	 * Creates a new context
	 * 
	 * @param array $variables
	 */
	private function __construct($variables)
	{
		$this->variables	= $variables;
	}

    /**
     * Gets data for the specified key
     * 
     * @param string $key
     * @return mixed associated data
     */
	public function getData($key)
    {
        return isset($this->variables[$key]) ? $this->variables[$key] : null;
    }
	
    /**
     * Returns whenever or not a variable with the specified key is defined
     * 
     * @param string $key
     * @return boolean
     */
	public function hasData($key)
    {
        return isset($this->variables[$key]);
    }
}
?>