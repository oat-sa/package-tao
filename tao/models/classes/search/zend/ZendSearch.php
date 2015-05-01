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
namespace oat\tao\model\search\zend;

use oat\tao\model\search\Search;
use tao_models_classes_FileSourceService;
use common_Logger;
use ZendSearch\Lucene\Lucene;
use ZendSearch\Lucene\Document;
use ZendSearch\Lucene\Search\QueryHit;
use oat\oatbox\Configurable;

/**
 * Zend Lucene Search implementation 
 * 
 * @author Joel Bout <joel@taotesting.com>
 */
class ZendSearch extends Configurable implements Search
{	
    /**
     * 
     * @var \ZendSearch\Lucene\SearchIndexInterface
     */
    private $index;
    
    /**
     * 
     * @return \ZendSearch\Lucene\SearchIndexInterface
     */
    public function getIndex() {
        if (is_null($this->index)) {
            $this->fileSystem = tao_models_classes_FileSourceService::singleton()->getFileSource($this->getOption('fileSystem'));
            $this->index = Lucene::open($this->fileSystem->getPath());
        }
        return $this->index;
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\tao\model\search\Search::query()
     */
    public function query($queryString) {
        $hits = $this->getIndex()->find($queryString);
        
        $ids = array();
        foreach ($hits as $hit) {
            $ids[] = $hit->getDocument()->getField('uri')->getUtf8Value();
        }
        
        return $ids;
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\tao\model\search\Search::index()
     */
    public function index(\Traversable $resourceTraversable) {
        
        // flush existing index
        $this->flushIndex();
        $count = 0;
        
        // index the resources
        foreach ($resourceTraversable as $resource) {
            $indexer = new ZendIndexer($resource);
            $this->getIndex()->addDocument($indexer->toDocument());
            $count++;
        }
        
        \common_Logger::i('Reindexed '.$count.' resources');
        return $count;
    }
    
    public function flushIndex() {
        $fileSystem = tao_models_classes_FileSourceService::singleton()->getFileSource($this->getOption('fileSystem'));
        Lucene::create($fileSystem->getPath());
    }
    
    /**
     * 
     * @return \oat\tao\model\search\zend\ZendSearch
     */
    public static function createSearch() {
        $privateDataPath = FILES_PATH.'tao'.DIRECTORY_SEPARATOR.'ZendSearch'.DIRECTORY_SEPARATOR;
        
        if (file_exists($privateDataPath)) {
            \helpers_File::emptyDirectory($privateDataPath);
        }
        
        $privateFs = \tao_models_classes_FileSourceService::singleton()->addLocalSource('Zend Search index folder', $privateDataPath);
        $search = new ZendSearch(array(
            'fileSystem' => $privateFs->getUri()
        )); 
        $search->flushIndex();
        return $search; 
    }
}