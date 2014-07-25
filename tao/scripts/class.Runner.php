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
 * Short description of class tao_scripts_Runner
 *
 * @abstract
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 * @package tao
 
 */
abstract class tao_scripts_Runner
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---
    private $isCli;
    private $logOny;
    /**
     * Short description of attribute parameters
     *
     * @access protected
     * @var array
     */
    protected $parameters = array();

    /**
     * Short description of attribute inputFormat
     *
     * @access protected
     * @var array
     */
    protected $inputFormat = array();

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  array inputFormat
     * @param  array options
     * @return mixed
     */
    public function __construct($inputFormat = array(), $options = array())
    {
        
        if(PHP_SAPI == 'cli' && !isset($options['argv'])){
            $this->argv = $_SERVER['argv'];
            $this->isCli = true;
        }
        else{
            $this->argv = isset($options['argv']) ? $options['argv'] : array();
            $this->isCli = false;
        }
        if(isset($options['output_mode'])  && $options['output_mode'] == 'log_only'){
            $this->logOny = true;
        }
        $this->out("* Running ".(isset($this->argv[0]) ? $this->argv[0] : __CLASS__) , $options);
         
        $this->inputFormat = $inputFormat;
        
        //check if help is needed
        $helpTokens = array('-h', 'help', '-help', '--help)');
        foreach( $helpTokens as $helpToken){
            if(in_array($helpToken, $this->argv)){
            		 	$this->help();
            		 	exit(0);
            }
        }
        
        //validate the input parameters
        if(!$this->validateInput()){
            $this->help();
            $this->err("Scripts stopped!", true);
        }
         
        //script run loop
         
        $this->preRun();
         
        $this->run();
         
        $this->postRun();
         
        $this->out('Execution of Script ' . (isset($this->argv[0]) ? $this->argv[0] : __CLASS__) . ' completed' , $options);
         
    	
        
    }

    /**
     * Short description of method validateInput
     *
     * @access private
     * @author firstname and lastname of author, <author@example.org>
     * @return boolean
     */
    private function validateInput()
    {
        $returnValue = (bool) false;

        
        
        $returnValue = true;
        
        /**
         * Parse the arguments from the command lines 
         * and set them into the parameter variable.
         * All the current formats are allowed:
         * <code>
         * php script.php -arg1 value1 -arg2 value2
         * php script.php --arg1 value1 --arg2 value2
         * php script.php -arg1=value1 -arg2=value2
         * php script.php --arg1=value1 --arg2=value2
         * php script.php value1 value2 //the key will be numeric
         * </code>
         */
        $i = 1;
        while($i < count($this->argv)){
        	$arg = trim($this->argv[$i]);
        	if(!empty($arg)){
        	    // command -(-)arg1=value1
        		if(preg_match("/^[\-]{1,2}\w+=(.*)+$/", $arg)){
        		    $sequence = explode('=', preg_replace("/^[\-]{1,}/", '', $arg));
	        		if(count($sequence) >= 2){
	        			$this->parameters[$sequence[0]] = $sequence[1];
	        		}
	        	}
	        	// command -(-)arg1 value1 value2
        		else if(preg_match("/^[\-]{1,2}\w+$/", $arg)){
        		    $key = preg_replace("/^[\-]{1,}/", '', $arg);
    			    $this->parameters[$key] = '';
    			    while(isset($this->argv[$i + 1]) && substr(trim($this->argv[$i + 1]),0,1)!='-') {
    			        $this->parameters[$key] .= trim($this->argv[++$i]).' ';
    			    }
    			    $this->parameters[$key] = substr($this->parameters[$key], 0, -1); 
	        	}
        	    // command value1 value2
	        	else{
	        		$this->parameters[$i] = $arg;
	        	}
        	}
        	$i++;
        }
        
        //replaces shortcuts by their original names
        if (isset($this->inputFormat['parameters'])) {
            foreach($this->inputFormat['parameters'] as $parameter){
            	if(isset($parameter['shortcut'])){
            		$short = $parameter['shortcut'];
            		$long = $parameter['name'];
            		if(array_key_exists($short, $this->parameters) && !array_key_exists($long, $this->parameters)){
    					$this->parameters[$long] = $this->parameters[$short];
    					unset($this->parameters[$short]);
    				}
            	}
            }
        }
        
        //one we have the parameters, we can validate it
        if(isset($this->inputFormat['min'])){
        	$min 	= (int) $this->inputFormat['min'];
        	$found 	=  count($this->parameters);
        	if($found < $min){
        		$this->err("Invalid parameter count: $found parameters found ($min expected)");
        		$returnValue = false;
        	}
        }
        
        if(isset($this->inputFormat['required']) && is_array($this->inputFormat['required']) && count($this->inputFormat['required'])){
        	
			$requireds = array();
			if(!is_array($this->inputFormat['required'][0])){
        		$requireds = array($this->inputFormat['required']);
        	}else{
        		$requireds = $this->inputFormat['required'];
        	}
        	
        	$found = false;
        	foreach($requireds as $required){
        		
        		$matched = 0;
	        	foreach($required as $parameter){
	        		if(array_key_exists($parameter, $this->parameters)){
	        			$matched++;
	        		}
	        	}
	        	if($matched == count($required)){
        			$found = true;
        			break;
	        	}
        	}
        	
        	if(!$found){
        		$this->err("Unable to find required arguments");
        		$returnValue = false;
        	}
        }
        
        if($returnValue && isset($this->inputFormat['parameters'])){
        	 foreach($this->inputFormat['parameters'] as $parameter){
        		if(isset($this->parameters[$parameter['name']])){
	        	 	$input = $this->parameters[$parameter['name']];
		        	switch($parameter['type']){
		        		case 'file': 
		       				if( !is_file($input) || 
		       					!file_exists($input) || 
		       					!is_readable($input))
		       				{
		       					$this->err("Unable to access to the file: $input");
		       					$returnValue = false;
		       				}
		       				break;
	        			case 'dir': 
	        				if( !is_dir($input) || 
	        					!is_readable($input))
	        				{
	        					$this->err("Unable to access to the directory: $input");
	        					$returnValue = false;
	        				}
	        				break;
	        			case 'path': 
	        				if( !is_dir(dirname($input)) )
	        				{
	        					$this->err("Wrong path given: $input");
	        					$returnValue = false;
	        				}
	        				break;
	        			case 'int':
	        			case 'float':
	        			case 'double':
	        				if(!is_numeric($input)){
	        					$this->err("$input is not a valid ".$parameter['type']);
	        					$returnValue = false;
	        				}
	        				break;
	        			case 'string':
	        				if(!is_string($input)){
	        					$this->err("$input is not a valid ".$parameter['type']);
	        					$returnValue = false;
	        				}
	        				break;
	        			case 'boolean':
	        				if(!is_bool($input) && strtolower($input) != 'true' && strtolower($input) != 'false' && !empty($input)){
	        					$this->err("$input is not a valid ".$parameter['type']);
	        					$returnValue = false;
	        				}else{
	        					if(is_bool($input)){
	        						$this->parameters[$parameter['name']] = $input = settype($input, 'boolean');
	        					}
	        					else if(!empty($input)){
	        						$this->parameters[$parameter['name']] = ((strtolower($input) == 'true') ? true : false);
	        					}
	        					else{
	        						$this->parameters[$parameter['name']] = true;
	        					}
	        				}
	        				break;
	        				
	        		}
        		}
        	}
        }
        
 
        
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method preRun
     *
     * @access protected
     * @author firstname and lastname of author, <author@example.org>
     * @return mixed
     */
    protected function preRun()
    {
        
        
    }

    /**
     * Short description of method run
     *
     * @abstract
     * @access protected
     * @author firstname and lastname of author, <author@example.org>
     * @return mixed
     */
    protected abstract function run();

    /**
     * Short description of method postRun
     *
     * @access protected
     * @author firstname and lastname of author, <author@example.org>
     * @return mixed
     */
    protected function postRun()
    {
        
        
    }
    /**
     *
     * @access
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * @param unknown $message
     * @param unknown $options
     * @return Ambigous <string, unknown>
     */
    private function renderCliOutput($message, $options = array()){
        $returnValue = '';
         
        if(isset($options['prefix'])){
            $returnValue = $options['prefix'];
        }
         
        $colorized = false;
        isset($options['color']) ?  $color = $options['color'] : $color = 'grey';
        $color = trim(tao_helpers_Cli::getFgColor($color));
        if(!empty($color) && substr(strtoupper(PHP_OS),0,3) != 'WIN'){
            $colorized = true;
             
            $returnValue .= "\033[{$color}m" ;
        }
        isset($options['background']) ?  $bg = $options['background'] : $bg = '';
        $bg = trim(tao_helpers_Cli::getBgColor($bg));
        if(!empty($bg)){
            $colorized = true;
            $returnValue .= "\033[{$bg}m";
        }
    
        $returnValue .= $message;
         
        if(!isset($options['inline'])){
            $returnValue .= "\n";
        }
    
        if($colorized){
            $returnValue .= "\033[0m";
        }
        return $returnValue;
    }
    
    /**
     * 
     * @access private
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * @param string $message
     * @param array $options
     * @return string
     */
    private function renderHtmlOutput($message, $options = array()){
        $returnValue = '';
    
        if(isset($options['prefix'])){
            $returnValue = $options['prefix'];
        }
    
        isset($options['color']) ?  $color = $options['color'] : $color = 'grey';
        if(!empty($color)){
            $colorized = true;
            $returnValue .= '<div class="' .$color.'">';
        }
        $returnValue .= $message;
    
        if(!isset($options['inline'])){
            $returnValue .= "<br/>";
        }
    
        if($colorized){
            $returnValue .= "</div>";
        }
        return $returnValue;
    }
    /**
     * Short description of method out
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  string message
     * @param  array options
     */
    public function out($message, $options = array())
    {
        
        $returnValue =  $this->isCli ? $this->renderCliOutput($message,$options) : $this->renderHtmlOutput($message,$options);
        if ($this->logOny){
            //do nothing
        }
        else{
            echo $returnValue ;
        }
        common_Logger::i($message, array('SCRIPTS_RUNNER'));
        
    }

    /**
     * Short description of method err
     *
     * @access protected
     * @author firstname and lastname of author, <author@example.org>
     * @param  string message
     * @param  boolean stopExec
     */
    protected function err($message, $stopExec = false)
    {
        
        common_Logger::e($message);
        echo $this->out($message, array('color' => 'light_red'));
        
        if($stopExec == true){
                    if($this->isCli){
        	   exit(1);	//exit the program with an error
            }
            else {
                throw new Exception($message);
            }
        }
        
        
    }

    /**
     * Short description of method help
     *
     * @access protected
     * @author firstname and lastname of author, <author@example.org>
     * @return mixed
     */
    protected function help()
    {
        
        
    	$usage = "Usage:php {$this->argv[0]} [arguments]\n";
    	$usage .= "\nArguments list:\n";
		foreach($this->inputFormat['parameters'] as $parameter){
		    $line = '';
       		if(isset($parameter['required'])){
       			if($parameter['required'] == true){
       				$line .= "Required";
       			}
       			else{
       				$line .= "Optional";
       			}
       		}
       		else{
       			//$usage .= "\t";
       		}
       		$line = str_pad($line, 15).' ';
			$line .= "--{$parameter['name']}";
       		if(isset($parameter['shortcut'])){
       			$line .= "|-{$parameter['shortcut']}";
       		}
       		$line = str_pad($line, 39).' ';
       		$line .= "{$parameter['description']}";
       		$usage .= $line."\n";
       	}
  		$this->out($usage, array('color' => 'light_blue'));
    	
        
    }

    /**
     * Short description of method outVerbose
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  string message
     * @param  array options
     * @return mixed
     */
    public function outVerbose($message, $options = array())
    {
        
        common_Logger::i($message);
        if (isset($this->parameters['verbose']) && $this->parameters['verbose'] === true) {
        	$this->out($message, $options);
        }
        
    }

} /* end of abstract class tao_scripts_Runner */

?>