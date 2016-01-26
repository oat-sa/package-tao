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
 * Copyright (c) (original work) 2015 Open Assessment Technologies SA
 *
 */

use oat\generis\model\data\Model;
use oat\oatbox\Configurable;
use oat\generis\model\data\ModelManager;

/**
 * transitory model for the smooth sql implementation
 * 
 * @author joel bout <joel@taotesting.com>
 * @package generis
 */
class core_kernel_persistence_smoothsql_SmoothModel extends Configurable
    implements Model
{
    const OPTION_PERSISTENCE = 'persistence';
    const OPTION_READABLE_MODELS = 'readable';
    const OPTION_WRITEABLE_MODELS = 'writeable';
    const OPTION_NEW_TRIPLE_MODEL = 'addTo';
    
    /**
     * Persistence to use for the smoothmodel
     * 
     * @var common_persistence_SqlPersistence
     */
    private $persistence;
    
    private static $readableSubModels = null;
    
    private static $updatableSubModels = null;
    
    public function getPersistence() {
        if (is_null($this->persistence)) {
            $this->persistence = common_persistence_SqlPersistence::getPersistence($this->getOption(self::OPTION_PERSISTENCE));
        }
        return $this->persistence;
    }

    /**
     * (non-PHPdoc)
     * @see \oat\generis\model\data\Model::getRdfInterface()
     */
    public function getRdfInterface() {
        return new core_kernel_persistence_smoothsql_SmoothRdf($this);
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\generis\model\data\Model::getRdfsInterface()
     */
    public function getRdfsInterface() {
        return new core_kernel_persistence_smoothsql_SmoothRdfs($this);
    }
    
    // Manage the sudmodels of the smooth mode
    
    /**
     * Returns the id of the model to add to
     * 
     * @return string
     */
    public function getNewTripleModelId() {
        return $this->getOption(self::OPTION_NEW_TRIPLE_MODEL);
    }
    
    public function getReadableModels() {
        return $this->getOption(self::OPTION_READABLE_MODELS);
    }

    public function getWritableModels() {
        return $this->getOption(self::OPTION_WRITEABLE_MODELS);
    }
    
    /**
     * Defines a model as readable
     *
     * @param string $id
     */
    public function addReadableModel($id) {
    
        common_Logger::i('ADDING MODEL '.$id);
    
        $readables = $this->getOption(self::OPTION_READABLE_MODELS);
        $this->setOption(self::OPTION_READABLE_MODELS, array_unique(array_merge($readables, array($id))));
    
        // update in persistence
        ModelManager::setModel($this);
    }
    
    //
    // Deprecated functions
    // 
    
    /**
     * Returns the submodel ids that are readable
     * 
     * @deprecated
     * @return array()
     */
    public static function getReadableModelIds() {
        $model = ModelManager::getModel();
        if (!$model instanceof self) {
            throw new common_exception_Error(__FUNCTION__.' called on '.get_class($model).' model implementation');
        }
        return $model->getReadableModels();
    }
    
    /**
     * Returns the submodel ids that are updatable
     * 
     * @deprecated
     * @return array()
     */
    public static function getUpdatableModelIds() {
        $model = ModelManager::getModel();
        if (!$model instanceof self) {
            throw new common_exception_Error(__FUNCTION__.' called on '.get_class($model).' model implementation');
        }
        return $model->getWritableModels();
    }
    
    /**
     * For hardification we need to ba able to bypass the model restriction
     *
     * @deprecated
     * @param array $ids
     */
    public static function forceUpdatableModelIds($ids)
    {
        throw new common_exception_Error(__FUNCTION__.' no longer supported');
    }
    
    /**
     * @deprecated
     */
    public static function forceReloadModelIds() {
        common_Logger::w('Call to deprecated '.__FUNCTION__.' no longer does anything');
    }

}