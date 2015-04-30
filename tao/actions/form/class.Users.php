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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

/**
 * This container initialize the user edition form.
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package tao
 
 */
class tao_actions_form_Users
    extends tao_actions_form_Instance
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute user
     *
     * @access protected
     * @var Resource
     */
    protected $user = null;

    /**
     * Short description of attribute formName
     *
     * @access protected
     * @var string
     */
    protected $formName = '';

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Class clazz
     * @param  Resource user
     * @param  boolean forceAdd
     * @return mixed
     */
    public function __construct( core_kernel_classes_Class $clazz,  core_kernel_classes_Resource $user = null, $forceAdd = false)
    {
        
        
    	if (empty($clazz)){
    		throw new Exception('Set the user class in the parameters');	
    	}
    	
    	$this->formName = 'user_form';
    	
    	$options = array();
    	$service = tao_models_classes_UserService::singleton();
    	if(!empty($user)){
    		$this->user = $user;
			$options['mode'] = 'edit';
    	}
    	else{
    		if(isset($_POST[$this->formName.'_sent']) && isset($_POST['uri'])){
    			$this->user = new core_kernel_classes_Resource(tao_helpers_Uri::decode($_POST['uri']));
    		}
    		else{
    			$this->user = $service->createInstance($clazz, $service->createUniqueLabel($clazz));
    		}
    		$options['mode'] = 'add';
    	}
    	
    	if($forceAdd){
    		$options['mode'] = 'add';
    	}
    	
    	$options['topClazz'] = CLASS_GENERIS_USER;
    	
    	parent::__construct($clazz, $this->user, $options);
    	
        
    }

    /**
     * Short description of method getUser
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return core_kernel_classes_Resource
     */
    public function getUser()
    {
        $returnValue = null;

        
        
        $returnValue = $this->user;
        
        

        return $returnValue;
    }

    /**
     * Short description of method initForm
     *
     * @access protected
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    protected function initForm()
    {
        
		
    	parent::initForm();
    	
    	$this->form->setName($this->formName);
    	
		$actions = tao_helpers_form_FormFactory::getCommonActions('top');
		$this->form->setActions($actions, 'top');
		$this->form->setActions($actions, 'bottom');
		
        
    }

    /**
     * Short description of method initElements
     *
     * @access protected
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    protected function initElements()
    {
        
		
		if(!isset($this->options['mode'])){
			throw new Exception("Please set a mode into container options ");
		}
		
		parent::initElements();
		
		//login field
		$loginElement = $this->form->getElement(tao_helpers_Uri::encode(PROPERTY_USER_LOGIN));
		$loginElement->setDescription($loginElement->getDescription() . ' *');
		if($this->options['mode'] == 'add'){
			$loginElement->addValidators(array(
				tao_helpers_form_FormFactory::getValidator('NotEmpty'),
				tao_helpers_form_FormFactory::getValidator('Callback', array(
					'object' => tao_models_classes_UserService::singleton(), 
					'method' => 'loginAvailable', 
					'message' => __('This Login is already in use') 
				))
			));
		}
		else{
			$loginElement->setAttributes(array('readonly' => 'readonly', 'disabled' => 'disabled'));
		}
		
		
		//set default lang to the languages fields
		$langService = tao_models_classes_LanguageService::singleton();
		
		$dataLangElt = $this->form->getElement(tao_helpers_Uri::encode(PROPERTY_USER_DEFLG));
		$dataLangElt->addValidator(tao_helpers_form_FormFactory::getValidator('NotEmpty'));
		$dataLangElt->setDescription($dataLangElt->getDescription() . ' *');
    	$dataUsage = new core_kernel_classes_Resource(INSTANCE_LANGUAGE_USAGE_DATA);
		$dataOptions = array();
        foreach($langService->getAvailableLanguagesByUsage($dataUsage) as $lang){
			$dataOptions[tao_helpers_Uri::encode($lang->getUri())] = $lang->getLabel();
		}
		$dataLangElt->setOptions($dataOptions);
		
		$uiLangElt = $this->form->getElement(tao_helpers_Uri::encode(PROPERTY_USER_UILG));
        $uiLangElt->addValidator(tao_helpers_form_FormFactory::getValidator('NotEmpty'));
        $uiLangElt->setDescription($uiLangElt->getDescription() . ' *');
    	$guiUsage = new core_kernel_classes_Resource(INSTANCE_LANGUAGE_USAGE_GUI);
		$guiOptions = array();
        foreach($langService->getAvailableLanguagesByUsage($guiUsage) as $lang){
			$guiOptions[tao_helpers_Uri::encode($lang->getUri())] = $lang->getLabel();
		}
		$uiLangElt->setOptions($guiOptions);
		
		// roles field
		$property = new core_kernel_classes_Property(PROPERTY_USER_ROLES);
		$roles = $property->getRange()->getInstances(true);
		$rolesOptions = array();
		foreach ($roles as $r){
			$rolesOptions[tao_helpers_Uri::encode($r->getUri())] = $r->getLabel();
		}
		asort($rolesOptions);
								 
		$rolesElt = $this->form->getElement(tao_helpers_Uri::encode($property->getUri()));
		$rolesElt->setDescription($rolesElt->getDescription() . ' *');
		$rolesElt->addValidator(tao_helpers_form_FormFactory::getValidator('NotEmpty'));
		$rolesElt->setOptions($rolesOptions);
		
		// password field
		$this->form->removeElement(tao_helpers_Uri::encode(PROPERTY_USER_PASSWORD));
		
		if($this->options['mode'] == 'add'){
			$pass1Element = tao_helpers_form_FormFactory::getElement('password1', 'Hiddenbox');
			$pass1Element->setDescription(__('Password *'));
			$pass1Element->addValidators(array(
				tao_helpers_form_FormFactory::getValidator('NotEmpty'),
				tao_helpers_form_FormFactory::getValidator('Length', array('min' => 3))
			));
			$this->form->addElement($pass1Element);
			
			$pass2Element = tao_helpers_form_FormFactory::getElement('password2', 'Hiddenbox');
			$pass2Element->setDescription(__('Repeat password *'));
			$pass2Element->addValidators(array(
				tao_helpers_form_FormFactory::getValidator('NotEmpty'),
				tao_helpers_form_FormFactory::getValidator('Password', array('password2_ref' => $pass1Element)),
			));
			$this->form->addElement($pass2Element);
		}
		else {
			if (helpers_PlatformInstance::isDemo()) {
				$warning  = tao_helpers_form_FormFactory::getElement('warningpass', 'Label');
				$warning->setValue(__('Unable to change passwords in demo mode'));
				$this->form->addElement($warning);
				$this->form->createGroup("pass_group", __("Change the password"), array('warningpass'));
			} else {
			
				$pass2Element = tao_helpers_form_FormFactory::getElement('password2', 'Hiddenbox');
				$pass2Element->setDescription(__('New password'));
				$pass2Element->addValidators(array(
					tao_helpers_form_FormFactory::getValidator('Length', array('min' => 3))
				));
				$this->form->addElement($pass2Element);
				
				$pass3Element = tao_helpers_form_FormFactory::getElement('password3', 'Hiddenbox');
				$pass3Element->setDescription(__('Repeat new password'));
				$pass3Element->addValidators(array(
					tao_helpers_form_FormFactory::getValidator('Password', array('password2_ref' => $pass2Element)),
				));
				$this->form->addElement($pass3Element);
				
				$this->form->createGroup("pass_group", __("Change the password"), array('password1', 'password2', 'password3'));
				if (empty($_POST[$pass2Element->getName()]) && empty($_POST[$pass3Element->getName()])) {

					$pass2Element->setForcedValid();
					$pass3Element->setForcedValid();
				}
			}
		}
		/**/
		
		
        
    }

}

?>