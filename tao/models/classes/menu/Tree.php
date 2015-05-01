<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
i *
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

class Tree  implements PhpSerializable
{
    const SERIAL_VERSION = 1392821334;
    
    private $data = array();
    
    public static function fromSimpleXMLElement(\SimpleXMLElement $node) {
        $data = array();
        foreach($node->attributes() as $attrName => $attrValue) {
            $data[$attrName] = (string)$attrValue;
        }
        return new static($data);
    }
    
    public function __construct($data, $version = self::SERIAL_VERSION) {
        $this->data = $data;
    }
    
    public function get($attribute) {
        return isset($this->data[$attribute]) ? $this->data[$attribute] : null;
    }
 
    public function getAttributes(){
        return array_keys($this->data);
    }
    
    public function getActions()
    {
        $actions = array();
        foreach ($this->data as $key => $value) {
            if (!in_array($key, array('rootNode', 'dataUrl', 'className', 'name'))) {
                $actions[$key] = $value;
            }
        }
        return $actions;
    }
   
    public function getName() {
        return $this->data['name'];
    }
    
    public function __toPhpCode() {
        return "new ".__CLASS__."("
            .\common_Utils::toPHPVariableString($this->data).','
            .\common_Utils::toPHPVariableString(self::SERIAL_VERSION)
        .")";
    }
}
