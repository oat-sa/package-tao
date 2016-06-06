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
 * The Parser enables you to load, parse and validate xml content from an xml
 * Usually used for to load and validate the itemContent  property.
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 
 */
class tao_models_classes_Parser
{

    /**
     * XML content string
     *
     * @access protected
     * @var string
     */
    protected $content = null;
    
    /**
     * Short description of attribute source
     *
     * @access protected
     * @var string
     */
    protected $source = '';

    /**
     * Short description of attribute sourceType
     *
     * @access protected
     * @var int
     */
    protected $sourceType = 0;

    /**
     * Short description of attribute errors
     *
     * @access protected
     * @var array
     */
    protected $errors = array();

    /**
     * Short description of attribute valid
     *
     * @access protected
     * @var boolean
     */
    protected $valid = false;

    /**
     * Short description of attribute fileExtension
     *
     * @access protected
     * @var string
     */
    protected $fileExtension = 'xml';

    /**
     * Short description of attribute SOURCE_FILE
     *
     * @access public
     * @var int
     */

    const SOURCE_FILE = 1;

    /**
     * Short description of attribute SOURCE_URL
     *
     * @access public
     * @var int
     */
    const SOURCE_URL = 2;

    /**
     * Short description of attribute SOURCE_STRING
     *
     * @access public
     * @var int
     */
    const SOURCE_STRING = 3;

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string source
     * @param  array options
     * @return mixed
     */
    public function __construct($source, $options = array()){

        if(preg_match("/^<\?xml(.*)?/m", trim($source))){
            $this->sourceType = self::SOURCE_STRING;
        }else if(preg_match("/^http/", $source)){
            $this->sourceType = self::SOURCE_URL;
        }else if(is_file($source)){
            $this->sourceType = self::SOURCE_FILE;
        }else{
            throw new common_exception_Error("Denied content in the source parameter! ".get_class($this)." accepts either XML content, a URL to an XML Content or the path to a file but got ".substr($source, 0, 500));
        }
        $this->source = $source;

        if(isset($options['extension'])){
            $this->fileExtension = $options['extension'];
        }
    }
    
    public function getSource(){
        return $this->source;
    }

    /**
     * Short description of method validate
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string schema
     * @return boolean
     */
    public function validate($schema = '')
    {
        //You know sometimes you think you have enough time, but it is not always true ...
        //(timeout in hudson with the generis-hard test suite)
        helpers_TimeOutHelper::setTimeOutLimit(helpers_TimeOutHelper::MEDIUM);
        
        $content = $this->getContent();
        if (!empty($content)) {
            try {
                libxml_use_internal_errors(true);

                $dom = new DomDocument();
                $dom->formatOutput = true;
                $dom->preserveWhiteSpace = false;
                
                $this->valid = $dom->loadXML($content);

                if ($this->valid && !empty($schema)) {
                    $this->valid = $dom->schemaValidate($schema);
                }

                if (!$this->valid) {
                    $this->addErrors(libxml_get_errors());
                }
                libxml_clear_errors();
            } catch(DOMException $de) {
                $this->addError($de);
            }
        }
        
        
        helpers_TimeOutHelper::reset();
        return (bool) $this->valid;
    }
    
    /**
     * Excecute parser validation and stops at the first valid one, and returns the identified schema
     * 
     * @param array $xsds
     * @return string
     */
    public function validateMultiple($xsds = array())
    {
        $returnValue = '';

        foreach ($xsds as $xsd) {
            $this->errors = array();
            if ($this->validate($xsd)) {
                $returnValue = $xsd;
                break;
            }
        }

        return $returnValue;
    }
    
    /**
     * Short description of method isValid
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return boolean
     */
    public function isValid(){
        return (bool) $this->valid;
    }

    /**
     * Short description of method getErrors
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return array
     */
    public function getErrors(){
        $returnValue = $this->errors;
        return (array) $returnValue;
    }

    /**
     * Short description of method displayErrors
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  boolean htmlOutput
     * @return string
     */
    public function displayErrors($htmlOutput = true){

        $returnValue = (string) '';

        foreach($this->errors as $error){
            $returnValue .= $error['message'];
            if(isset($error['file']) && isset($error['line'])){
                $returnValue .= ' in file '.$error['file'].', line '.$error['line'];
            }
            $returnValue .= PHP_EOL;
        }

        if($htmlOutput){
            $returnValue = nl2br($returnValue);
        }

        return (string) $returnValue;
    }

    /**
     * Short description of method addError
     *
     * @access protected
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  mixed error
     * @return mixed
     */
    protected function addError($error){

        $this->valid = false;

        if($error instanceof Exception){
            $this->errors[] = array(
                'file' => $error->getFile(),
                'line' => $error->getLine(),
                'message' => "[".get_class($error)."] ".$error->getMessage()
            );
        }elseif($error instanceof LibXMLError){
            $this->errors[] = array(
                'file' => $error->file,
                'line' => $error->line,
                'message' => "[".get_class($error)."] ".$error->message
            );
        }elseif(is_string($error)){
            $this->errors[] = array(
                'message' => $error
            );
        }
    }
    
    /**
     * Get XML content.
     *
     * @access protected
     * @author Aleh Hutnikau, <hutnikau@1pt.com>
     * @param boolean $refresh load content again.
     * @return string
     */
    protected function getContent($refresh = false)
    {
        if ($this->content === null || $refresh) {
            try{
                switch ($this->sourceType) {
                    case self::SOURCE_FILE:
                        //check file
                        if(!file_exists($this->source)){
                            throw new Exception("File {$this->source} not found.");
                        }
                        if(!is_readable($this->source)){
                            throw new Exception("Unable to read file {$this->source}.");
                        }
                        if(!preg_match("/\.{$this->fileExtension}$/", basename($this->source))){
                            throw new Exception("Wrong file extension in ".basename($this->source).", {$this->fileExtension} extension is expected");
                        }
                        if(!tao_helpers_File::securityCheck($this->source)){
                            throw new Exception("{$this->source} seems to contain some security issues");
                        }
                        $this->content = file_get_contents($this->source);
                        break;
                    case self::SOURCE_URL:
                        //only same domain
                        if(!preg_match("/^".preg_quote(BASE_URL, '/')."/", $this->source)){
                            throw new Exception("The given uri must be in the domain {$_SERVER['HTTP_HOST']}");
                        }
                        $this->content = tao_helpers_Request::load($this->source, true);
                        break;
                    case self::SOURCE_STRING:
                        $this->content = $this->source;
                        break;
                }
            } catch(Exception $e) {
                $this->addError($e);
            }
        }
        
        return $this->content;
    }
    
    /**
     * Short description of method addErrors
     *
     * @access protected
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  array errors
     * @return mixed
     */
    protected function addErrors($errors){

        foreach($errors as $error){
            $this->addError($error);
        }
    }

    /**
     * Short description of method clearErrors
     *
     * @access protected
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return mixed
     */
    protected function clearErrors(){
        $this->errors = array();
    }

    /**
     * Creates a report without title of the parsing result
     * @return common_report_Report
     */
    public function getReport(){
        if($this->isValid()){
            return common_report_Report::createSuccess('');
        }else{
            $report = new common_report_Report('');
            foreach($this->getErrors() as $error){
                $report->add(common_report_Report::createFailure($error['message']));
            }
            return $report;
        }
    }

}