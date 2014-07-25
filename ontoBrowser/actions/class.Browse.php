<?php
/**
 * QtiAuthoring Controller provide actions to edit a QTI item
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoItems
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */

class ontoBrowser_actions_Browse extends tao_actions_CommonModule {

	/**
	 * constructor: initialize the service and the default data
	 * @return Delivery
	 */
	public function __construct(){

		parent::__construct();

	}
	
	/**
	 * Return the currently viewing resource
	 * 
	 * @return core_kernel_classes_Resource
	 */
	private function getCurrentResource() {
		if ($this->hasRequestParameter('uri')) {
			$uri = $this->getRequestParameter('uri');
			if (preg_match('/^i[0-9]+$/', $uri)) {
				$uri = LOCAL_NAMESPACE.'#'.$uri;
			} elseif (substr($uri, 0, 7) == 'http_2_') {
				$uri = tao_helpers_Uri::decode($uri);
			}
		} else {
			$uri = TAO_OBJECT_CLASS;
		}
		return new core_kernel_classes_Resource($uri);
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
			$class = new core_kernel_classes_Class($res->getUri());
			$this->setData('subclassOf', $class->getParentClasses(false));
			$this->setData('subclasses', $class->getSubClasses());
			$this->setData('instances', $class->getInstances());
		}
		$this->setData('userLg', core_kernel_classes_Session::singleton()->getDataLanguage());
		
		$this->setView('browse.tpl');
	}
	
    private function getRdfTriples( core_kernel_classes_Resource $resource, $usingRestrictionOn = "Object")
    {
        $returnValue = null;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012C6 begin
        
    	$dbWrapper = core_kernel_classes_DbWrapper::singleton();
	
	     $namespaces = common_ext_NamespaceManager::singleton()->getAllNamespaces();
	     $namespace = $namespaces[substr($resource->getUri(), 0, strpos($resource->getUri(), '#') + 1)];
	
	     $query = 'SELECT * FROM "statements" WHERE "'.$usingRestrictionOn.'" = ? order by modelID ';
	     
	     $result = $dbWrapper->query($query, array(
	    	 $resource->getUri()
	     ));
	
	     $returnValue = new core_kernel_classes_ContainerCollection(new common_Object(__METHOD__));
	     while($statement = $result->fetch()){
	     	$triple = new core_kernel_classes_Triple();
	     	$triple->modelID = $statement["modelID"];
	     	$triple->subject = $statement["subject"];
	     	$triple->predicate = $statement["predicate"];
	     	$triple->object = $statement["object"];
	     	$triple->id = $statement["id"];
	     	$triple->lg = $statement["l_language"];
	     	$triple->readPrivileges = $statement["stread"];
	     	$triple->editPrivileges = $statement["stedit"];
	     	$triple->deletePrivileges = $statement["stdelete"];
	     	$returnValue->add($triple);
	     }
        
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012C6 end

        return $returnValue;
    }

}
?>
