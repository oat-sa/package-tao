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

/**
 * Iterator over all triples
 * 
 * @author joel bout <joel@taotesting.com>
 * @package generis
 */
class core_kernel_persistence_smoothsql_SmoothIterator
    implements Iterator
{
    const CACHE_SIZE = 100;
    
    /**
     * @var array
     */
    private $modelIds;
    
    /**
     * @var common_persistence_SqlPersistence
     */
    private $persistence;
    
    /**
     * Id of the current instance
     * 
     * @var int
     */
    private $currentTriple;

    /**
     * List of resource uris currently being iterated over
     * 
     * @var array
     */
    private $cache = null;
    
    /**
     * Constructor of the iterator expecting the model ids
     * 
     * @param array $modelIds
     */
    public function __construct(common_persistence_SqlPersistence $persistence, $modelIds = null) {
        $this->persistence = $persistence;
        $this->modelIds = $modelIds;
        $this->currentTriple = 0;
        $this->load(0);
    }
    
    /**
     * (non-PHPdoc)
     * @see Iterator::rewind()
     */
    function rewind() {
        $this->load(0);
    }
    
    /**
     * (non-PHPdoc)
     * @see Iterator::current()
     * @return core_kernel_classes_Triple
     */
    function current() {
        return $this->cache[$this->currentTriple];
    }
    
    /**
     * (non-PHPdoc)
     * @see Iterator::key()
     */
    function key() {
        return $this->cache[$this->currentTriple]->id;
    }
    
    /**
     * (non-PHPdoc)
     * @see Iterator::next()
     */
    function next() {
        if ($this->valid()) {
            $last = $this->key();
            $this->currentTriple++;
            if (!isset($this->cache[$this->currentTriple])) {
                $this->load($last);
            }
        }
    }
    
    /**
     * (non-PHPdoc)
     * @see Iterator::valid()
     */
    function valid() {
        return !empty($this->cache);
    }
    
    /**
     * Loads the next n triples, startign with $id
     * 
     * @param int $id
     */
    protected function load($id) {
        
        $query = 'SELECT * FROM statements WHERE id > ? '
            .(is_null($this->modelIds) ? '' : 'AND modelid IN ('.implode(',', $this->modelIds).') ')
            .'ORDER BY id LIMIT ?';
        $result = $this->persistence->query($query, array($id, self::CACHE_SIZE));

        $this->cache = array();
        while ($statement = $result->fetch()) {
            $triple = new core_kernel_classes_Triple();
            $triple->modelid = $statement["modelid"];
            $triple->subject = $statement["subject"];
            $triple->predicate = $statement["predicate"];
            $triple->object = $statement["object"];
            $triple->id = $statement["id"];
            $triple->lg = $statement["l_language"];
            $this->cache[] = $triple;
        }
        
        $this->currentTriple = 0;
    }
}