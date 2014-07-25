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

/**
 * Represents an action in the TAO main toolbar
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
class ToolbarAction implements PhpSerializable
{
    const SERIAL_VERSION = 1392821335;
    
    /**
     * The data serializable data of the action
     * @type array 
     */
    private $data;
    
    /**
     * Create an instance of ToolbarAction from an XML node
     * @param SimpleXMLELement $node the source data
     * @param string $extensionId the extension the action belongs to
     * @return ToolbarAction the instance
     */
    public static function fromSimpleXMLElement(\SimpleXMLElement $node, $extensionId) {
        $text = (string)$node;
        $data = array(
            'id'        => (string)$node['id'],
            'extension' => $extensionId,
            'title'		=> (string)$node['title'],
            'level'		=> (int)$node['level'],
            'text'      => empty($text) ? null : $text,
            'icon'      => isset($node['icon']) ? (string)$node['icon'] : null,
            'js'        => isset($node['js']) ? (string)$node['js'] : null,
            'structure' => isset($node['structure']) ? (string)$node['structure'] : null,
        );
        return new static($data);
    }  

    /**
     * Construct from a data array
     * @param array $data the action data
     */    
    public function __construct($data, $version = self::SERIAL_VERSION) {
        $this->data = $data;
    }
   
    /**
     * Get the action id
     * @return string the id
     */
    public function getId(){
        return $this->data['id'];
    } 

    /**
     * Get the extension the action belongs to
     * @return string the extension id
     */
    public function getExtension(){
        return $this->data['extension'];
    } 

    /**
     * Get the action title
     * @return string the title
     */
    public function getTitle(){
        return $this->data['title'];
    } 

    /**
     * Used for the display ordering
     * @return int the level
     */
    public function getLevel(){
        return $this->data['level'];
    }
 
    /**
     * Get the action text to display
     * @return string the text
     */
    public function getText(){
        return $this->data['text'];
    } 

    /**
     * Get the action icon class
     * @return string the icon class name
     */
    public function getIcon(){
        return $this->data['icon'];
    } 

    /**
     * Get the path of a JS controller to call once the user trigger the action
     * @return string the controller path/route
     */
    public function getJs(){
        return $this->data['js'];
    } 

    /**
     * Get the name of a structure the action redirect to. Not compatible with Js
     * @return string the structure id
     */
    public function getStructure(){
        return $this->data['structure'];
    } 

    /**
     * Expose the data
     * @return array the data
     */
    public function toArray(){
        return $this->data;
    }

    public function __toPhpCode() {
        return "new ".__CLASS__."("
            .\common_Utils::toPHPVariableString($this->data).','
            .\common_Utils::toPHPVariableString(self::SERIAL_VERSION)
        .")";
    }
}
