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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php

error_reporting(E_ALL);

/**
 * TAO - tao/helpers/form/elements/xhtml/class.Treeview.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 02.01.2012, 11:12:09 with ArgoUML PHP module
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package tao
 * @subpackage helpers_form_elements_xhtml
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include tao_helpers_form_elements_Treeview
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('tao/helpers/form/elements/class.Treeview.php');

/* user defined includes */
// section 127-0-1-1-1a593a5d:129ad6a35a4:-8000:000000000000204A-includes begin
// section 127-0-1-1-1a593a5d:129ad6a35a4:-8000:000000000000204A-includes end

/* user defined constants */
// section 127-0-1-1-1a593a5d:129ad6a35a4:-8000:000000000000204A-constants begin
// section 127-0-1-1-1a593a5d:129ad6a35a4:-8000:000000000000204A-constants end

/**
 * Short description of class tao_helpers_form_elements_xhtml_Treeview
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package tao
 * @subpackage helpers_form_elements_xhtml
 */
class tao_helpers_form_elements_xhtml_Treeview
    extends tao_helpers_form_elements_Treeview
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method feed
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function feed()
    {
        // section 127-0-1-1-7109ddcd:1344660e25c:-8000:0000000000003484 begin
		$expression = "/^" . preg_quote($this->name, "/") . "(.)*[0-9]+$/";
		$found = false;
		foreach ($_POST as $key => $value) {
			if (preg_match($expression, $key)) {
				$found = true;
				break;
			}
		}
		if ($found) {
			$this->setValues(array());
			foreach ($_POST as $key => $value) {
				if (preg_match($expression, $key)) {
					$this->addValue(tao_helpers_Uri::decode($value));
				}
			}
		}
        // section 127-0-1-1-7109ddcd:1344660e25c:-8000:0000000000003484 end
    }

    /**
     * Short description of method getOptions
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string format
     * @return array
     */
    public function getOptions($format = 'flat')
    {
        $returnValue = array();

        // section 127-0-1-1--65f085c2:129b27ea381:-8000:0000000000002202 begin

        switch($format){
        	case 'structured':
        		$returnValue = parent::getOptions();
        		break;
        	case 'flat':
        	default:
        		$returnValue = tao_helpers_form_GenerisFormFactory::extractTreeData(parent::getOptions());
        		break;
        }

        // section 127-0-1-1--65f085c2:129b27ea381:-8000:0000000000002202 end

        return (array) $returnValue;
    }

    /**
     * Short description of method setValue
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string value
     * @return mixed
     */
    public function setValue($value)
    {
        // section 127-0-1-1--65f085c2:129b27ea381:-8000:000000000000220B begin

    	$this->addValue($value);

        // section 127-0-1-1--65f085c2:129b27ea381:-8000:000000000000220B end
    }

    /**
     * Short description of method render
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function render()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-1a593a5d:129ad6a35a4:-8000:000000000000204C begin

        $widgetTreeName  = $this->name.'-TreeBox';
        $widgetValueName = $this->name.'-TreeValues';

        $returnValue .= "<label class='form_desc' for='{$this->name}'>". _dh($this->getDescription())."</label>";

        $returnValue .= "<div class='form-elt-container' style='min-height:50px; overflow-y:auto;'>";
        $returnValue .= "<div id='{$widgetValueName}'></div>";


		$returnValue .= "<div id='{$widgetTreeName}'></div>";

		//initialize the AsyncFileUpload Js component
		$returnValue .= '<script type="text/javascript">
			$(function(){
			 require([\'require\', \'jquery\', \'generis.tree.select\'], function(req, $, GenerisTreeSelectClass) {
				$("div[id=\''.$widgetTreeName.'\']").tree({
					data: {
						type : "json",
						async: false,
						opts : {static : ';
    	$returnValue .= json_encode($this->getOptions('structured'));
    	$returnValue .= '}
    				},
    				callback:{
	    				onload: function(TREE_OBJ) {
	    					checkedElements = '.json_encode($this->values).';
	    					$.each(checkedElements, function(i, elt){
								NODE = $("li[id=\'"+elt+"\']");
								if(NODE.length > 0){
									parent = TREE_OBJ.parent(NODE);
									TREE_OBJ.open_branch(parent);
									while(parent != -1){
										parent = TREE_OBJ.parent(parent);
										TREE_OBJ.open_branch(parent);
									}
									$.tree.plugins.checkbox.check(NODE);
								}
							});
	    				},
	    				onchange: function(NODE, TREE_OBJ){
	    					var valueContainer = $("div[id=\''.$widgetValueName.'\']");
	    					valueContainer.empty();
	    					$.each($.tree.plugins.checkbox.get_checked(TREE_OBJ), function(i, myNODE){
	    						valueContainer.append("<input type=\'hidden\' name=\''.$this->name.'_"+i+"\' value=\'"+$(myNODE).attr("id")+"\' />");
							});
	    				}
    				},
					types: {
					 "default" : {
							renameable	: false,
							deletable	: false,
							creatable	: false,
							draggable	: false
						}
					},
					ui: { theme_name : "checkbox" },
					plugins : { checkbox : { three_state : false} }
				});
			 });
			});
			</script>';
        $returnValue .= "</div><br />";

        // section 127-0-1-1-1a593a5d:129ad6a35a4:-8000:000000000000204C end

        return (string) $returnValue;
    }

    /**
     * Short description of method getEvaluatedValue
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function getEvaluatedValue()
    {
        // section 127-0-1-1--19ea91f3:1349db91b83:-8000:000000000000349C begin
    	$values = array_map("tao_helpers_Uri::decode", $this->getValues());
    	if(count($values) == 1){
			return $values[0];
    	} else{
    		return $values;
    	}
        // section 127-0-1-1--19ea91f3:1349db91b83:-8000:000000000000349C end
    }

} /* end of class tao_helpers_form_elements_xhtml_Treeview */

?>