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
 * Short description of class tao_helpers_form_elements_xhtml_Treeview
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package tao
 
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

        

        switch($format){
        	case 'structured':
        		$returnValue = parent::getOptions();
        		break;
        	case 'flat':
        	default:
        		$returnValue = tao_helpers_form_GenerisFormFactory::extractTreeData(parent::getOptions());
        		break;
        }

        

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
        

    	$this->addValue($value);

        
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
                            var tree = $("#'. $widgetTreeName.'");
	    					$.each(checkedElements, function(i, elt){
								NODE = $("li[id=\'"+elt+"\']", tree);
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
        
    	$values = array_map("tao_helpers_Uri::decode", $this->getValues());
    	if(count($values) == 1){
			return $values[0];
    	} else{
    		return $values;
    	}
        
    }

}

?>