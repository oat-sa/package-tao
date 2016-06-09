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
 * 
 */

namespace oat\tao\helpers\form;

use common_cache_FileCache;
use common_cache_NotFoundException;
use common_Logger;
use core_kernel_classes_Class;
use core_kernel_classes_Property;

/**
 * The FormFactory enable you to create ready-to-use instances of the Form
 * It helps you to get the commonly used instances for the default rendering
 * etc.
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 
 */
class WidgetRegistry
{
    const CACHE_KEY = 'tao_widget_data';

    /**
     * Cache of widget informations
     * @var array
     */
    private static $widgetCache = null;
    
    public static function getWidgetDefinitionById($id) {
        $widgets = self::getWidgetDefinitions();
        foreach ($widgets as $widgetDefinition) {
            if ($widgetDefinition['id'] == strtolower($id)) {
                return $widgetDefinition;
            }
            
        }
        // Many widgets don't have a proper definition, don't throw any error
        // common_Logger::w('Widget with id "'.strtolower($id).'" not found');
        return null;
    }
    
    public static function getWidgetDefinition(\core_kernel_classes_Resource $widget) {
        $widgets = self::getWidgetDefinitions();
        if (isset($widgets[$widget->getUri()])) {
            return $widgets[$widget->getUri()];
        } else {
            common_Logger::w('Widget "'.$widget->getUri().'" not found');
            return null;
        }
    }
    
    protected static function getWidgetDefinitions() {
        if (is_null(self::$widgetCache)) {
            try {
                self::$widgetCache = common_cache_FileCache::singleton()->get(self::CACHE_KEY);
            } catch (common_cache_NotFoundException $e) {
                // not in cache need to load
                self::$widgetCache = self::getWidgetsFromOntology();
                common_cache_FileCache::singleton()->put(self::$widgetCache, self::CACHE_KEY);
            }
        }
        return self::$widgetCache;
    }

    /**
     * @return array
     */
    private static function getWidgetsFromOntology() {
        $class = new core_kernel_classes_Class(CLASS_WIDGET);
        $rendererClass = new core_kernel_classes_Class(CLASS_WIDGETRENDERER);
        $widgets = array();
        foreach ($class->getInstances(true) as $widgetResource) {
            $id = $widgetResource->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_WIDGET_ID));
            if (!is_null($id)) {
                $renderers = $rendererClass->searchInstances(array(
                	PROPERTY_WIDGETRENDERER_WIDGET => $widgetResource->getUri()
                ), array('like' => false));
                $rendererClasses = array();
                foreach ($renderers as $renderer) {
                    $props = $renderer->getPropertiesValues(array(
                        PROPERTY_WIDGETRENDERER_MODE,PROPERTY_WIDGETRENDERER_IMPLEMENTATION
                    ));
                    if (count($props[PROPERTY_WIDGETRENDERER_MODE]) == 1 && count($props[PROPERTY_WIDGETRENDERER_IMPLEMENTATION])) {
                        $mode = (string)reset($props[PROPERTY_WIDGETRENDERER_MODE]);
                        $class = (string)reset($props[PROPERTY_WIDGETRENDERER_IMPLEMENTATION]);
                        $rendererClasses[$mode] = $class;
                    } else {
                        common_Logger::w('Definition of $widget renderer.'.$renderer->getUri().') invalid');
                    }
                }
                $widgets[$widgetResource->getUri()] = array(
                    'id' => strtolower($id),
                    'renderers' => $rendererClasses
                );
            } else {
                // deprecated widget
                $id = substr($widgetResource->getUri(), strpos($widgetResource->getUri(), '#')+1);
                $className = "tao_helpers_form_elements_xhtml_".ucfirst(strtolower($id));
                $widgets[$widgetResource->getUri()] = array(
                    'id' => strtolower($id),
                    'renderers' => array('xhtml' => $className)
                );
            }
        }
        return $widgets;
    }

}