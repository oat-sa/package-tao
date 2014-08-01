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
?>
<?php
/**
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package tao
 
 *
 */
class tao_actions_Api extends tao_actions_CommonModule {

	/**
	 * @var tao_models_classes_UserService
	 */
	protected $userService;
	
	/**
	 * Name of the variable used for the execution environment 
	 * @var string
	 */
	const ENV_VAR_NAME = 'taoEnv';
	
	/**
	 * Constructor
	 * initialize the user service
	 */
	public function __construct(){
		
		parent::__construct();
		
		$this->userService = tao_models_classes_UserService::singleton();
	}
	
	/**
	 * create a unique token that will be exchanged during the communications
	 * @return string the token
	 */
	protected function createToken(){
		//get the sum of a unique token to identify the content
		return sha1( uniqid(self::ENV_VAR_NAME, true) );		//the env var is just used as a SALT
	}
	
	/**
	 * Build and load the item execution environment.
	 * 
	 * @param core_kernel_classes_Resource $processExecution
	 * @param core_kernel_classes_Resource $item
	 * @param core_kernel_classes_Resource $test
	 * @param core_kernel_classes_Resource $delivery
	 * @param core_kernel_classes_Resource $user
	 * 
	 * @return array
	 */
	protected function createExecutionEnvironment(core_kernel_classes_Resource $processExecution, 
													core_kernel_classes_Resource $item, 
													core_kernel_classes_Resource $test, 
													core_kernel_classes_Resource $delivery, 
													core_kernel_classes_Resource $user){
		$executionEnvironment = array();
		
		foreach(func_get_args() as $arg){
			if(is_null($arg)){
				return $executionEnvironment;
			}
		}
		
		//we build the data to give to the item
		$executionEnvironment = array(

			'token'			=> $this->createToken(),
			'localNamespace' => rtrim(common_ext_NamespaceManager::singleton()->getLocalNamespace()->getUri(), '#'),
		
			CLASS_PROCESS_EXECUTIONS => array(
				'uri'		=> $processExecution->getUri(),
				RDFS_LABEL	=> $processExecution->getLabel()
			),
			
			TAO_ITEM_CLASS	=> array(
				'uri'		=> $item->getUri(),
				RDFS_LABEL	=> $item->getLabel()
			),
			TAO_TEST_CLASS	=> array(
				'uri'		=> $test->getUri(),
				RDFS_LABEL	=> $test->getLabel()
			),
			TAO_DELIVERY_CLASS	=> array(
				'uri'		=> $delivery->getUri(),
				RDFS_LABEL	=> $delivery->getLabel()
			),
			TAO_SUBJECT_CLASS => array(
				'uri'					=> $user->getUri(),
				RDFS_LABEL				=> $user->getLabel(),
				PROPERTY_USER_LOGIN		=> (string)$user->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_USER_LOGIN)),
				PROPERTY_USER_FIRSTNAME	=> (string)$user->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_USER_FIRSTNAME)),
				PROPERTY_USER_LASTNAME	=> (string)$user->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_USER_LASTNAME))
			)
		);
		
		$session = PHPSession::singleton();
		$session->setAttribute(self::ENV_VAR_NAME.'_'.tao_helpers_Uri::encode($user->getUri()), $executionEnvironment);
		
		return $executionEnvironment;
	} 
	
	/**
	 * create an execution environnement only for the authentication
	 * @return array the executionEnvironment
	 */
	public static function createAuthEnvironment(){
		
		$context = Context::getInstance();
		$session = PHPSession::singleton();
		if(strtolower($context->getActionName()) == 'createauthenvironment'){
			throw new Exception('Action denied, only servers side call are allowed');
		}
		if(!$session->hasAttribute('processUri')){
			throw new Exception('Envirnoment can be create only in a workflow context');
		}
		$processExecution = new core_kernel_classes_Resource(tao_helpers_Uri::decode($session->getAttribute('processUri')));
		
		$userService = tao_models_classes_UserService::singleton();
		$user = $userService->getCurrentUser();
		if(is_null($user)){
			throw new Exception('No user is logged in');
		}
		
		$sessionKey = self::ENV_VAR_NAME.'_'.tao_helpers_Uri::encode($user->getUri());
		if($session->hasAttribute($sessionKey)){
			$executionEnvironment = $session->getAttribute($sessionKey);
			if(isset($executionEnvironment['token']) && $executionEnvironment[CLASS_PROCESS_EXECUTIONS]['uri'] == $processExecution->getUri() ){
				return $executionEnvironment;
			}
		}
			
		$executionEnvironment = array(
			'token' => self::createToken(),
			CLASS_PROCESS_EXECUTIONS => array(
				'uri'		=> $processExecution->getUri(),
				RDFS_LABEL	=> $processExecution->getLabel()
			),
			TAO_SUBJECT_CLASS => array(
				'uri'					=> $user->getUri(),
				RDFS_LABEL				=> $user->getLabel(),
				PROPERTY_USER_LOGIN		=> (string)$user->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_USER_LOGIN)),
				PROPERTY_USER_FIRSTNAME	=> (string)$user->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_USER_FIRSTNAME)),
				PROPERTY_USER_LASTNAME	=> (string)$user->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_USER_LASTNAME))
			)
		);
		$session->setAttribute($sessionKey, $executionEnvironment);
		return  $executionEnvironment;
	}
	
	/**
	 * Get the data of the current execution
	 * @return array
	 */
	protected function getExecutionEnvironment(){
		$session = PHPSession::singleton();
		
		$currentUser = $this->userService->getCurrentUser();
		if(!is_null($currentUser)){
			$sessionKey =  self::ENV_VAR_NAME . '_' . tao_helpers_Uri::encode($currentUser->getUri());
			
			if($session->hasAttribute($sessionKey)){
				$executionEnvironment = $session->getAttribute($sessionKey);
				
				if(isset($executionEnvironment['token'])){
					return $executionEnvironment;
				}
			}
		}
		return array();
	}
	
	/**
	 * Enbales you to authenticate a communication based on the token
	 * @param string $token
	 * @return boolean
	 */
	protected function authenticate($token){
		
		if(!empty($token)){
			
			$session = PHPSession::singleton();
			
			$currentUser = $this->userService->getCurrentUser();
			if(!is_null($currentUser)){
				$sessionKey =  self::ENV_VAR_NAME . '_' . tao_helpers_Uri::encode($currentUser->getUri());
				
				if($session->hasAttribute($sessionKey)){
					$executionData = $session->getAttribute($sessionKey);
					
					if(isset($executionData['token'])){
						if($executionData['token'] == $token){
							return true;
						}
					}
				}
			}
		}
		return false;
	}
	
}
?>