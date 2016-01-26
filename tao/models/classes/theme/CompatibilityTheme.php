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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */
namespace oat\tao\model\theme;

use oat\tao\helpers\Template;
use oat\oatbox\Configurable;

/**
 * Backwards compatibilit Theme build
 * based on original array
 * 
 * @author bout
 */
class CompatibilityTheme extends DefaultTheme implements Theme
{

    public function getLabel()
    {
        $all = $this->getOptions();
        $first = reset($all);
        return $first['name'];
    }
    
    public function getTemplate($id, $context = Theme::CONTEXT_BACKOFFICE)
    {
        if ($this->hasOption($context)) {
            $arr = $this->getOption($context);
            $templates = $arr['templates'];
            if (isset($templates[$id])) {
                return ROOT_PATH.$templates[$id];
            } else {
                return parent::getTemplate($id, $context);
            }
        } else {
            return parent::getTemplate($id, $context);
        }
        
    }
    
    public function getStylesheet($context = Theme::CONTEXT_BACKOFFICE)
    {
        if ($this->hasOption($context)) {
            $arr = $this->getOption($context);
            return ROOT_URL.$arr['path'];
        } else {
            return parent::getStylesheet($context);
        }
    }
    
}
