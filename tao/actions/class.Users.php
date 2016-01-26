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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

/**
 * This controller provide the actions to manage the application users (list/add/edit/delete)
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package tao
 *
 */
class tao_actions_Users extends tao_actions_CommonModule
{
    /**
     * @var tao_models_classes_UserService
     */
    protected $userService = null;

    /**
     * Role User Management should not take into account
     */

    private $filteredRoles = array();

    /**
     * Constructor performs initializations actions
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->userService = tao_models_classes_UserService::singleton();
        $this->defaultData();

        $extManager = common_ext_ExtensionsManager::singleton();
    }

    /**
     * Show the list of users
     * @return void
     */
    public function index()
    {
        $this->setView('user/list.tpl');
    }

    /**
     * Provide the user list data via json
     * @return string|json
     */
    public function data()
    {
        $page = $this->getRequestParameter('page');
        $limit = $this->getRequestParameter('rows');
        $sortBy = $this->getRequestParameter('sortby');
        $sortOrder = $this->getRequestParameter('sortorder');
        $filterQuery = $this->getRequestParameter('filterquery');
        $filterColumns = $this->getRequestParameter('filtercolumns');
        $start = $limit * $page - $limit;

        $fieldsMap = [
            'login' => PROPERTY_USER_LOGIN,
            'firstname' => PROPERTY_USER_FIRSTNAME,
            'lastname' => PROPERTY_USER_LASTNAME,
            'email' => PROPERTY_USER_MAIL,
            'dataLg' => PROPERTY_USER_DEFLG,
            'guiLg' => PROPERTY_USER_UILG
        ];

        // sorting
        $order = array_key_exists($sortBy, $fieldsMap) ? $fieldsMap[$sortBy] : $fieldsMap['login'];

        // filtering
        $filters = [];
        if ($filterQuery) {
            if (!$filterColumns) {
                // if filter columns not set, search by all columns
                $filterColumns = array_keys($fieldsMap);
            }
            $filters = array_flip(array_intersect_key($fieldsMap, array_flip($filterColumns)));
            array_walk($filters, function (&$row, $key) use($filterQuery) {
                $row = $filterQuery;
            });
        }

        $options = array(
            'recursive' => true,
            'like' => true,
            'chaining' => count($filters) > 1 ? 'or' : 'and',
            'order' => $order,
            'orderdir' => strtoupper($sortOrder),
        );

        // get total user count...
        $total = $this->userService->getCountUsers($options, $filters);

        // get the users using requested paging...
        $users = $this->userService->getAllUsers(array_merge($options, [
            'offset' => $start,
            'limit' => $limit
        ]), $filters);

        $rolesProperty = new core_kernel_classes_Property(PROPERTY_USER_ROLES);

        $response = new stdClass();
        $readonly = array();
        $index = 0;
        foreach ($users as $user) {

            $propValues = $user->getPropertiesValues(array(
                PROPERTY_USER_LOGIN,
                PROPERTY_USER_FIRSTNAME,
                PROPERTY_USER_LASTNAME,
                PROPERTY_USER_MAIL,
                PROPERTY_USER_DEFLG,
                PROPERTY_USER_UILG,
                PROPERTY_USER_ROLES
            ));

            $roles = $user->getPropertyValues($rolesProperty);
            $labels = array();
            foreach ($roles as $uri) {
                $r = new core_kernel_classes_Resource($uri);
                $labels[] = $r->getLabel();
            }

            $id = tao_helpers_Uri::encode($user->getUri());
            $firstName = empty($propValues[PROPERTY_USER_FIRSTNAME]) ? '' : (string)current($propValues[PROPERTY_USER_FIRSTNAME]);
            $lastName = empty($propValues[PROPERTY_USER_LASTNAME]) ? '' : (string)current($propValues[PROPERTY_USER_LASTNAME]);
            $uiRes = empty($propValues[PROPERTY_USER_UILG]) ? null : current($propValues[PROPERTY_USER_UILG]);
            $dataRes = empty($propValues[PROPERTY_USER_DEFLG]) ? null : current($propValues[PROPERTY_USER_DEFLG]);

            $response->data[$index]['id'] = $id;
            $response->data[$index]['login'] = (string)current($propValues[PROPERTY_USER_LOGIN]);
            $response->data[$index]['firstname'] = $firstName;
            $response->data[$index]['lastname'] = $lastName;
            $response->data[$index]['email'] = (string)current($propValues[PROPERTY_USER_MAIL]);
            $response->data[$index]['roles'] = implode(', ', $labels);
            $response->data[$index]['dataLg'] = is_null($dataRes) ? '' : $dataRes->getLabel();
            $response->data[$index]['guiLg'] = is_null($uiRes) ? '' : $uiRes->getLabel();

            if ($user->getUri() == LOCAL_NAMESPACE . DEFAULT_USER_URI_SUFFIX) {
                $readonly[$id] = true;
            }
            $index++;
        }

        $response->page = floor($start / $limit) + 1;
        $response->total = ceil($total / $limit);
        $response->records = count($users);
        $response->readonly = $readonly;

        $this->returnJson($response, 200);
    }

    /**
     * Remove a user
     * The request must contains the user's login to remove
     * @return void
     */
    public function delete()
    {
        $deleted = false;
        $message = __('An error occured during user deletion');
        if (helpers_PlatformInstance::isDemo()) {
            $message = __('User deletion not permited on a demo instance');
        } elseif ($this->hasRequestParameter('uri')) {
            $user = new core_kernel_classes_Resource(tao_helpers_Uri::decode($this->getRequestParameter('uri')));

            if ($user->getUri() == LOCAL_NAMESPACE . DEFAULT_USER_URI_SUFFIX) {
                $message = __('Default user cannot be deleted');
            } elseif ($this->userService->removeUser($user)) {
                $deleted = true;
                $message = __('User deleted successfully');
            }
        }
        $this->returnJson(array(
            'deleted' => $deleted,
            'message' => $message
        ));
    }

    /**
     * form to add a user
     * @return void
     */
    public function add()
    {
        $myFormContainer = new tao_actions_form_Users(new core_kernel_classes_Class(CLASS_TAO_USER));
        $myForm = $myFormContainer->getForm();

        if ($myForm->isSubmited()) {
            if ($myForm->isValid()) {
                $values = $myForm->getValues();
                $values[PROPERTY_USER_PASSWORD] = core_kernel_users_Service::getPasswordHash()->encrypt($values['password1']);
                unset($values['password1']);
                unset($values['password2']);

                $binder = new tao_models_classes_dataBinding_GenerisFormDataBinder($myFormContainer->getUser());

                if ($binder->bind($values)) {
                    $this->setData('message', __('User added'));
                    $this->setData('exit', true);
                }
            }
        }

        $this->setData('loginUri', tao_helpers_Uri::encode(PROPERTY_USER_LOGIN));
        $this->setData('formTitle', __('Add a user'));
        $this->setData('myForm', $myForm->render());
        $this->setView('user/form.tpl');
    }

    public function addInstanceForm()
    {
        if (!tao_helpers_Request::isAjax()) {
            throw new Exception("wrong request mode");
        }

        $clazz = new core_kernel_classes_Class(CLASS_TAO_USER);
        $formContainer = new tao_actions_form_CreateInstance(array($clazz), array());
        $myForm = $formContainer->getForm();

        if ($myForm->isSubmited()) {
            if ($myForm->isValid()) {

                $properties = $myForm->getValues();
                $instance = $this->createInstance(array($clazz), $properties);

                $this->setData('message', __($instance->getLabel() . ' created'));
                //$this->setData('reload', true);
                $this->setData('selectTreeNode', $instance->getUri());
            }
        }

        $this->setData('formTitle', __('Create instance of ') . $clazz->getLabel());
        $this->setData('myForm', $myForm->render());

        $this->setView('form.tpl', 'tao');
    }

    /**
     * action used to check if a login can be used
     * @return void
     */
    public function checkLogin()
    {
        if (!tao_helpers_Request::isAjax()) {
            throw new Exception("wrong request mode");
        }

        $data = array('available' => false);
        if ($this->hasRequestParameter('login')) {
            $data['available'] = $this->userService->loginAvailable($this->getRequestParameter('login'));
        }

        $this->returnJson($data);
    }

    /**
     * Form to edit a user
     * User login must be set in parameter
     * @return void
     */
    public function edit()
    {
        if (!$this->hasRequestParameter('uri')) {
            throw new Exception('Please set the user uri in request parameter');
        }

        $user = new core_kernel_classes_Resource(tao_helpers_Uri::decode($this->getRequestParameter('uri')));

        $myFormContainer = new tao_actions_form_Users($this->userService->getClass($user), $user);
        $myForm = $myFormContainer->getForm();

        if ($myForm->isSubmited()) {
            if ($myForm->isValid()) {
                $values = $myForm->getValues();

                if (!empty($values['password2']) && !empty($values['password3'])) {
                    $values[PROPERTY_USER_PASSWORD] = core_kernel_users_Service::getPasswordHash()->encrypt($values['password2']);
                }

                unset($values['password2']);
                unset($values['password3']);

                if (!preg_match("/[A-Z]{2,4}$/", trim($values[PROPERTY_USER_UILG]))) {
                    unset($values[PROPERTY_USER_UILG]);
                }
                if (!preg_match("/[A-Z]{2,4}$/", trim($values[PROPERTY_USER_DEFLG]))) {
                    unset($values[PROPERTY_USER_DEFLG]);
                }

                $binder = new tao_models_classes_dataBinding_GenerisFormDataBinder($user);

                if ($binder->bind($values)) {
                    $this->setData('message', __('User saved'));
                }
            }
        }

        $this->setData('formTitle', __('Edit a user'));
        $this->setData('myForm', $myForm->render());
        $this->setView('user/form.tpl');
    }
}
