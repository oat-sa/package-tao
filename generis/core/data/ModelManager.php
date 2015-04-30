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
 * Copyright (c) 2002-2008 (original work) 2014 Open Assessment Technologies SA
 * 
 */

namespace oat\generis\model\data;

/**
 * transitory class to manage the ontology driver
 * instead of managing full models, it only handles the rdfs interfaces
 * 
 * @author Joel Bout <joel@taotesting.com>
 */
class ModelManager
{
    const CONFIG_KEY = 'ontology';
    
    private static $model = null;
    
    /**
     * @return Model
     */
    public static function getModel() {
        if (is_null(self::$model)) {
            $array = \common_ext_ExtensionsManager::singleton()->getExtensionById('generis')->getConfig(self::CONFIG_KEY);
            if (is_array($array)) {
                self::$model = self::array2model($array);
            } else {
                throw new \common_exception_InconsistentData('No data model found');
            }
        }
        return self::$model;
    }
    
    /**
     * @param core_kernel_persistence_RdfsDriver $model
     */
    public static function setModel(Model $model) {
        self::$model = $model;
        \common_ext_ExtensionsManager::singleton()->getExtensionById('generis')->setConfig(self::CONFIG_KEY, self::model2array($model));
    }
    
    protected static function model2array(Model $model) {
        $className = get_class($model);
        return array(
        	'class' => $className,
            'config' => $model->getOptions()
        );
    }
    
    protected static function array2model($array) {
        if (!isset($array['class']) || !isset($array['config']) || !is_array($array['config'])) {
            throw new \common_exception_Error('Illegal model array');
        }
        $className = $array['class'];
        return new $className($array['config']);
    }
}