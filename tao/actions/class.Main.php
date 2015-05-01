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
use oat\oatbox\user\LoginService;
use oat\tao\helpers\TaoCe;
use oat\tao\model\menu\Action;
use oat\tao\model\accessControl\func\AclProxy as FuncProxy;
use oat\tao\model\accessControl\ActionResolver;
use \common_session_SessionManager;
use \common_Logger;

/**
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package tao
 
 *
 */
class tao_actions_Main extends tao_actions_CommonModule
{

    /**
     * The user service
     *
     * @var tao_models_classes_UserService 
     */
    protected $userService;
    
	/**
	 * Constructor performs initializations actions
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
    public function entry()
    {
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
            $naviElements = $this->getNavigationElementsByGroup('settings');
            foreach($naviElements as $key => $naviElement) {
                if($naviElement['perspective']->getId() !== 'user_settings') {
                    unset($naviElements[$key]);
                    continue;
                }
            }


            $this->setData('userLabel', \common_session_SessionManager::getSession()->getUserLabel());

            $this->setData('settings-menu', $naviElements);

            $this->setData('content-template', array('blocks/entry-points.tpl', 'tao'));

            $this->setView('layout.tpl', 'tao');
	    }
	}
	
	/**
	 * Authentication form,
	 * default page, main entry point to the user
     *
	 * @return void
	 */
	public function login()
	{

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
			    $success = LoginService::login($myForm->getValue('login'), $myForm->getValue('password'));
				if($success){
				    \common_Logger::i("Successful login of user '" . $myForm->getValue('login') . "'.");
				    
					if ($this->hasRequestParameter('redirect') && tao_models_classes_accessControl_AclProxy::hasAccessUrl($_REQUEST['redirect'])) {
						$this->redirect($_REQUEST['redirect']);
					} else {
						$this->redirect(_url('entry', 'Main'));
					}
                } else {
                    \common_Logger::i("Unsuccessful login of user '" . $myForm->getValue('login') . "'.");
					$this->setData('errorMessage', __('Invalid login or password. Please try again.'));
				}
			}
		}

        $this->setData('form', $myForm->render());
        $this->setData('title', __("TAO Login"));
        if ($this->hasRequestParameter('msg')) {
            $this->setData('msg', htmlentities($this->getRequestParameter('msg')));
        }
        $this->setData('content-template', array('blocks/login.tpl', 'tao'));

        $this->setView('layout.tpl', 'tao');
	}

	/**
	 * Logout, destroy the session and back to the login page
	 */
	public function logout()
	{
		common_session_SessionManager::endSession();
		$this->redirect(_url('entry', 'Main', 'tao'));
	}

	/**
	 * The main action, load the layout
     *
	 * @return void
	 */
    public function index()
    {
        
        $user      = $this->userService->getCurrentUser();
        $extension = $this->getRequestParameter('ext');
        $structure = $this->getRequestParameter('structure');
        
		if($this->hasRequestParameter('structure')) {
            
			// structured mode
			// @todo stop using session to manage uri/classUri
			$this->removeSessionAttribute('uri');
			$this->removeSessionAttribute('classUri');
			$this->removeSessionAttribute('showNodeUri');
            
            TaoCe::setLastVisitedUrl(
                _url(
                    'index',
                    'Main',
                    'tao',
                    array(
                        'structure' => $structure,
                        'ext'       => $extension
                    )
                )
            );
            
            $sections = $this->getSections($extension, $structure);
			if (count($sections) > 0) {
				$this->setData('sections', $sections);
			} else {
				common_Logger::w('no sections');
			}
		} else {
            
            //check if the user is a noob, otherwise redirect him to his last visited extension.
            $firstTime = TaoCe::isFirstTimeInTao();
            if ($firstTime == false) {
               $lastVisited = TaoCe::getLastVisitedUrl();
               if(!is_null($lastVisited)){
                   $this->redirect($lastVisited);
               }
            }
        }


        $perspectiveTypes = array(Perspective::GROUP_DEFAULT, 'settings');
        foreach ($perspectiveTypes as $perspectiveType) {
            $this->setData($perspectiveType . '-menu', $this->getNavigationElementsByGroup($perspectiveType));
        }
        
        $this->setData('user_lang', \common_session_SessionManager::getSession()->getDataLanguage());
        $this->setData('userLabel', \common_session_SessionManager::getSession()->getUserLabel());
        // re-added to highlight selected extension in menu
        $this->setData('shownExtension', $extension);
        $this->setData('shownStructure', $structure);
		                
        //creates the URL of the action used to configure the client side
        $clientConfigParams = array(
            'shownExtension' => $extension,
            'shownStructure' => $structure
        );
        $this->setData('client_config_url', $this->getClientConfigUrl($clientConfigParams));
        $this->setData('content-template', array('blocks/sections.tpl', 'tao'));

		$this->setView('layout.tpl', 'tao');
	}
    
    
    /**
     * Get perspective data depending on the group set in structure.xml
     * 
     * @param $groupId
     * @return array
     */
    private function getNavigationElementsByGroup($groupId)
    {
        $entries = array();
        foreach (MenuService::getPerspectivesByGroup($groupId) as $i => $perspective) {
            $binding = $perspective->getBinding();
            $children = $this->getMenuElementChildren($perspective);
            
            if (!empty($binding) || !empty($children)) {
                $entry = array(
                    'perspective' => $perspective,
                    'children'    => $children
                );
                if (!is_null($binding)) {
                    $entry['binding'] = $perspective->getExtension() . '/' . $binding;
                }
                $entries[$i] = $entry;
            }
        }
        return $entries;
    }
    
    /**
     * Get nested menu elements depending on user rights.
     *
     * @param Perspective $menuElement from the structure.xml
     * @return array menu elements list
     */
    private function getMenuElementChildren(Perspective $menuElement)
    {
        $children = array();
        foreach ($menuElement->getChildren() as $section) {
            if (
                tao_models_classes_accessControl_AclProxy::hasAccess(
                    $section->getAction(), $section->getController(), $section->getExtensionId()
                )
            ) {
                $children[] = $section;
            }
        }
        return $children;
    }

    /**
     * Get the sections of the current extension's structure
     *
     * @param string $shownExtension
     * @param string $shownStructure
     * @return array the sections
     */
    private function getSections($shownExtension, $shownStructure)
    {

        $sections = array();
        $user = common_Session_SessionManager::getSession()->getUser();
        $structure = MenuService::getPerspective($shownExtension, $shownStructure);
        if (!is_null($structure)) {
            foreach ($structure->getChildren() as $section) {
                
                if (
                    tao_models_classes_accessControl_AclProxy::hasAccess(
                        $section->getAction(),
                        $section->getController(),
                        $section->getExtensionId()
                    )
                ) {
    
                    foreach($section->getActions() as $action){
                        $resolver = ActionResolver::getByControllerName($action->getController(), $action->getExtensionId());  
                        if(!FuncProxy::accessPossible($user, $resolver->getController(), $action->getAction())){
                            $section->removeAction($action); 
                        }
                        
                    }
    
    				$sections[] = $section;
                }
            }
        }
        
        return $sections;
    }
    

    /**
     * Check if the system is ready
     */
    public function isReady()
    {
		if(tao_helpers_Request::isAjax()){
            // the default ajax response is successful style rastafarai
            $ajaxResponse = new common_AjaxResponse();
        } else {
            throw new common_exception_IsAjaxAction(__CLASS__.'::'.__METHOD__.'()');
        }
    }
}
