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
    extends common_persistence_sql_QueryIterator
{
    /**
     * Constructor of the iterator expecting the model ids
     * 
     * @param array $modelIds
     */
    public function __construct(common_persistence_SqlPersistence $persistence, $modelIds = null) {
        $query = 'SELECT * FROM statements '
            .(is_null($modelIds) ? '' : 'WHERE modelid IN ('.implode(',', $modelIds).') ')
            .'ORDER BY id';
        parent::__construct($persistence, $query);
    }
    
    /**
     * (non-PHPdoc)
     * @see Iterator::current()
     * @return core_kernel_classes_Triple
     */
    function current() {
        $statement = parent::current();
        
        $triple = new core_kernel_classes_Triple();
        $triple->modelid = $statement["modelid"];
        $triple->subject = $statement["subject"];
        $triple->predicate = $statement["predicate"];
        $triple->object = $statement["object"];
        $triple->id = $statement["id"];
        $triple->lg = $statement["l_language"];
        return $triple;
    }
}