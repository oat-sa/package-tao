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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA;
 *
 *
 */
namespace oat\tao\model\routing;

/**
 * A simple router, that maps a relative Url to
 * namespaced Controller class
 * 
 * @author Joel Bout, <joel@taotesting.com>
 */
class NamespaceRoute extends Route
{
    public function resolve($relativeUrl) {
        $slash = strpos($relativeUrl, '/');
        if ($slash !== false && substr($relativeUrl, 0, $slash) == $this->getId()) {
	        $namespace = $this->getConfig();
	        $rest = substr($relativeUrl, $slash+1);
	        if (!empty($rest)) {
                $parts = explode('/', $rest, 3);
                $controller = rtrim($namespace, '\\').'\\'.$parts[0];
                //todo
                $method = isset($parts[1]) ? $parts[1] : DEFAULT_ACTION_NAME;
                return $controller.'@'.$method;
            } elseif (defined('DEFAULT_MODULE_NAME') && defined('DEFAULT_ACTION_NAME')) {
                $controller = rtrim($namespace, '\\').'\\'.DEFAULT_MODULE_NAME;
                $method = DEFAULT_ACTION_NAME;
                return $controller.'@'.$method;
            }
        }
        return null;
    }
}
