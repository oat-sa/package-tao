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
namespace oat\taoWorkspace\model\generis;

use oat\generis\model\data\Model;
use common_Logger;
use \common_exception_MissingParameter;
use \common_exception_Error;
use oat\generis\model\data\ModelManager;
use oat\oatbox\service\ConfigurableService;

/**
 * transitory model for the smooth sql implementation
 * 
 * @author joel bout <joel@taotesting.com>
 * @package generis
 */
class WrapperModel extends ConfigurableService
    implements Model
{
    
    static public function wrap(Model $original, Model $workspace) {
        return new self(array('inner' => $original, 'workspace' => $workspace));
    }
    
    /**
     * @var oat\generis\model\data\RdfInterface
     */
    private $rdf;
    
    /**
     * @var oat\generis\model\data\RdfsInterface
     */
    private $rdfs;
    
    /**
     * Constructor of the smooth model, expects a persistence in the configuration
     * 
     * @param array $configuration
     * @throws common_exception_MissingParameter
     */
    public function __construct($options = array()) {
        if (!isset($options['inner'])) {
            throw new common_exception_MissingParameter('inner', __CLASS__);
        }
        parent::__construct($options);
        
        $inner = $this->getInnerModel();
        
        $this->rdf = new WrapperRdf($inner->getRdfInterface(), $this);
        $this->rdfs = new WrapperRdfs($inner->getRdfsInterface(), $this->getWorkspaceModel()->getRdfsInterface());
    }
    
    /**
     * @return Model
     */
    public function getInnerModel()
    {
        return $this->getOption('inner');
    }
    
    public function getWorkspaceModel()
    {
        return $this->getOption('workspace');
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\generis\model\data\Model::getRdfInterface()
     */
    public function getRdfInterface()
    {
        return $this->rdf;
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\generis\model\data\Model::getRdfsInterface()
     */
    public function getRdfsInterface()
    {
        return $this->rdfs;
    }
    
    public function getReadableModels()
    {
        return $this->getInnerModel()->getReadableModels();
    }

    public function addReadableModel($modelId)
    {
        common_Logger::i('Adding model '.$modelId.' via wrapper');
        $this->getInnerModel()->addReadableModel($modelId);
        
        // update in persistence
        ModelManager::setModel($this);
    }
}