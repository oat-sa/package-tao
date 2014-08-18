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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA;
 *
 *
 */
namespace oat\ontoBrowser\actions;

class Browse extends \tao_actions_CommonModule {

	/**
	 * Return the currently viewing resource
	 * 
	 * @return \core_kernel_classes_Resource
	 */
	private function getCurrentResource() {
		if ($this->hasRequestParameter('uri')) {
			$uri = $this->getRequestParameter('uri');
			if (preg_match('/^i[0-9]+$/', $uri)) {
				$uri = LOCAL_NAMESPACE.'#'.$uri;
			} elseif (substr($uri, 0, 7) == 'http_2_') {
				$uri = \tao_helpers_Uri::decode($uri);
			}
		} else {
			$uri = TAO_OBJECT_CLASS;
		}
		return new \core_kernel_classes_Resource($uri);
	}
	
	public function index() {
		$res = $this->getCurrentResource();
		
		$this->setData('res', $res);
		$this->setData('types', $res->getTypes());
		    //restricted on the currently selected language
		    //$this->setData('triples', $res->getRdfTriples()->getIterator());

		$this->setData('triples', $this->getRdfTriples($res, 'Subject')->getIterator());

		$this->setData('otriples', $this->getRdfTriples($res, 'Object')->getIterator());
		
		$this->setData('ptriples', $this->getRdfTriples($res, 'Predicate')->getIterator());
		
		if ($res->isClass()) {
			$class = new \core_kernel_classes_Class($res->getUri());
			$this->setData('subclassOf', $class->getParentClasses(false));
			$this->setData('subclasses', $class->getSubClasses());
			$this->setData('instances', $class->getInstances());
		}
		$this->setData('userLg', \core_kernel_classes_Session::singleton()->getDataLanguage());
		
		$this->setView('browse.tpl');
	}
	
    private function getRdfTriples( \core_kernel_classes_Resource $resource, $usingRestrictionOn = "Object")
    {
        $returnValue = null;

    	$dbWrapper = \core_kernel_classes_DbWrapper::singleton();
	
	     $namespaces = \common_ext_NamespaceManager::singleton()->getAllNamespaces();
	     $namespace = $namespaces[substr($resource->getUri(), 0, strpos($resource->getUri(), '#') + 1)];
	
	     $query = 'SELECT * FROM "statements" WHERE "'.$usingRestrictionOn.'" = ? order by modelid ';
	     
	     $result = $dbWrapper->query($query, array(
	    	 $resource->getUri()
	     ));
	
	     $returnValue = new \core_kernel_classes_ContainerCollection(new \common_Object(__METHOD__));
	     while($statement = $result->fetch()){
	     	$triple = new \core_kernel_classes_Triple();
	     	$triple->modelid = $statement["modelid"];
	     	$triple->subject = $statement["subject"];
	     	$triple->predicate = $statement["predicate"];
	     	$triple->object = $statement["object"];
	     	$triple->id = $statement["id"];
	     	$triple->lg = $statement["l_language"];
// 	     	$triple->readPrivileges = $statement["stread"];
// 	     	$triple->editPrivileges = $statement["stedit"];
// 	     	$triple->deletePrivileges = $statement["stdelete"];
	     	$returnValue->add($triple);
	     }
        
        return $returnValue;
    }

}