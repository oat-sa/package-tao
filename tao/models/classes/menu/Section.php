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

class Section implements PhpSerializable
{
    const SERIAL_VERSION = 1392821334;
    
    private $data = array();
    
    private $trees = array();
    
    private $actions = array();
    
    public static function fromSimpleXMLElement(\SimpleXMLElement $node) {
        $data = array(
            'id'  => (string)$node['id'],
            'name'  => (string)$node['name'],
            'url' => (string)$node['url']
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
    public function __construct($data, $trees, $actions, $version = self::SERIAL_VERSION) {
        $this->data = $data;
        $this->trees = $trees;
        $this->actions = $actions;
    }
    
    public function getId() {
        return $this->data['id'];
    }
    
    public function getUrl() {
        return $this->data['url'];
    }
    
    public function getName() {
        return $this->data['name'];
    }
    
    public function getTrees() {
        return $this->trees;
    }
    
    public function getActions() {
        return $this->actions;
    }
    
    public function __toPhpCode() {
        return "new ".__CLASS__."("
            .\common_Utils::toPHPVariableString($this->data).','
            .\common_Utils::toPHPVariableString($this->trees).','
            .\common_Utils::toPHPVariableString($this->actions).','
            .\common_Utils::toPHPVariableString(self::SERIAL_VERSION)
        .")";
    }
}