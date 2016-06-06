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
class common_persistence_sql_QueryIterator
    implements Iterator
{
    const CACHE_SIZE = 100;
    
    /**
     * @var common_persistence_SqlPersistence
     */
    private $persistence;
    
    /**
     * Query to iterator over
     *
     * @var string
     */
    private $query;
    
    /**
     * Query parameters
     *
     * @var string
     */
    private $params;
    
    /**
     * Id of the current instance
     * 
     * @var int
     */
    private $currentResult = null;

    /**
     * Return statements of the last query
     * 
     * @var array
     */
    private $cache = null;
    
    /**
     * Constructor of the iterator expecting the model ids
     * 
     * @param array $modelIds
     */
    public function __construct(common_persistence_SqlPersistence $persistence, $query, $params = array()) {
        $this->persistence = $persistence;
        $this->query = $query;
        $this->params = $params;
        $this->rewind();
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
        return $this->cache[$this->currentResult];
    }
    
    /**
     * (non-PHPdoc)
     * @see Iterator::key()
     */
    function key() {
        return $this->currentResult;
    }
    
    /**
     * (non-PHPdoc)
     * @see Iterator::next()
     */
    function next() {
        if ($this->valid()) {
            $last = $this->key();
            $this->currentResult++;
            if (!isset($this->cache[$this->currentResult])) {
                $this->load($last+1);
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
     * Loads the next n results, starting with $offset
     * 
     * @param int $offset
     */
    protected function load($offset) {

        $query = $this->persistence->getPlatForm()->limitStatement($this->query, self::CACHE_SIZE, $offset);
        $result = $this->persistence->query($query, $this->params);

        $this->cache = array();
        $pos = $offset;
        while ($statement = $result->fetch()) {
            $this->cache[$pos++] = $statement;
        }

        $this->currentResult = $offset;
    }
}