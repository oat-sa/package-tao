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
 * Copyright (c) 2013 (original work) Open Assessment Techonologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */

/**
 * Short description of class taoQTI_models_classes_QTI_response_Template
 *
 * @access public
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package taoQTI
 * @subpackage models_classes_QTI_response
 */
class taoQTI_models_classes_QTI_response_Template extends taoQTI_models_classes_QTI_response_ResponseProcessing implements taoQTI_models_classes_QTI_response_Rule
{
    /**
     * Short description of attribute MATCH_CORRECT
     *
     * @access public
     * @var string
     */

    const MATCH_CORRECT = 'http://www.imsglobal.org/question/qti_v2p1/rptemplates/match_correct';

    /**
     * Short description of attribute MAP_RESPONSE
     *
     * @access public
     * @var string
     */
    const MAP_RESPONSE = 'http://www.imsglobal.org/question/qti_v2p1/rptemplates/map_response';

    /**
     * Short description of attribute MAP_RESPONSE_POINT
     *
     * @access public
     * @var string
     */
    const MAP_RESPONSE_POINT = 'http://www.imsglobal.org/question/qti_v2p1/rptemplates/map_response_point';

    /**
     * Short description of attribute MATCH_CORRECT
     *
     * @access public
     * @var string
     */
    const MATCH_CORRECT_qtiv2p0 = 'http://www.imsglobal.org/question/qti_v2p0/rptemplates/match_correct';

    /**
     * Short description of attribute MAP_RESPONSE
     *
     * @access public
     * @var string
     */
    const MAP_RESPONSE_qtiv2p0 = 'http://www.imsglobal.org/question/qti_v2p0/rptemplates/map_response';

    /**
     * Short description of attribute MAP_RESPONSE_POINT
     *
     * @access public
     * @var string
     */
    const MAP_RESPONSE_POINT_qtiv2p0 = 'http://www.imsglobal.org/question/qti_v2p0/rptemplates/map_response_point';

    /**
     * Short description of attribute uri
     *
     * @access protected
     * @var string
     */
    protected $uri = '';

    /**
     * Short description of attribute file
     *
     * @access protected
     * @var string
     */
    protected $file = '';

    /**
     * Short description of method getRule
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return string
     */
    public function getRule(){
        $returnValue = (string) '';

        // section 127-0-1-1-3397f61e:12c15e8566c:-8000:0000000000002AFF begin
        if($this->uri == self::MATCH_CORRECT){
            $returnValue = taoQTI_models_classes_Matching_Matching::MATCH_CORRECT;
        }else if($this->uri == self::MAP_RESPONSE){
            $returnValue = taoQTI_models_classes_Matching_Matching::MAP_RESPONSE;
        }else if($this->uri == self::MAP_RESPONSE_POINT){
            $returnValue = taoQTI_models_classes_Matching_Matching::MAP_RESPONSE_POINT;
        }

        // section 127-0-1-1-3397f61e:12c15e8566c:-8000:0000000000002AFF end

        return (string) $returnValue;
    }

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string uri
     * @return mixed
     */
    public function __construct($uri){
        //automatically transform to qti 2.1 templates:
        switch($uri){
            case self::MATCH_CORRECT_qtiv2p0:
                $uri = self::MATCH_CORRECT;
                break;
            case self::MAP_RESPONSE_qtiv2p0:
                $uri = self::MAP_RESPONSE;
                break;
            case self::MAP_RESPONSE_POINT_qtiv2p0:
                $uri = self::MAP_RESPONSE_POINT;
                break;
        }

        if($uri != self::MATCH_CORRECT &&
                $uri != self::MAP_RESPONSE &&
                $uri != self::MAP_RESPONSE_POINT){
            throw new common_Exception("Unknown response processing template '$uri'");
        }
        $this->uri = $uri;

        $this->file = ROOT_PATH.'/taoQTI/models/classes/QTI/data/qtiv2p1/rptemplates/'.basename($this->uri).'.xml';
        if(!file_exists($this->file)){
            throw new Exception("Unable to load response processing template {$this->uri} in {$this->file}");
        }

        parent::__construct();
    }

    /**
     * Short description of method toQTI
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return string
     */
    public function toQTI(){
        $tplRenderer = new taoItems_models_classes_TemplateRenderer(
                static::getTemplatePath().'/qti.rptemplate.tpl.php', array('uri' => $this->uri)
        );

        $returnValue = $tplRenderer->render();

        return (string) $returnValue;
    }

    /**
     * Short description of method getUri
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return string
     */
    public function getUri(){
        return (string) $this->uri;
    }

    public function toArray(){
        return array();
    }

    protected function getUsedAttributes(){
        return array();
    }

}