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

class Perspective implements PhpSerializable
{
    const SERIAL_VERSION = 1392821334;
    
    private $data = array();
    
    private $sections = array();
    
    public static function fromSimpleXMLElement(\SimpleXMLElement $node, $extensionId) {
        $data = array(
            'id' => (string) $node['id'],
            'visible' => $node['visible'] == 'true',
            'name' => (string) $node['name'],
            'description' => (string) $node->description,
            'extension' => $extensionId,
            'level' => (string) $node['level'],
        );
        $sections = array();
        foreach ($node->xpath("sections/section") as $sectionNode) {
            $sections[] = Section::fromSimpleXMLElement($sectionNode);
        }
        return new static($data, $sections);
    }
    
    public function __construct($data, $sections, $version = self::SERIAL_VERSION) {
        $this->data = $data;
        $this->sections = $sections;
    }
    
    public function addSection(Section $section) {
        $existingKey = false;
        foreach ($this->sections as $key => $existingSection) {
            if ($existingSection->getId() == $section->getId()) {
                $existingKey = $key;
                break;
            }
        }
        if ($existingKey !== false) {
            $this->sections[$existingKey] = $section;
        } else {
            $this->sections[] = $section;
        }
    }
    
    public function getId() {
        return $this->data['id'];
    }
    
    public function getExtension() {
        return $this->data['extension'];
    }

    public function getName() {
        return $this->data['name'];
    }
    
    public function getDescription() {
        return $this->data['description'];
    }
    
    public function getLevel() {
        return $this->data['level'];
    }

    public function isVisible() {
        return $this->data['visible'];
    }
    
    public function getSections() {
        return $this->sections;
    }
    
    public function __toPhpCode() {
        return "new ".__CLASS__."("
            .\common_Utils::toPHPVariableString($this->data).','
            .\common_Utils::toPHPVariableString($this->sections).','
            .\common_Utils::toPHPVariableString(self::SERIAL_VERSION)
        .")";
    }
}