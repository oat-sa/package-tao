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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA
 *
 */

use oat\generis\model\data\RdfInterface;
use oat\oatbox\service\ServiceManager;
use oat\oatbox\event\EventManager;
use oat\generis\model\data\event\ResourceCreated;

/**
 * Implementation of the RDF interface for the smooth sql driver
 * 
 * @author joel bout <joel@taotesting.com>
 * @package generis
 */
class core_kernel_persistence_smoothsql_SmoothRdf
    implements RdfInterface
{
    /**
     * @var core_kernel_persistence_smoothsql_SmoothModel
     */
    private $model;
    
    public function __construct(core_kernel_persistence_smoothsql_SmoothModel $model) {
        $this->model = $model;
    }
    
    protected function getPersistence() {
        return $this->model->getPersistence();
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\generis\model\data\RdfInterface::get()
     */
    public function get($subject, $predicate) {
        throw new \common_Exception('Not implemented');
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\generis\model\data\RdfInterface::add()
     */
    public function add(\core_kernel_classes_Triple $triple) {
        if (!in_array($triple->modelid, $this->model->getReadableModels())) {
            $this->model->addReadableModel($triple->modelid);
        }
        $query = "INSERT INTO statements ( modelId, subject, predicate, object, l_language) VALUES ( ? , ? , ? , ? , ? );";
        $success = $this->getPersistence()->exec($query, array($triple->modelid, $triple->subject, $triple->predicate, $triple->object, is_null($triple->lg) ? '' : $triple->lg));
        if ($triple->predicate == RDFS_SUBCLASSOF || $triple->predicate == RDF_TYPE) {
            $eventManager = $this->getServiceManager()->get(EventManager::CONFIG_ID);
            $eventManager->trigger(new ResourceCreated(new core_kernel_classes_Resource($triple->subject)));
        }
        return $success;
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\generis\model\data\RdfInterface::remove()
     */
    public function remove(\core_kernel_classes_Triple $triple) {
        $query = "DELETE FROM statements WHERE subject = ? AND predicate = ? AND object = ? AND l_language = ?;";
        return $this->getPersistence()->exec($query, array($triple->subject, $triple->predicate, $triple->object, is_null($triple->lg) ? '' : $triple->lg));
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\generis\model\data\RdfInterface::search()
     */
    public function search($predicate, $object) {
        throw new \common_Exception('Not implemented');
    }
    
    public function getIterator() {
        return new core_kernel_persistence_smoothsql_SmoothIterator($this->getPersistence());
    }
    
    public function getServiceManager()
    {
        return ServiceManager::getServiceManager();
    }
}