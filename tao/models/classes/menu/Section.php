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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA;
 *
 *
 */

namespace oat\tao\model\menu;

use oat\oatbox\PhpSerializable;

class Section extends MenuElement implements PhpSerializable
{

    const SERIAL_VERSION = 1392821334;
    
    const POLICY_MERGE = 'merge';
    
    const POLICY_OVERRIDE = 'override';

    private $data = array();

    private $trees = array();

    private $actions = array();

    public static function fromSimpleXMLElement(\SimpleXMLElement $node)
    {

		$url = isset($node['url']) ? (string) $node['url'] : '#';
		if ($url == '#' || empty($url)) {
			$extension  = null;
			$controller = null;
			$action     = null;
		} else {
			list($extension, $controller, $action) = explode('/', trim($url, '/'));
		}

        $data = array(
            'id'         => (string)$node['id'],
            'name'       => (string)$node['name'],
            'url'        => $url,
			'extension'  => $extension,
			'controller' => $controller,
			'action'     => $action,
            'binding'    => isset($node['binding']) ? (string)$node['binding'] : null,
            'policy'     => isset($node['policy']) ? (string)$node['policy'] : self::POLICY_MERGE,
            'disabled'   => isset($node['disabled']) ? true : false
        );

        $trees = array();
        foreach ($node->xpath("trees/tree") as $treeNode) {
            $trees[] = Tree::fromSimpleXMLElement($treeNode);
        }

        $actions = array();
        foreach ($node->xpath("actions/action") as $actionNode) {
            $actions[] = Action::fromSimpleXMLElement($actionNode);
        }

        return new static($data, $trees, $actions);
    }

    public function __construct($data, $trees, $actions, $version = self::SERIAL_VERSION)
    {
        parent::__construct($data['id'], $version);
        $this->data    = $data;
        $this->trees   = $trees;
        $this->actions = $actions;

        $this->migrateDataFromLegacyFormat();
    }

    public function getUrl()
    {
        return _url($this->getAction(), $this->getController(), $this->getExtensionId());
    }

    public function getName()
    {
        return $this->data['name'];
    }

    public function getExtensionId()
	{
		return $this->data['extension'];
    }

	public function getController()
	{
		return $this->data['controller'];
	}

	public function getAction()
	{
		return $this->data['action'];
	}

    /**
     * Policy on how to deal with existing structures
     * 
     * Only merge or override are currently supported
     * 
     * @return string
     */
    public function getPolicy()
    {
        return $this->data['policy'];
    }
  
    /**
     * Get the JavaScript binding to run instead of loading the URL
     *
     * @return string|null the binding name or null if none
     */ 
    public function getBinding()
    {
        return $this->data['binding'];
    }
 
    /**
     * Is the section disabled ?
     *
     * @return boolean if the section is disabled
     */ 
    public function getDisabled()
    {
        return $this->data['disabled'];
    }

    public function getTrees()
    {
        return $this->trees;
    }

    public function addTree(Tree $tree)
    {
        $this->trees[] = $tree;
    }

    public function getActions()
    {
        return $this->actions;
    }

    public function addAction(Action $action)
    {
        $this->actions[] = $action;
    }

    public function removeAction(Action $action)
    {
        $index = array_search($action, $this->actions, true);
        if($index !== false) {
            unset($this->actions[$index]);
        }
    }

    /**
     * @param string $groupId
     * @return array
     */
    public function getActionsByGroup($groupId)
    {
        $actions = array();
        foreach ($this->getActions() as $action) {
            if ($action->getGroup() === $groupId) {
                $actions[] = $action;
            }
        };
        return $actions;
    }

    /**
     * Enables sections to be backward compatible with the structure format 
     * (before some actions were missing as they were defined in the tree part).
     * legacy format : tao <= 2.6.x
     * this method should be deprecated from 2.8.0 / 3.0.0
     */
    private function migrateDataFromLegacyFormat(){

        if(count($this->trees) > 0){

            //tree attributes to be migrated.
           $mapping = array(
              'editClassUrl' => array(
                'attr'   => 'selectClass',
                'action' => array(
                    'id'      => 'edit_class',
                    'name'    => 'edit class',
                    'group'   => 'none',
                    'context' => 'class',
                    'binding' => 'load'
                ) 
              ),
              'editInstanceUrl' => array(
                'attr'   => 'selectInstance',
                'action' => array(
                    'id'      => 'edit_instance',
                    'name'    => 'edit instance',
                    'group'   => 'none',
                    'context' => 'instance',
                    'binding' => 'load'
                ) 
              ),
              'addInstanceUrl'  => array(
                'attr'   => 'addInstance',
                'action' => array(
                    'id'      => 'add_instance',
                    'name'    => 'add instance',
                    'group'   => 'none',
                    'context' => 'instance',
                    'binding' => 'instantiate'
                ) 
              ),
              'addSubClassUrl'  => array(
                'attr'   => 'addClass',
                'action' => array(
                    'id'      => 'add_class',
                    'name'    => 'add class',
                    'group'   => 'none',
                    'context' => 'class',
                    'binding' => 'subClass'
                ) 
              ),
              'deleteUrl' => array(
                'attr'   => 'addClass',
                'action' => array(
                    'id'      => 'add_class',
                    'name'    => 'add class',
                    'group'   => 'none',
                    'context' => 'class',
                    'binding' => 'subClass'
                ) 
              ),
              'moveInstanceUrl' => array(
                'attr'   => 'moveInstance',
                'action' => array(
                    'id'      => 'move',
                    'name'    => 'move',
                    'group'   => 'none',
                    'context' => 'instance',
                    'binding' => 'moveNode'
                ) 
              )
           );

           foreach($this->trees as $index => $tree){
               $needMigration = false;
               $treeAttributes = $tree->getAttributes();
            
               //check if this attribute needs a migration
               foreach($treeAttributes as $attr){
                    if(array_key_exists($attr, $mapping)){
                        $needMigration = true;
                        break;
                    } 
               }
               if($needMigration){
                    $newData = array();

                    //migrate the tree
                    foreach($treeAttributes as $attr){
                        //if the attribute belongs to the mapping
                        if(array_key_exists($attr, $mapping)){
                            $url = $tree->get($attr);
                            $actionName = false;
    
                            //try to find an action with the same url
                            foreach($this->actions as $action){
                                if($action->getRelativeUrl() == $url){
                                    $actionName = $action->getId();
                                    break;
                                }
                            }
                            if($actionName){
                                $newData[$mapping[$attr]['attr']] = $actionName;
                            } else {
    
                                //otherwise create a new action from the mapping
                                $newData[$mapping[$attr]['attr']] = $mapping[$attr]['action']['id'];
                                $actionData = $mapping[$attr]['action'];
                                $actionData['url'] = $url;
                                
                                list($extension, $controller, $action) = explode('/', trim($url, '/'));
                                $actionData['extension'] = $extension;
                                $actionData['controller'] = $controller;
                                $actionData['action'] = $action;
                                
                                $this->actions[] = new Action($actionData); 
                            }
                        } else {
                            $newData[$attr] = $tree->get($attr);
                        }
                    }
    
                    //the tree is replaced
                    $this->trees[$index] = new Tree($newData);
               }
           }
        }
    }


    public function __toPhpCode()
    {
        return "new " . __CLASS__ . "("
        . \common_Utils::toPHPVariableString($this->data) . ','
        . \common_Utils::toPHPVariableString($this->trees) . ','
        . \common_Utils::toPHPVariableString($this->actions) . ','
        . \common_Utils::toPHPVariableString(self::SERIAL_VERSION)
        . ")";
    }
}
