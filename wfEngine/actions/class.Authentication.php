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
 * Copyright (c) 2007-2010 (original work) Public Research Centre Henri Tudor & University of Luxembourg) (under the project TAO-QUAL);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php
class wfEngine_actions_Authentication extends wfEngine_actions_WfModule
{

    /**
     * Users Service
     * @var type wfEngine_models_classes_UserService
     */
    protected $userService;

    /**
     * Action constructor
     */
    public function __construct()
    {
    	parent::__construct();
		$this->userService = wfEngine_models_classes_UserService::singleton();
    }

	/**
	 * WfEngine Login controler
	 */
	public function index()
	{

		if($this->hasRequestParameter('errorMessage')){
			$this->setData('errorMessage',$this->getRequestParameter('errorMessage'));
		}

		$processUri = urldecode($this->getRequestParameter('processUri'));
		$processExecution = common_Utils::isUri($processUri)?new core_kernel_classes_Resource($processUri):null;

		$activityUri = urldecode($this->getRequestParameter('activityUri'));
		$activityExecution = common_Utils::isUri($activityUri)?new core_kernel_classes_Resource($activityUri):null;

		//create the login for to the activity execution of a process execution:
		$myLoginFormContainer = new wfEngine_actions_form_Login(array(
			'processUri' => !is_null($processExecution)?$processExecution->getUri():'',
			'activityUri' => !is_null($activityExecution)?$activityExecution->getUri():''
		));
		$myForm = $myLoginFormContainer->getForm();

		if($myForm->isSubmited()){
			if($myForm->isValid()){
				$values = $myForm->getValues();
				if($this->userService->loginUser($values['login'], $values['password'])){
					if(!empty($values['processUri']) && !empty($values['activityUri'])){
						$this->redirect(_url('index', 'ProcessBrowser', 'wfEngine', array(
								'processUri' => urlencode($values['processUri']),
								'activityUri' => urlencode($values['activityUri'])
							)
						));
					}else{
						$this->redirect(_url('index', 'WfHome'));
					}
				}
				else{
					$this->setData('errorMessage', __('Invalid login or password. Please try again.'));
				}
			}
		}

		$this->setData('form', $myForm->render());
		$this->setView('login.tpl');
	}

    /**
     * Login a user to the workflow engine through an ajax request
     */
    public function login()
    {
        $success = false;
        $message = __('Unable to log in the user');
        //log the user
        if($this->hasRequestParameter('login') && $this->hasRequestParameter('password')){
            if ($this->userService->loginUser($this->getRequestParameter('login'), $this->getRequestParameter('password'))){
                $success = true;
                $message = __('User logged in successfully');
            }
        }
        //write the response
        new common_AjaxResponse(array(
            'success'   => $success
            , 'message' => $message
        ));
    }

    /**
     * Get information about the current user
     */
    public function info()
    {
        $data = array();
        $success = false;
        $currentUser = $this->userService->getCurrentUser();
        if(!is_null($currentUser)){

            $success = true;
            //properties to get
            $properties = array(
                'login' => PROPERTY_USER_LOGIN,
                'uilg' => PROPERTY_USER_UILG,
                'deflg' => PROPERTY_USER_DEFLG,
                'mail' => PROPERTY_USER_MAIL,
                'firstname' => PROPERTY_USER_FIRSTNAME,
                'lastname' => PROPERTY_USER_LASTNAME,
            );
            foreach($properties as $label=>$propertyUri){
                $value = $currentUser->getOnePropertyValue(new core_kernel_classes_Property($propertyUri));
                if($value instanceof core_kernel_classes_Resource){
                    $data[$label] = $value->getUri();
                }else if($value instanceof core_kernel_classes_Literal){
                    $data[$label] = (string)$value;
                }
            }
            //add roles
            $data['roles'] = array();
            foreach($currentUser->getAllPropertyValues(new core_kernel_classes_Property(RDFS_TYPE)) as $type){
                $data['roles'][] = $type->getUri();
            }
        }
        //write the response
        new common_AjaxResponse(array(
            'success'   => $success
            , 'data'    => $data
        ));
    }

    /**
     * Logout a user
     */
	public function logout()
	{
		// Humpa Lumpa motion twin session destroy .
		if (isset($_COOKIE[session_name()])) {
    		setcookie(session_name(), session_id(), 1, '/');
		}

		// Finally, destroy the session.
		session_unset();

		if (!tao_helpers_Request::isAjax()) {
			$this->redirect(_url('index', 'Authentication'));
		} else {
			echo json_encode(array('success' => true));
		}
	}
}
?>
