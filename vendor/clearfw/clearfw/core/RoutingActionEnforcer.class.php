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
 * ActionEnforcer class
 * TODO ActionEnforcer class documentation.
 * 
 * @author J�r�me Bogaerts <jerome.bogaerts@tudor.lu> <jerome.bogaerts@gmail.com>
 */
class RoutingActionEnforcer extends GenerisActionEnforcer
{
    private function getRoutes() {
        $routes = array();
        foreach (common_ext_ExtensionsManager::singleton()->getEnabledExtensions() as $ext) {
            foreach ($ext->getManifest()->getRoutes() as $key => $value) {
                $routes[$key] = $value;
            }
        }
        return $routes;
    }
    
    /**
     * 
     * (non-PHPdoc)
     * @see GenerisActionEnforcer::getControllerClass()
     */
	protected function getControllerClass()
	{
	    $resolver = new Resolver();
	    $relUrl = $resolver->getRelativeUrl();

	    $controllerClass = null;
	    foreach ($this->getRoutes() as $path => $ns) {
	        $path = trim($path, '/');
	        if (substr($relUrl, 0, strlen($path)) == $path) {
	            $rest = trim(substr($relUrl, strlen($path)), '/');
	            if (!empty($rest)) {
                    $parts = explode('/', $rest, 2);
                    return $ns.'\\'.$parts[0];
	            } elseif (defined('DEFAULT_MODULE_NAME')) {
                    return $ns.'\\'.DEFAULT_MODULE_NAME;
                }
	        }
	    }
	    // DEFAULT_MODULE_NAME
	    
	    // no explicit route found
	    return parent::getControllerClass();
	}

}
?>