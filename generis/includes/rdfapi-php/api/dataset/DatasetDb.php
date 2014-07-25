<?php
require_once RDFAPI_INCLUDE_DIR . 'dataset/Dataset.php';
require_once RDFAPI_INCLUDE_DIR . 'model/DbModel.php';
require_once RDFAPI_INCLUDE_DIR . 'dataset/IteratorFindQuadsDb.php';
// ----------------------------------------------------------------------------------
// Class: DatasetDb
// ----------------------------------------------------------------------------------

/**
* Persistent implementation of a Dataset in a database.
* A RDF dataset is a collection of named RDF graphs.
*
* @version  $Id$
* @author Daniel Westphal (http://www.d-westphal.de)
* @author Chris Bizer <chris@bizer.de>
*
* @package 	dataset
* @access	public
**/
require_once(RDFAPI_INCLUDE_DIR.PACKAGE_DBASE);

class DatasetDb extends Dataset
{

	/**
	* Reference to databse connection.
	*
	* @var		PDO dbConnection
	* @access	private
	*/
	var $dbConnection;

	/**
	* Reference to the dbStore Object.
	*
	* @var		$dbStore dbStore
	* @access	private
	*/
	var $dbStore;


	/**
	* Name of the Dataset
	*
	* @var		string
	* @access	private
	*/
	var $setName;


	/**
    * Constructor
    * You can supply a Dataset name.
    *
    * @param  PDO
    * @param  DbStore
    * @param  string
	* @access	public
    */
	function DatasetDb(&$dbConnection,&$dbStore,$datasetName)
	{
		$this->dbConnection=& $dbConnection;
		$this->dbStore=&$dbStore;
		$this->setName= $datasetName;
		$this->_initialize();
	}

	/**
    * Initialize
    * Read all needed data into the set.
    *
    *
	* @access	private
    */
	function _initialize()
	{
		$recordSet =& $this->dbConnection->query('SELECT "defaultModelUri"
                                         FROM "datasets" WHERE "datasetName"=\''.$this->setName.'\'');
		
		
   		$this->defaultGraph=& $this->dbStore->getModel($recordSet->fetchColumn(0));
   		$recordSet->closeCursor();
	}



//	=== Graph level methods ========================

	/**
    * Sets the Dataset name. Return true on success, false otherwise.
    *
    * @param  string
	* @access	public
    */
	function setDatasetName($datasetName)
	{
		if ($this->dbStore->datasetExists($datasetName))
		{
			return false;
		}

		$dsName = $this->dbConnection->quote($datasetName);
		$sName = $this->dbConnection->quote($this->setName);
		$this->dbConnection->exec('UPDATE "datasets" SET "datasetName"='.$dsName.'
                                      WHERE "datasetName"='.$sName);

		$this->dbConnection->exec('UPDATE "dataset_model" SET "datasetName"='.$dsName.'
                                      WHERE "datasetName"='.$sName);
		$this->setName=$datasetName;
		return true;
	}

	/**
    * Returns the Datasets name.
    *
    * @return string
	* @access	public
    */
	function getDatasetName()
	{
		return $this->setName;
	}

	/**
	 * Adds a NamedGraph to the set.
	 *
	 * @param NamedGraphDb
	 */
	function addNamedGraph(&$graph)
	{
		$graphNameURI=$graph->getGraphName();
		$this->removeNamedGraph($graphNameURI);
		$this->dbConnection->exec('INSERT INTO "dataset_model" VALUES('
		  . $this->dbConnection->quote($this->setName) . ','
		  . $this->dbConnection->quote($graph->modelID) . ','
		  . $this->dbConnection->quote($graphNameURI) .')');
	}


	/**
	 * Overwrites the existting default graph.
	 *
	 * @param DbModel
	 */
	function setDefaultGraph(&$graph)
	{
		$this->dbConnection->exec('UPDATE "datasets" SET "defaultModelUri" ='
		  . $this->dbConnection->quote($graph->modelURI) . '  WHERE "datasetName" ='
		  . $this->dbConnection->quote($this->setName));
	}

	/**
	 * Returns a reference to the defaultGraph.
	 *
	 * @return NamedGraphDb
	 */
	function & getDefaultGraph()
	{
		$name = $this->dbConnection->quote($this->setName);
		$result = $this->dbConnection->query('SELECT "defaultModelUri" FROM "datasets" WHERE "datasetName" ='.$name);
		$defaultGraphURI = $result->fetchColumn(0);
		$result->closeCursor();
		return ($this->dbStore->getNamedGraphDb($defaultGraphURI,'http://rdfapi-php/dataset_defaultGraph_'.$this->setName));
	}

	/**
	 * Returns true, if a defaultGraph exists. False otherwise.
	 *
	 * @return boolean
	 */
	function hasDefaultGraph()
	{
		return true;
	}

	/**
	 * Removes a NamedGraph from the set. Nothing happens
	 * if no graph with that name is contained in the set.
	 *
	 * @param string
	 */
	function removeNamedGraph($graphName)
	{
		$this->dbConnection->exec('DELETE FROM "dataset_model" WHERE "datasetName"='
		  . $this->dbConnection->quote($this->setName) . ' AND "graphURI" ='
		  . $this->dbConnection->quote($graphName));
	}

	/**
	 * Tells wether the Dataset contains a NamedGraph.
	 *
	 * @param  string
	 * @return boolean
	 */
	function containsNamedGraph($graphName)
	{
		$sName = $this->dbConnection->quote($this->setName);
		$gName = $this->dbConnection->quote($graphName);
		$result = $this->dbConnection->query("SELECT count(*) FROM dataset_model WHERE datasetName=".$sName." AND graphURI =".$gName);
		$count = (int) $result->fetchColumn(0);
		$result->closeCursor();

		return ($count > 0);
	}

	/**
	 * Returns the NamedGraph with a specific name from the Dataset.
	 * Changes to the graph will be reflected in the set.
	 *
	 * @param string
	 * @return NamedGraphDb or null
	 */
	function &getNamedGraph($graphName)
	{
		if(!$this->containsNamedGraph($graphName))
			return null;

		$gName = $this->dbConnection->quote();
		$modelVars =& $this->dbConnection->query('SELECT "models"."modelURI", "models"."modelID", "models"."baseURI"
	                                             FROM "models", "dataset_model"
	                                             WHERE "dataset_model"."graphURI" = ' .$gName .' AND "dataset_model"."modelId" = "models"."modelID"');
		
		$row = $modelVars->fetch();
		$modelVars->closeCursor();
		
		return new NamedGraphDb($this->dbConnection, $row[0],
                         		$row[1], $graphName ,$row[2]);
	}

	/**
	 * Returns the names of the namedGraphs in this set as strings in an array.
	 *
	 * @return Array
	 */
	function listGraphNames()
	{
		$sName = $this->dbConnection->quote($this->setName);
		$recordSet =& $this->dbConnection->query('SELECT "graphURI" FROM "dataset_model" WHERE "datasetName" ='.$this->setName);

		$return = array();
		while ($row = $recordSet->fetch())
		{
		  $return[] = $row[0];
		}
		return $return;
	}

	/**
	 * Creates a new NamedGraph and adds it to the set. An existing graph with the same name will be replaced. But the old namedGraph remains in the database.
	 *
	 * @param  string
	 * @param  string
	 * @return NamedGraphDb
	 */
	function &createGraph($graphName,$baseURI = null)
	{
		$graph =& $this->dbStore->getNewNamedGraphDb(uniqid('http://rdfapi-php/namedGraph_'),$graphName,$baseURI);
		$this->addNamedGraph($graph);

		return $graph;
	}

	/**
	 * Deletes all NamedGraphs from the set.
	 */
	function clear()
	{
		$sName = $this->dbConnection->quote($this->setName);
		$this->dbConnection->exec('DELETE FROM "dataset_model" WHERE "datasetName" ='.$sName);
	}

	/**
	 * Returns the number of NamedGraphs in the set. Empty graphs are counted.
	 *
	 * @return int
	 */
	function countGraphs()
	{
		$sName = $this->dbConnection->quote($this->setName);
		$result = $this->dbConnection->query('SELECT count(*) FROM "dataset_model" WHERE "datasetName" ='.$sName);
		$count = $result->fetchColumn(0);
		$result->closeCursor();
		
		return $count;
	}

	/**
	 * Returns an iterator over all {@link NamedGraph}s in the set.
	 *
	 * @return IteratorAllGraphsDb
	 */
	function &listNamedGraphs()
	{
		$sName = $this->dbConnection->quote($this->setName);
		$recordSet =& $this->dbConnection->query('"SELECT "graphURI" FROM "dataset_model" WHERE "datasetName" ='.$sName);
		$it = new IteratorAllGraphsDb($recordSet, $this);
		return $it;
	}

	/**
	 * Tells wether the set contains any NamedGraphs.
	 *
	 * @return boolean
	 */
	function isEmpty()
	{
		return ($this->countGraphs()==0);
	}

	/**
	 * Add all named graphs of the other dataset to this dataset.
	 *
	 * @param Dataset
	 */
	function addAll($otherDataset)
	{
		for($iterator = $otherDataset->listNamedGraphs(); $iterator->valid(); $iterator->next())
		{
			$this->addNamedGraph($iterator->current());
 		};

 		if ($otherDataset->hasDefaultGraph())
 		{
 			$this->defaultGraph = $this->defaultGraph->unite($otherDataset->getDefaultGraph());
 		}
	}

//	=== Quad level methods ========================

	/**
	 * Adds a quad to the Dataset. The argument must not contain any
	 * wildcards. If the quad is already present, nothing happens. A new
	 * named graph will automatically be created if necessary.
	 *
	 * @param Quad
	 */
	function addQuad(&$quad)
	{
		$graphName=$quad->getGraphName();
		$graphName=$graphName->getLabel();

		$graph=& $this->getNamedGraph($graphName);

		if ($graph===null)
			$graph=& $this->createGraph($graphName);

		$statement=$quad->getStatement();
		$graph->add($statement);
	}

	/**
	 * Tells wether the Dataset contains a quad or
	 * quads matching a pattern.
	 *
	 * @param Resource
	 * @param Resource
	 * @param Resource
	 * @param Resource
	 * @return boolean
	 */
	function containsQuad($graphName,$subject,$predicate,$object)
	{
		// static part of the sql statement
		$sName = $this->dbConnection->quote($this->setName);
		$sql = 'SELECT count(*)
          		FROM "statements", "dataset_model"
           		WHERE "datasetName" ='.$sName.' AND "statements"."modelID" = "dataset_model"."modelId"';

		if($graphName!=null)
		{
			$gLabel = $this->dbConnection->quote($gLabel);
			$sql.= ' AND "graphURI" = '.$gLabel;
		}

		// dynamic part of the sql statement
		$sql .= DbModel::_createDynSqlPart_SPO($subject, $predicate, $object);
		
		$result = $this->dbConnection->query($sql);
		$count = $result->fetchColumn(0);
		$result->closeCursor();
		
		return ($count > 0);
	}

	/**
	 * Deletes a Quad from the RDF dataset.
	 *
	 * @param Quad
	 */
	function removeQuad($quad)
	{
		$graphName=$quad->getGraphName();$graphName=$graphName->getLabel();
		//find namedGraph IDs
		$gName = $this->dbConnection->quote($gName);
		$result = $this->dbConnection->query('SELECT "modelId" FROM "dataset_model" WHERE "graphURI" = '.$gName);
		$graphID = $result->fetchColumn(0);
		$result->closeCursor();

		// static part of the sql statement
		$sql = 'DELETE FROM "statements" WHERE "modelID" = '.$graphID;

		// dynamic part of the sql statement
		$sql .= DbModel::_createDynSqlPart_SPO($quad->getSubject(), $quad->getPredicate(), $quad->getObject());

		// execute the query
		if($graphID)
		{
			$this->dbConnection->exec($sql);
		}
	}

	/**
	 * Counts the Quads in the RDF dataset. Identical Triples in
	 * different NamedGraphs are counted individually.
	 *
	 * @return int
	 */
	function countQuads()
	{
		$sName = $this->dbConnection->quote();
		$sql = 'SELECT count(*)
          		FROM "statements", "dataset_model"
           		WHERE "datasetName" ='.$sName.' AND "statements"."modelID"="dataset_model"."modelId"';
		$result = $this->dbConnection->query($sql);
		$count = $result->fetchColumn(0);
		$result->closeCursor();

		return ((int)$count);
	}

	/**
	 * Finds Statements that match a quad pattern. The argument may contain
	 * wildcards.
	 *
	 * @param Resource or null
	 * @param Resource or null
	 * @param Resource or null
	 * @param Resource or null
	 * @return IteratorFindQuadsDb
	 */
	function &findInNamedGraphs($graphName,$subject,$predicate,$object,$returnAsTriples =false )
	{
		// static part of the sql statement
		$sName = $this->dbConnection->quote($this->setName);
		$sql = 'SELECT "subject", "predicate", "object", "l_language", "l_datatype", "subject_is", "object_is", "dataset_model"."graphURI"
          		FROM "statements", "dataset_model"
           		WHERE "datasetName" ='.$sName.' AND "statements"."modelID" = "dataset_model"."modelId"';

		if($graphName!=null)
		{
			$gName = $this->dbConnection->quote($graphName);
			$sql.= ' AND "graphURI" ='.$gName;
		}

		// dynamic part of the sql statement
		$sql .= DbModel::_createDynSqlPart_SPO($subject, $predicate, $object);

		// execute the query
		$recordSet =& $this->dbConnection->query($sql);

		$it = new IteratorFindQuadsDb($recordSet, $this, $returnAsTriples);
		return $it;
	}

	/**
	 * Finds Statements that match a pattern in the default Graph. The argument may contain
	 * wildcards.
	 *
	 * @param Resource or null
	 * @param Resource or null
	 * @param Resource or null
	 * @return IteratorFindQuadsDb
	 */
	function &findInDefaultGraph($subject,$predicate,$object)
	{
		$sName = $this->dbConnection->quote($this->setName);
		$recordSet = $this->dbConnection->query('SELECT "models"."modelID" FROM "datasets", "models" WHERE "datasets"."datasetName" ='.$sName.' AND "datasets"."defaultModelUri" = "models"."modelURI"');
		$defaultGraphID = $recordSet->fetchColumn(0);
		$recordSet->closeCursor();
		
		// static part of the sql statement
		$sql = 'SELECT "subject", "predicate", "object", "l_language", "l_datatype", "subject_is", "object_is"
          		FROM "statements"
           		WHERE "modelID" ='.$defaultGraphID;

		// dynamic part of the sql statement
		$sql .= DbModel::_createDynSqlPart_SPO($subject, $predicate, $object);

		// execute the query
		$recordSet =& $this->dbConnection->query($sql);

		$it = new IteratorFindQuadsDb($recordSet, $this, true);
		return $it;
	}
}
?>