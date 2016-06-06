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

interface Theme
{
    const CONTEXT_BACKOFFICE = 'backOffice';
    
    const CONTEXT_FRONTOFFICE = 'frontOffice';

    /**
     * Returns a human readable title of the theme
     * @return string
     */
    public function getLabel();
    
    /**
     * Returns the path to the template file on the fs
     * that is referenced by id and context
     * 
     * @param string $id
     * @param string $context
     * @return string filepath
     */
    public function getTemplate($id, $context = self::CONTEXT_BACKOFFICE);
    
    /**
     * Returns the url to the StyleSheet for the indicated context
     * 
     * @param string $context
     * @return string url
     */
    public function getStylesheet($context = self::CONTEXT_BACKOFFICE);
}
