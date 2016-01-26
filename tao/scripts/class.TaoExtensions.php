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


/**
 * This Script class aims at providing tools to manage TAO extensions.
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package tao
 
 */
class tao_scripts_TaoExtensions
    extends tao_scripts_Runner
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * The current action the TaoExtensions Script class is running. It
     * to the 'action' parameter given in input.
     *
     * @access public
     * @var string
     */
    public $currentAction = '';

    /**
     * Contains the final values of the CLI parameters given as input for this
     * (merge of the default values and paraleters array).
     *
     * @access public
     * @var array
     */
    public $options = array();

    /**
     * States if the Generis user is connected or not.
     *
     * @access public
     * @var boolean
     */
    public $connected = false;

    // --- OPERATIONS ---

    /**
     * Instructions to execute before the run method.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return void
     */
    public function preRun()
    {
       
        $this->checkInput();

    }

    /**
     * Instructions to execute to handle the action to perform.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return void
     */
    public function run()
    {
       
        $this->outVerbose("Connecting...");
        if ($this->connect($this->options['user'], $this->options['password'])){
            $this->outVerbose("Connected to TAO API.");
            
            switch ($this->options['action']){
                case 'setConfig':
                    $this->setCurrentAction($this->options['action']);
                    $this->actionSetConfig();
                break;
                
                case 'install':
                    $this->setCurrentAction($this->options['action']);
                    $this->actionInstall();
                break;
            }
            
            $this->disconnect();    
        }
        else{
            $this->error("Could not connect to TAO API. Please check your user name and password.", true);
        }
       
    }

    /**
     * Instructions to execute after the postRun method.
     *
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     */
    public function postRun()
    {
       
        $this->outVerbose("Script executed gracefully.");
       
    }

    /**
     * Checks the input parameters when the script is called from the CLI. It
     * check parameters common to any action (user, password, action) and
     * to the appropriate checking method for the other parameters.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return void
     */
    public function checkInput()
    {
        
        $this->options = array('verbose' => false,
                               'action' => null,
                               'user' => null,
                               'password' => null);
                               
        $this->options = array_merge($this->options, $this->parameters);
        
        // Check common inputs.
        if ($this->options['user'] == null){
            $this->error("Please provide a Generis 'user'.", true);
        }
        else{
            if ($this->options['password'] == null){
                $this->error("Please provide a Generis 'password'.", true);
            }
            else{
                if ($this->options['action'] == null){
                    $this->error("Please provide the 'action' parameter.", true);
                }
                else{
                    switch ($this->options['action']){
                        case 'setConfig':
                            $this->checkSetConfigInput();
                        break;
                        
                        case 'install':
                            $this->checkInstallInput();
                        break;
                        
                        default:
                            $this->error("Please provide a valid 'action' parameter.", true);
                        break;
                    }
                }    
            }
        }
       
    }

    /**
     * Get the current action being executed.
     *
     * @access protected
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    protected function getCurrentAction()
    {
        $returnValue = (string) '';

       
        $returnValue = $this->currentAction;


        return (string) $returnValue;
    }

    /**
     * Set the current action being executed.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string currentAction The name of the current action being executed by the script.
     * @return void
     */
    public function setCurrentAction($currentAction)
    {
        
        $this->currentAction = $currentAction;
       
    }

    /**
     * Sets a configuration parameter of an extension. The configuration
     * to change is provided with the 'configParameter' CLI argument and its
     * is provided with the 'configValue' CLI argument. The extension on which
     * want to change a parameter value is provided with the 'extension' CLI
     *
     * Parameters that can be changed are:
     * - loaded (boolean)
     * - loadAtStartup (boolean)
     * - ghost (boolean)
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return void
     */
    public function actionSetConfig()
    {
       
        
        // The values accepted in the 'loaded', 'loadAtStartup' and 'ghost' columns of
        // the extensions table are 0 | 1.
        $configValue = $this->options['configValue'];
        $configParam = $this->options['configParameter'];
        $extensionId = $this->options['extension'];
        
        try{
            $ext = common_ext_ExtensionsManager::singleton()->getExtensionById($extensionId);
            $currentConfig = $ext->getConfiguration();
            
            if ($currentConfig == null){
                $this->error("The extension '${extensionId} is not referenced.", true);
            }
            else{
                // Change the configuration with the new value.
                switch ($configParam){
                    case 'loaded':
                        $currentConfig->loaded = $configValue;
                    break;
                    
                    case 'loadAtStartup':
                        $currentConfig->loadedAtStartUp = $configValue;
                    break;
                    
                    case 'ghost':
                        $currentConfig->ghost = $configValue;
                    break;
                    
                    default:
                        $this->error("Unknown configuration parameter '${configParam}'.", true);
                    break;
                }
            
                $currentConfig->save($ext);
                $this->outVerbose("Configuration parameter '${configParam}' successfully updated to " . (($configValue == true) ? 1 : 0) . " for extension '${extensionId}'.");
            }
        }
        catch (common_ext_ExtensionException $e){
            $this->error("The extension '${extensionId}' does not exist or has no manifest.", true);
        }
        
    }

    /**
     * Create a new instance of the TaoExtensions script and executes it. If the
     * inputFormat parameter is not provided, the script configures itself
     * to foster code reuse.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  array inputFormat
     * @param  array options
     * @return mixed
     */
    public function __construct($inputFormat = array(), $options = array())
    {
       
        if (count($inputFormat) == 0){
            // Autoconfigure the script.
            $inputFormat = array('min' => 3,
                                 'parameters' => array(
                                                        array('name' => 'verbose',
                                                              'type' => 'boolean',
                                                              'shortcut' => 'v',
                                                              'description' => 'Verbose mode'
                                                              ),
                                                        array('name' => 'user',
                                                              'type' => 'string',
                                                              'shortcut' => 'u',
                                                              'description' => 'Generis user (must be a TAO Manager)'
                                                              ),
                                                        array('name' => 'password',
                                                              'type' => 'string',
                                                              'shortcut' => 'p',
                                                              'description' => 'Generis password'
                                                             ),
                                                        array('name' => 'action',
                                                              'type' => 'string',
                                                              'shortcut' => 'a',
                                                              'description' => 'Action to perform'
                                                             ),
                                                        array('name' => 'configParameter',
                                                              'type' => 'string',
                                                              'shortcut' => 'cP',
                                                              'description' => "Configuration parameter (loaded|loadAtStartup|ghost) to change when the 'setConfig' action is called"
                                                             ),
                                                        array('name' => 'configValue',
                                                              'type' => 'boolean',
                                                              'shortcut' => 'cV',
                                                              'description' => "Configuration value to set when the 'setConfig' action is called"
                                                             ),
                                                        array('name' => 'extension',
                                                              'type' => 'string',
                                                              'shortcut' => 'e',
                                                              'description' => "Extension ID that determines the TAO extension to focus on"),
                                                        array('name' => 'data',
                                                              'type' => 'boolean',
                                                              'shortcut' => 'd',
                                                              'description' => "States if local data must be imported or not at installation time")
                                                       )
                                );
        }

        parent::__construct($inputFormat, $options);
      
    }

    /**
     * Check additional inputs for the 'setConfig' action.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return void
     */
    public function checkSetConfigInput()
    {
        
        $availableParameters = array('loaded', 'loadAtStartup', 'ghost');
        $defaults = array('extension' => null,
                          'configParameter' => null,
                          'configValue' => null);
                          
        $this->options = array_merge($defaults, $this->options);
        
        if ($this->options['configParameter'] == null){
            $this->error("Please provide the 'configParam' parameter.", true);
        }
        else if (!in_array($this->options['configParameter'], $availableParameters)){
            $this->error("Please provide a valid 'configParam' parameter value (" . implode("|", $availableParameters) . ").", true);
        }
        else if ($this->options['configValue'] === null){
            $this->error("Please provide the 'configValue' parameter.", true);
        }
        else if ($this->options['extension'] == null){
            $this->error("Please provide the 'extension' parameter.", true);
        }
    }
    
    /**
     * Set the connected attribute to a given value.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  boolean value true if the user is connected, otherwhise false.
     * @return void
     */
    public function setConnected($value)
    {
         
        $this->connected = $value;
         
    }


    /**
     * Short description of method isConnected
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return boolean
     */
    public function isConnected()
    {
        return (bool) $this->connected;

    }
    


    /**
     * Display an error message. If the stopExec parameter is set to true, the
     * of the script stops and the currently connected user is disconnected if
     * It overrides the Runner->err method for this purpose.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string message The error message to display.
     * @param  boolean stopExec If set to false, the execution of the script stops.
     * @return mixed
     */
    public function error($message, $stopExec = false)
    {
        
        if ($stopExec == true){
            $this->disconnect();
        }
        
        $this->err($message, $stopExec);
        
    }

    /**
     * Connect to the generis API by using the CLI arguments 'user' and
     * It returns true or false depending on the connection establishement.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string user
     * @param  string password
     * @return boolean
     */
    public function connect($user, $password)
    {
        $returnValue = (bool) false;

       
        $userService = tao_models_classes_UserService::singleton();
        $returnValue = $userService->loginUser($user, $password);
        $this->setConnected($returnValue);
       

        return (bool) $returnValue;
    }

    /**
     * Disconnect the currently connected user.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return mixed
     */
    public function disconnect()
    {
       
        if ($this->isConnected()){
            $this->outVerbose("Disconnecting user...");
            $userService = tao_models_classes_UserService::singleton();
            if ($userService->logout() == true){
                $this->outVerbose("User gracefully disconnected from TAO API.");
                $this->setConnected(false);
            }
            else{
                $this->error("User could not be disconnected from TAO API.");
            }
        }
       
    }

    /**
     * Short description of method actionInstall
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return void
     */
    public function actionInstall()
    {
       
        $extensionId = $this->options['extension']; // ID of the extension to install.
        $importLocalData = $this->options['data']; // Import local data (local.rdf) or not ?
        try{
            // Retrieve the extension's information.
            $this->outVerbose("Locating extension '${extensionId}'...");
            $extensionManager = common_ext_ExtensionsManager::singleton();
            $ext = $extensionManager->getExtensionById($extensionId);
            $this->outVerbose("Extension located.");
            
            try{
                // Install the extension.
                $this->outVerbose("Installing extension '${extensionId}'...");
                $installer = new tao_install_ExtensionInstaller($ext, $importLocalData);
                $installer->install();
                $this->outVerbose("Extension successfully installed.");
            }
            catch (common_ext_ForbiddenActionException $e){
                $this->error("A forbidden action was undertaken: " . $e->getMessage() . " .", true);
            }
            catch (common_ext_AlreadyInstalledException $e){
                $this->error("Extension '" . $e->getExtensionId() . "' is already installed.", true);
            }
            catch (common_ext_MissingExtensionException $e){
                $this->error("Extension '" . $extensionId . " is dependant on extension '" . $e->getExtensionId() . "' but is missing. Install '" . $e->getExtensionId() . "' first.", true);
            }
        }
        catch (common_ext_ExtensionException $e){
            $this->error("An unexpected error occured: " . $e->getMessage(), true);
        }
      
    }

    /**
     * Short description of method checkInstallInput
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return void
     */
    public function checkInstallInput()
    {
       
        $defaults = array('extension' => null,
                          'data' => true);
                          
        $this->options = array_merge($defaults, $this->options);
        
        if ($this->options['extension'] == null){
            $this->error("Please provide the 'extension' parameter.", true);
        }
       
    }

}

?>