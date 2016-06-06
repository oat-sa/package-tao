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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA
 *               
 */

namespace oat\taoQtiItem\model\qti;

use oat\taoQtiItem\model\qti\Element;
use oat\taoQtiItem\model\qti\container\FlowContainer;
use oat\taoQtiItem\model\qti\container\ContainerStatic;

/**
 * XInclude model of XInclude element, the href identified the location of the xml body string
 * and the body represents the loaded content from the href.
 *
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package taoQtiItem
 
 */
class XInclude extends Element implements FlowContainer
{

    /**
     * the QTI tag name as defined in QTI standard
     *
     * @access protected
     * @var string
     */
    protected static $qtiTagName = 'include';
    protected $body = null;
    
    public function __construct($attributes = array(), Item $relatedItem = null, $serial = ''){
        parent::__construct($attributes, $relatedItem, $serial);
        $this->body = new ContainerStatic('', $relatedItem);
    }
    
    /**
     * Get the body of XInclude element
     * 
     * @return oat\taoQtiItem\model\qti\container\ContainerStatic
     */
    public function getBody(){
        return $this->body;
    }
    
    /**
     * Get the list of used attributes
     * 
     * @return array
     */
    public function getUsedAttributes(){
        return array();
    }
    
    /**
     * Get the variables for the qti template rendering
     * 
     * @return array
     */
    protected function getTemplateQtiVariables(){

        $variables = parent::getTemplateQtiVariables();

        $tag = static::$qtiTagName;

        //search existing mathML ns declaration:
        $ns = $this->getXIncludeNamespace();
        if(empty($ns)){
            //add one!
            $relatedItem = $this->getRelatedItem();
            if(!is_null($relatedItem)){
                $ns = 'xi';
                $relatedItem->addNamespace($ns, 'http://www.w3.org/2001/XInclude');
            }
        }
        if(!empty($ns)){
            //proceed to ns addition:
            $tag = $ns.':'.$tag;
        }

        $variables['tag'] = $tag;

        return $variables;
    }
    
    /**
     * Get the xml namespace of the xinclude
     * 
     * @return string
     */
    public function getXIncludeNamespace(){
        $ns = '';
        $relatedItem = $this->getRelatedItem();
        if(!is_null($relatedItem)){
            foreach($relatedItem->getNamespaces() as $name => $uri){
                if(strpos($uri, 'XInclude') > 0){
                    $ns = $name;
                    break;
                }
            }
        }
        return $ns;
    }
    
    /**
     * Get the absolute path of the template of the qti.xml
     * 
     * @return string
     */
    public static function getTemplateQti(){
       return static::getTemplatePath().'/qti.include.tpl.php';
    }
}