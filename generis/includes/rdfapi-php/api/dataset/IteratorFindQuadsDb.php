<?php 
//----------------------------------------------------------------------------------
// Class: IteratorFindQuadsDb
// ----------------------------------------------------------------------------------


/**
* Implementation of a quad iterator.
*
* This Iterator should be used like:
* for($iterator = $dataset->findInNamedGraphs(null,null,null,null); $iterator->valid(); $iterator->next()) 
* {
*	$currentQuad=$iterator->current();
* };
*
*
* @version  $Id$
* @author Daniel Westphal (http://www.d-westphal.de)
*
*
* @package 	dataset
* @access	public
**/
class IteratorFindQuadsDb
{
	/**
	* Holds a reference to the associated DB resultSet.
	*
	* @var		$dbResultSets PDOStatement
	* @access	private
	*/
	var $dbResultSet;
	
	/**
	* Holds a reference to the associated datasetDb.
	*
	* @var		$datasetDb datasetDb
	* @access	private
	*/
	var $datasetDb;

	/**
	* boolean value, if the results should be returned as triples.
	*
	* @var		boolean
	* @access	private
	*/
	var $returnAsTriples;
	
	var $key;
	
	var $current;
	
	/**
    * Constructor.
    *
    * @param dataset
	* @access	public
    */
	function IteratorFindQuadsDb(&$dbResultSet,&$datasetDb,$returnAsTriples=false)
	{
		$this->dbResultSet=& $dbResultSet;
		$this->datasetDb=& $datasetDb;
		$this->returnAsTriples=$returnAsTriples;
		$this->key = 0;
		$this->current = $dbResultSet->fetch();
	}
	
	/**
    * Resets iterator list to start.
    *
	* @access public
    */
	function rewind()
	{
		//not supported
	}
	
	/**
    * Says if there are additional items left in the list.
    *
    * @return	boolean
	* @access	public
    */
	function valid()
	{
		if (($this->dbResultSet === false) || (empty($this->current))){
			return false;
		}
		else{
			return true;	
		}
	}
	
	/**
    * Moves Iterator to the next item in the list.
    *
	* @access	public
    */
	function next()
	{
		if ($this->dbResultSet !== false){
			$this->current = $this->dbResultSet->fetch();
			$this->key++;
		}
	}
	
	/**
    * Returns the current item.
    *
    * @return	mixed
	* @access	public
    */
	function &current()
	{
		if ($this->dbResultSet === false){
			return null;
		}
		// subject
		if ($this->current[5] == 'r'){
			$sub = new Resource($this->current[0]);
		}
		else{
			$sub = new BlankNode($this->current[0]);
		}

		// predicate
		$pred = new Resource($this->current[1]);

		// object
		if ($this->current[6] == 'r'){
			$obj = new Resource($this->current[2]);
		}
		elseif ($this->current[6] == 'b'){
			$obj = new BlankNode($this->current[2]);
		}
		else{
			$obj = new Literal($this->current[2], $this->current[3]);
			if ($this->current[4])
			$obj->setDatatype($this->current[4]);
		}

		if($this->returnAsTriples){
			return (new Statement($sub, $pred, $obj));
		}
		else{
			return (new Quad(new Resource($this->current[7]),$sub,$pred,$obj));
		}
	}
	
	/**
    * Returns the key of the current item.
    *
    * @return	integer
	* @access	public
    */
	function key()
	{
		return $this->key;
	}
}
?>