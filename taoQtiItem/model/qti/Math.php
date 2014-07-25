<?php
/*
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; under version 2 of the License (non-upgradable). This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details. You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA. Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */

namespace oat\taoQtiItem\model\qti;

/**
 *
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package taoQTI
 
 */
class Math extends Element
{

    /**
     * the QTI tag name as defined in QTI standard
     *
     * @access protected
     * @var string
     */
    protected static $qtiTagName = 'math';
    protected $mathML = '';
    protected $annotations = array();

    public function setMathML($mathML){
        $ns = $this->getMathNamespace();
        //strip the outer math tags, to only store the body
        $mathML = preg_replace('/<(\/)?'.($ns ? $ns.':' : '').'math/is', '', $mathML);
        if($ns){
            //strip ns usage, to store raw mathML
            $mathML = preg_replace('/<(\/)?/is', '<$1', $mathML);
        }
        $this->mathML = $mathML;
    }

    public function getMathML(){
        return $this->mathML;
    }

    protected function getUsedAttributes(){
        return array();
    }

    protected function getTemplateQtiVariables(){

        $variables = parent::getTemplateQtiVariables();

        $tag = static::$qtiTagName;
        $body = $this->mathML;

        //render annotation:
        $annotations = '';
        foreach($this->annotations as $encoding => $value){
            $annotations .= '<annotation encoding="'.$encoding.'">'.$value.'</annotation>';
        }

        if(!empty($annotations)){
            if(strpos($body, '</semantics>')){
                $body = str_replace('</semantics>', $annotations.'</semantics>', $body);
            }else{
                $body = '<semantics>'.$body.$annotations.'</semantics>';
            }
        }

        //search existing mathML ns declaration:
        $ns = $this->getMathNamespace();
        if(empty($ns)){
            //add one!
            $relatedItem = $this->getRelatedItem();
            if(!is_null($relatedItem)){
                $ns = 'm';
                $relatedItem->addNamespace($ns, 'http://www.w3.org/1998/Math/MathML');
            }
        }
        if(!empty($ns)){
            //proceed to ns addition:
            $body = preg_replace('/<(\/)?([^!])/', '<$1'.$ns.':$2', $body);
            $tag = $ns.':'.$tag;
        }


        $variables['tag'] = $tag;
        $variables['body'] = $body;

        return $variables;
    }

    public function getMathNamespace(){
        $ns = '';
        $relatedItem = $this->getRelatedItem();
        if(!is_null($relatedItem)){
            foreach($relatedItem->getNamespaces() as $name => $uri){
                if(strpos($uri, 'MathML') > 0){
                    $ns = $name;
                    break;
                }
            }
        }
        return $ns;
    }

    public function toArray($filterVariableContent = false, &$filtered = array()){
        $data = parent::toArray($filterVariableContent, $filtered);
        $data['mathML'] = $this->mathML;
        $data['annotations'] = $this->annotations;
        return $data;
    }

    public function getAnnotations(){
        return $this->annotations;
    }

    public function setAnnotations($annotations){
        $this->annotations = $annotations;
    }

    public function setAnnotation($encoding, $value){
        $this->annotations[$encoding] = $value;
    }

    public function removeAnnotation($encoding){
        unset($this->annotations[$encoding]);
    }

    public function getAnnotation($encoding){
        return isset($this->annotations[$encoding]) ? $this->annotations[$encoding] : '';
    }

    public function toForm(){
        $formContainer = new Math($this);
        return $formContainer->getForm();
    }

}