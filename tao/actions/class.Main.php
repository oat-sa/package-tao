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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

use oat\tao\model\menu\MenuService;
use oat\tao\model\menu\Perspective;

/**
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package tao
 
 *
 */
class tao_actions_Main extends tao_actions_CommonModule {

    /**
     * The user service
     * @var tao_models_classes_UserService 
     */
    protected $userService;
    
	/**
	 * Constructor performs initializations actions
	 * @return void
	 */
	public function __construct()
	{
		//initialize service
		$this->service = tao_models_classes_TaoService::singleton();
        $this->userService = \tao_models_classes_UserService::singleton();
		$this->defaultData();
	}

	/**
	 * First page, when arriving on a system
	 * to choose front or back office
	 */
	public function entry() {
	    $entries = array();
	    foreach (MenuService::getEntryPoints() as $entry) {
	        if ($entry->hasAccess()) {
	            $entries[] = $entry;
	        }
	    }
	    if (empty($entries)) {
	        // no access -> error
	        if (common_session_SessionManager::isAnonymous()) {
	           return $this->redirect(_url('login')); 
	        } else {
	            common_session_SessionManager::endSession();
                return $this->returnError(__('You currently have no access to the platform'));
	        }
	    } elseif (count($entries) == 1 && !common_session_SessionManager::isAnonymous()) {
	        // single entrypoint -> redirect
	        $entry = current($entries);
	        return $this->redirect($entry->getUrl());
	    } else {
	        // multiple entries -> choice
	        if (!common_session_SessionManager::isAnonymous()) {
	            $this->setData('user', common_session_SessionManager::getSession()->getUserLabel());
	        }
    	    $this->setData('entries', $entries);
    		$this->setView('entry.tpl');
	    }
	}
	
	/**
	 * Authentication form,
	 * default page, main entry point to the user
	 * @return void
	 */
	public function login()
	{
		//add the login stylesheet
		tao_helpers_Scriptloader::addCssFile(TAOBASE_WWW . 'css/login.css');

		$params = array();
		if ($this->hasRequestParameter('redirect')) {
			$redirectUrl = $_REQUEST['redirect'];
				
			if (substr($redirectUrl, 0,1) == '/' || substr($redirectUrl, 0, strlen(ROOT_URL)) == ROOT_URL) {
				$params['redirect'] = $redirectUrl;
			}
		}
		$myLoginFormContainer = new tao_actions_form_Login($params);
		$myForm = $myLoginFormContainer->getForm();

		if($myForm->isSubmited()){
			if($myForm->isValid()){
				$adapter = new core_kernel_users_AuthAdapter($myForm->getValue('login'), $myForm->getValue('password'));
				if(common_user_auth_Service::singleton()->login($adapter)){
					if ($this->hasRequestParameter('redirect')) {
						$this->redirect($_REQUEST['redirect']);
					} else {
						$this->redirect(_url('entry', 'Main'));
					}
				}
				else{
					$this->setData('errorMessage', __('Invalid login or password. Please try again.'));
				}
			}
		}

        $this->setData('form', $myForm->render());
        $this->setData('title', __("TAO Login"));
        if ($this->hasRequestParameter('msg')) {
            $this->setData('msg', htmlentities($this->getRequestParameter('msg')));
        }
		$this->setView('main/login.tpl');
	}

	/**
	 * Logout, destroy the session and back to the login page
	 * @return
	 */
	public function logout()
	{
		session_destroy();
		$this->redirect(_url('entry', 'Main', 'tao'));
	}

	/**
	 * The main action, load the layout
	 * @return void
	 */
	public function index(){
        
		$this->setData('menu', $this->getMenuEntries());
        $this->setData('toolbar', $this->getToolbarActions());
        
        $user = $this->userService->getCurrentUser();
        $shownExtension = $this->getRequestParameter('ext');
        $shownStructure = $this->getRequestParameter('structure');
		if($this->hasRequestParameter('structure')) {
            
			// structured mode
			// @todo stop using session to manage uri/classUri
			$this->removeSessionAttribute('uri');
			$this->removeSessionAttribute('classUri');
			$this->removeSessionAttribute('showNodeUri');
            
            $this->userService->setLastVisitedExtension(_url('index', 'Main', 'tao', array(
                'structure' => $shownStructure,
                'ext' => $shownExtension
            )), $user);
            
			$sections = $this->getSections($shownExtension, $shownStructure);
			if (count($sections) > 0) {
				$this->setData('sections', $sections);
			} else {
				common_Logger::w('no sections');
			}
		} else {
            
            //check if the user is a noob, otherwise redirect him to his last visited extension.
            $firsttime = $this->userService->isFirstTimeInTao($user);
            if($firsttime == false){
               $lastVisited = $this->userService->getLastVisitedExtension($user);
               if(!is_null($lastVisited)){
                   $this->redirect($lastVisited);
               }
            }
        }
        
		$this->setData('user_lang', core_kernel_classes_Session::singleton()->getDataLanguage());
		$this->setData('userLabel', core_kernel_classes_Session::singleton()->getUserLabel());
		// readded to highlight selected extension in menu
		$this->setData('shownExtension', $shownExtension);
		                
        //creates the URL of the action used to configure the client side
        $clientConfigParameters = array(
            'shownExtension'    => $shownExtension,
            'shownStructure'    => $shownStructure
        );
        $this->setData('client_config_url', $this->getClientConfigUrl($clientConfigParameters));

		$this->setView('layout.tpl', 'tao');
	}
    
    
    /**
     * Get the list of menu structures
     * 
     * @return array with data about the menu structures
     */
    private function getMenuEntries(){
        $entries = array();
		foreach (MenuService::getAllPerspectives() as $i => $structure) {
            if ($structure->isVisible() && $this->hasAccessToStructure($structure)) {
                $entries[$i] = array(
                    'id'			=> $structure->getId(),
                    'name' 			=> $structure->getName(),
                    'extension'		=> $structure->getExtension(),
                    'description'	=> $structure->getDescription(),
                    'url'           => _url('index', null, null, array('structure' => $structure->getId(), 'ext' => $structure->getExtension()))
                );
            }
        }
        return $entries;
    }
    
    /**
     * Check wheter a user can access to the content of a structure
     * @param SimpleXMLElement $structure from the structure.xml
     * @return boolean true if the user is allowed
     */
    private function hasAccessToStructure(Perspective $structure){
        $access = false;
        foreach ($structure->getSections() as $section) {
            list($extension, $controller, $action) = explode('/', trim((string) $section->getUrl(), '/'));
            if (tao_models_classes_accessControl_AclProxy::hasAccess($action, $controller, $extension)) {
                $access = true;
                break;
            }
        }
        return $access;
    }
    
    /**
     * Get the sections of the current extension's structure
     * @param string $shownExtension
     * @param string $shownStructure
     * @return array the sections
     */
    private function getSections($shownExtension, $shownStructure){

        $sections = array();
        $structure = MenuService::getPerspective($shownExtension, $shownStructure);
        foreach ($structure->getSections() as $section) {
            
            list($extension, $controller, $action) = explode('/', trim((string) $section->getUrl(), '/'));

            if (tao_models_classes_accessControl_AclProxy::hasAccess($action, $controller, $extension)) {
                $sections[] = array(
                    'id'    => $section->getId(), 
                    'url'   => $section->getUrl(), 
                    'name'  => $section->getName()
                );
            }
        }
        
        return $sections;
    }
    
    /**
     * Get the actions to put into the toolbar
     * @return array the actions
     */
    private function getToolbarActions(){
        $actions = array();
		foreach (MenuService::getToolbarActions() as $i => $toolbarAction) {
            $access = false;
            $action = $toolbarAction->toArray();
            $extension = $toolbarAction->getExtension();
            if(!is_null($toolbarAction->getStructure())){
                $structure = MenuService::getPerspective($extension, $toolbarAction->getStructure());
                if($this->hasAccessToStructure($structure)){
                    $action['url'] =  _url('index', null, null, array('structure' => $toolbarAction->getStructure(), 'ext' => $extension));
                    $access = true;
                }
            } else {
                $action['js'] =  $extension. '/'. $toolbarAction->getJs();
                $access = tao_models_classes_accessControl_AclProxy::hasAccess(null, null, $extension);
            }
            if($access){
                $actions[$i] = $action;
            }
        }
        return $actions;
    }

    /**
     * Check if the system is ready
     */
    public function isReady(){
		if(tao_helpers_Request::isAjax()){
            // the default ajax response is successfull style rastafarai
            $ajaxResponse = new common_AjaxResponse();
        }
        else{
            throw new common_exception_IsAjaxAction(__CLASS__.'::'.__METHOD__.'()');
        }
    }

	/**
	 * Load the actions for the current section and the current data context
	 * @return void
	 */
	public function getSectionActions()
	{

		$uri = $this->hasRequestParameter('uri');
		$classUri = $this->hasRequestParameter('classUri');
		$extname = $this->hasRequestParameter('ext');
		$struct = $this->getRequestParameter('structure');

		$rootClasses = array(TAO_GROUP_CLASS, TAO_ITEM_CLASS, TAO_RESULT_CLASS, TAO_SUBJECT_CLASS, TAO_TEST_CLASS);

		$this->setData('actions', false);
		$this->setData('shownExtension', $this->getRequestParameter('ext'));

		$section = MenuService::getSection($extname, $struct, $this->getRequestParameter('section'));
		if (!is_null($section)) {
    		$actions = array();
    		foreach ($section->getActions() as $action) {
    		    if ($action->hasAccess()) {
    		        $display = __($action->getName());
    		        if(strlen($display) > 15){
    		            $display = str_replace(' ', "<br>", $display);
    		        }
    		        $actionData = array(
    		            'js'		=> $action->getJs(),
    		            'url' 		=> ROOT_URL . ltrim($action->getUrl(), '/'),
    		            'display'	=> $display,
    		            'rowName'	=> $action->getName(),
    		            'name'		=> _clean($action->getName()),
    		            'uri'		=> ($uri) ? $this->getRequestParameter('uri') : false,
    		            'classUri'	=> ($classUri) ? $this->getRequestParameter('classUri') : false,
    		            'reload'	=> $action->getReload(),
    		            'ext'       => $action->getExtensionId()
    		        );
    		        
    		        $actionData['disabled'] = true;
    		        switch ($action->getContext()) {
    		        	case 'resource':
    		        	    if ($classUri || $uri) {
    		        	        $actionData['disabled'] = false;
    		        	    }
    		        	    break;
    		        	case 'class':
    		        	    if ($classUri && !$uri) {
    		        	        $actionData['disabled'] = false;
    		        	    }
    		        	    break;
    		        	case 'instance':
    		        	    if ($classUri && $uri) {
    		        	        $actionData['disabled'] = false;
    		        	    }
    		        	    break;
    		        	case '*':
    		        	    $actionData['disabled'] = false;
    		        	    break;
    		        	default:
    		        	    $actionData['disabled'] = true;
    		        	    break;
    		        }
    		        
    		        //@todo remove this when permissions engine is setup
    		        if ($actionData['rowName'] == 'delete' && $classUri && !$uri) {
    		            if (in_array($actionData['classUri'], tao_helpers_Uri::encodeArray($rootClasses, tao_helpers_Uri::ENCODE_ARRAY_VALUES))) {
    		                $actionData['disabled'] = true;
    		            }
    		        }
    		        
    		        array_push($actions, $actionData);
    		    }
    		}
    			
    	    if (!empty($actions)) {
    			$this->setData('actions', $actions);
    		}
    
    		$this->setView('main/actions.tpl', 'tao');
		}
	}

	/**
	 * Load the section trees
	 * @return void
	 */
	public function getSectionTrees()
	{
		$extname	= $this->getRequestParameter('ext');
		$struct		= $this->getRequestParameter('structure');
		$sectionId	= $this->getRequestParameter('section');

		$section = MenuService::getSection($extname, $struct, $sectionId);
		if (!is_null($section)) {
    		$treeData = array();
    		foreach ($section->getTrees() as $tree) {
    		    $mapping = array(
    		        'editClassUrl'      => 'editClassAction',
    		        'editInstanceUrl'   => 'editInstanceAction',
    		        'addInstanceUrl'    => 'createInstanceAction',
    		        'moveInstanceUrl'   => 'moveInstanceAction',
    		        'addSubClassUrl'    => 'subClassAction',
    		        'deleteUrl'         => 'deleteAction',
    		        'duplicateUrl'      => 'duplicateAction',
    		        'dataUrl'           => 'dataUrl',
    		        'className'         => 'className',
    		        'name'              => 'name'
    		    );
    		    $treeArray = array();
    		    foreach ($mapping as $from => $to) {
    		        $attrValue = $tree->get($from);
    		        if (!is_null($attrValue)) {
    		            if(preg_match("/^\//", (string) $attrValue)){
    		                $treeArray[$to] = ROOT_URL . substr((string)$attrValue, 1);
    		            }
    		            else{
    		                $treeArray[$to] = (string)$attrValue;
    		            }
    		        }
    		    }
                if($this->hasSessionAttribute("showNodeUri")){
                    $treeArray['selectNode'] = $this->getSessionAttribute("showNodeUri");
                }
                if(isset($treeArray['className'])){
                    $treeArray['instanceClass'] = 'node-'.str_replace(' ', '-', strtolower($treeArray['className']));
                    $treeArray['instanceName'] = mb_strtolower(__($treeArray['className']), TAO_DEFAULT_ENCODING);
                }
                $treeId = tao_helpers_Display::textCleaner((string) $tree->getName(), '_');
                $treeData[$treeId] = $treeArray;
    		}
    		if (!empty($treeData)) {
                $this->setData('trees', $treeData);
            }
    
    		$this->setView('main/trees.tpl', 'tao');
		}
	}
}