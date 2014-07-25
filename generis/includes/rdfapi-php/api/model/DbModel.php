<?php
require_once RDFAPI_INCLUDE_DIR . 'model/Model.php';
require_once RDFAPI_INCLUDE_DIR . 'model/Blanknode.php';
require_once RDFAPI_INCLUDE_DIR . 'model/Statement.php';
require_once RDFAPI_INCLUDE_DIR . 'util/DBConnection.php';

// ----------------------------------------------------------------------------------
// Class: DbModel
// ----------------------------------------------------------------------------------

/**
* This class provides methods for manipulating DbModels from DbStore.
* A DbModel is an RDF Model, which is persistently stored in a relational database.
* This Class uses the PDO Database Abstraction Library.
*
*
* @version  $Id: DbModel.php 533 2007-08-16 09:32:03Z cweiske $
* @author   Radoslaw Oldakowski <radol@gmx.de>
*
* @package model
* @access	public
*/


class DbModel extends Model{

	/**
	* Database connection object.
	*
	* @var     PDO
	* @access	private
	*/
	var $dbConn;

	/**
	* Unique model URI.
	* Used to identify the DbModel.
	*
	* @var     string
	* @access	private
	*/
	var $modelURI;


	/**
	* Database internal modelID.
	* Used to avoid JOINs.
	*
	* @var     string
	* @access	private
	*/
	var $modelID;




	/**
	* Constructor
	* Do not call this directly.
	* Use the method getModel,getNewModel or putModel of the Class DbStore instead.
	*
	* @param   object PDO  &$dbConnection
	* @param   string   $modelURI
	* @param   string   $modelID
	* @param   string   $baseURI
	* @access	public
	*/
	function DbModel(&$dbConnection, $modelURI, $modelID, $baseURI=NULL) {

		$this->dbConn =& $dbConnection;
		$this->modelURI = $modelURI;
		$this->modelID = $modelID;
		$this->baseURI = $this->_checkBaseURI($baseURI);
	}


	/**
	* Set a base URI for the DbModel.
	* Affects creating of new resources and serialization syntax.
	*
	* @param	string	$uri
	* @throws  SqlError
	* @access	public
	*/
	function setBaseURI($uri) {

		$this->baseURI = $this->_checkBaseURI($uri);
		$bURI = $this->dbConn->quote($this->baseURI);
		
		$rs = $this->dbConn->exec('UPDATE "models" SET "baseURI"=' . $bURI .'
                                 WHERE "modelID"=' .$this->modelID);
		if ($rs === false){
			$errmsg = $this->dbConn->errorInfo();
			$errmsg = $errmsg[0];
			trigger_error($errmsg, E_USER_ERROR);	
		}
	}


	/**
	* Return the number of statements in this DbModel.
	*
	* @return	integer
	* @access	public
	*/
	function size() {
		$recordSet = $this->dbConn->query('SELECT COUNT("modelID") FROM "statements"
                                    WHERE "modelID" = ' .$this->modelID);
		$count = (int) $recordSet->fetchColumn(0);
		$recordSet->closeCursor();
		
		return $count;
	}


	/**
	* Check if this DbModel is empty.
	*
	* @return	boolean
	* @access	public
	*/
	function isEmpty() {
		return ($this->size() == 0);
	}


	/**
	* Add a new triple to this DbModel.
	*
	* @param	object Statement	&$statement
	* @throws	PhpError
	* @throws  SqlError
	* @access	public
	* @return mixed   true on success, false if the statement is already in the model,
	*                 error message (string) on failure
	*/
	function add(&$statement, $author = '') {

		if (!is_a($statement, 'Statement')) {
			$errmsg = RDFAPI_ERROR . '(class: DbModel; method: add): Statement expected.';
            common_Logger::e($errmsg);
			trigger_error($errmsg, E_USER_ERROR);
		}

		if (!$this->contains($statement)) {

			$sql = 'INSERT INTO "statements"
			        ("modelID", "subject", "predicate", "object", "l_language", "author", "stread", "stedit", "stdelete", "epoch")
			        VALUES
                    (?, ?, ?,';
			
			$a = array($this->modelID,
					   $statement->getLabelSubject(),
					   $statement->getLabelPredicate());
			
			if (is_a($statement->object(), 'Literal')) {
				$sql .= '?, ?, ?,'
				. "'yyy[admin,administrators,authors]',"		
				. "'yyy[admin,administrators,authors]',"
				. "'yyy[admin,administrators,authors]',"
				. "CURRENT_TIMESTAMP)";
				
				$a[] = $statement->obj->getLabel();
				$a[] = ($statement->obj->getLanguage() == null) ? '' : $statement->obj->getLanguage();
				$a[] = $author;
			}else{
				$sql .= '?,'
				. "'',"
				. '?,'
				."'yyy[admin,administrators,authors]',"
				."'yyy[admin,administrators,authors]',"
				."'yyy[admin,administrators,authors]',"
				."CURRENT_TIMESTAMP)";

				$a[] = $statement->obj->getLabel();
				$a[] = $author;
			}

			$sth = $this->dbConn->prepare($sql);
			
			if ($sth === false) {
				$errormsg = $this->dbConn->errorInfo();
				$errormsg = $errormsg[0];
                common_Logger::e($errmsg);
				trigger_error($errmsg, E_USER_ERROR);
            } else {
            	if ($sth->execute($a) === true){
                	return true;
            	}
            	else{
            		$errmsg = $sth->errorInfo();
            		$errormsg = $errmsg[0];
                    common_Logger::e($errmsg);
            		trigger_error($errmsg, E_USER_ERROR);	
            	}
            }
		} else {
			return false;
		}
	}


	/**
	* Alias for the method add().
	*
	* @param	object Statement	&$statement
	* @throws	PhpError
	* @throws  SqlError
	* @access	public
	*/
	function addWithoutDuplicates(&$statement) {

		$this->add($statement);
	}


	/**
	* Remove the given triple from this DbModel.
	*
	* @param	object Statement	&$statement
	* @throws	PhpError
	* @throws  SqlError
	* @access	public
	*/
	function remove(&$statement) {

		if (!is_a($statement, 'Statement')) {
			$errmsg = RDFAPI_ERROR . '(class: DbModel; method: remove): Statement expected.';
			trigger_error($errmsg, E_USER_ERROR);
		}

		$sql = 'DELETE FROM "statements"
           WHERE "modelID"=' .$this->modelID;
		$sql .= $this->_createDynSqlPart_SPO ($statement->subj, $statement->pred, $statement->obj);

		$rs =& $this->dbConn->execute($sql);
		if ($rs === false){
			$errmsg = $this->dbConn->errorInfo();
			$errmsg = $errmsg[0];
            common_Logger::e($errmsg);
			trigger_error($errmsg, E_USER_ERROR);
		}
	}


	/**
	* Short dump of the DbModel.
	*
	* @return	string
	* @access	public
	*/
	function toString() {

		return 'DbModel[modelURI=' .$this->modelURI .'; baseURI=' .$this->getBaseURI() .';  size=' .$this->size() .']';
	}


	/**
	* Dump of the DbModel including all triples.
	*
	* @return	string
	* @access	public
	*/
	function toStringIncludingTriples() {

		$memModel =& $this->getMemModel();
		return $memModel->toStringIncludingTriples();
	}



	/**
	* Create a MemModel containing all the triples of the current DbModel.
	*
	* @return object MemModel
	* @access public
	*/
	function & getMemModel() {

		$recordSet = $this->_getRecordSet($this);
		$m = $this->_convertRecordSetToMemModel($recordSet);
		return $m;
	}



    /**
    * Returns the model id
    *
    * @return int Model id number
    * @access public
    */
    function getModelID()
    {
        return $this->modelID;
    }



    /**
    * Returns the database connection object
    *
    * @return PDO Database object
    * @access public
    */
    function &getDbConn()
    {
        return $this->dbConn;
    }



	/**
	* Write the RDF serialization of the _DbModel as HTML.
	*
	* @access	public
	*/
	function writeAsHtml() {

		$memModel =& $this->getMemModel();
		$memModel->writeAsHtml();
	}


	/**
	* Write the RDF serialization of the DbModel as HTML table.
	*
	* @access	public
	*/
	function writeAsHtmlTable() {
		include_once(RDFAPI_INCLUDE_DIR.PACKAGE_UTILITY);
		$memModel =& $this->getMemModel();
		RDFUtil::writeHTMLTable($memModel);
	}


	/**
	* Write the RDF serialization of the DbModel to string.
	*
	* @return	string
	* @access	public
	*/
	function writeRdfToString() {

		$memModel =& $this->getMemModel();
		return $memModel->writeRdfToString();
	}


	/**
	* Saves the RDF,N3 or N-Triple serialization of the DbModel to a file.
	* You can decide to which format the model should be serialized by using a
	* corresponding suffix-string as $type parameter. If no $type parameter
	* is placed this method will serialize the model to XML/RDF format.
	* Returns FALSE if the DbModel couldn't be saved to the file.
	*
	* @access	public
	* @param 	string 	$filename
	* @param 	string 	$type
	* @throws   PhpError
	* @return	boolean
	*/
	function saveAs($filename, $type ='rdf') {

		$memModel = $this->getMemModel();
		$memModel->saveAs($filename, $type);

	}


	/**
	* Check if the DbModel contains the given statement.
	*
	* @param object Statement  &$statement
	* @return	boolean
	* @access	public
	*/
	function contains(&$statement) {

		$sql = 'SELECT "modelID" FROM "statements"
           WHERE "modelID" = ' .$this->modelID;
		$sql .= $this->_createDynSqlPart_SPO($statement->subj, $statement->pred, $statement->obj);
		
		$res = $this->dbConn->query($sql);
		$return = false;
		if ($row = $res->fetch()){
			$return = true; 
		}
		
		$res->closeCursor();
		return $return;
	}


	/**
	* Determine if all of the statements in the given model are also contained in this DbModel.
	*
	* @param	object Model	&$model
	* @return	boolean
	* @access	public
	*/
	function containsAll(&$model) {

		if (is_a($model, 'MemModel')) {

			foreach($model->triples as $statement)
			if(!$this->contains($statement))
			return FALSE;
			return TRUE;
		}

		elseif (is_a($model, 'DbModel')) {

			$recordSet = $this->_getRecordSet($model);
			while ($row = $recordSet->fetch()) {
				if (!$this->_containsRow($row))
				return FALSE;
			}
			return TRUE;
		}

		$errmsg = RDFAPI_ERROR . '(class: DbModel; method: containsAll): Model expected.';
		trigger_error($errmsg, E_USER_ERROR);
	}


	/**
	* Determine if any of the statements in the given model are also contained in this DbModel.
	*
	* @param	object Model	&$model
	* @return	boolean
	* @access	public
	*/
	function containsAny(&$model) {

		if (is_a($model, 'MemModel')) {

			foreach($model->triples as $statement)
			if($this->contains($statement))
			return TRUE;
			return FALSE;
		}

		elseif (is_a($model, 'DbModel')) {

			$recordSet = $this->_getRecordSet($model);
			while ($row = $recordSet->fetch()) {
				if ($this->_containsRow($row))
				return TRUE;
			}
			return FALSE;
		}

		$errmsg = RDFAPI_ERROR . '(class: DbModel; method: containsAny): Model expected.';
		trigger_error($errmsg, E_USER_ERROR);
	}


	/**
	* General method to search for triples in the DbModel.
	* NULL input for any parameter will match anything.
	* Example:  $result = $m->find( NULL, NULL, $node );
	*           Finds all triples with $node as object.
	*
	* @param	object Resource	$subject
	* @param	object Resource	$predicate
	* @param	object Node	$object
	* @return	object MemModel
	* @throws	PhpError
	* @throws  SqlError
	* @access	public
	*/
	function find($subject, $predicate, $object) {

		if ((!is_a($subject, 'Resource') && $subject != NULL) ||
		(!is_a($predicate, 'Resource') && $predicate != NULL) ||
		(!is_a($object, 'Node') && $object != NULL)) {

			$errmsg = RDFAPI_ERROR . '(class: DbModel; method: find): Parameters must be subclasses of Node or NULL';
			trigger_error($errmsg, E_USER_ERROR);
		}

		// static part of the sql statement
		$sql = 'SELECT "subject", "predicate", "object", "l_language"
           FROM "statements"
           WHERE "modelID" = ' .$this->modelID;

		// dynamic part of the sql statement
		$sql .= $this->_createDynSqlPart_SPO($subject, $predicate, $object);

		// execute the query
		$recordSet = $this->dbConn->query($sql);

		if ($recordSet === false){
			$errmsg = $this->dbConn->errorInfo();
			$errmsg = $errmsg[0];
			trigger_error($errmsg, E_USER_ERROR);	
		}
		// write the recordSet into memory Model
		else{
			return $this->_convertRecordSetToMemModel($recordSet);
		}
	}


	/**
	* Method to search for triples using Perl-style regular expressions.
	* NULL input for any parameter will match anything.
	* Example:  $result = $m->find_regex( NULL, NULL, $regex );
	*           Finds all triples where the label of the object node matches
	*the regular expression.
	* Return an empty MemModel if nothing is found.
	* !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
	* WARNING: Mhis method loads a DbModel into memory and performs the search
	*          on a MemModel, which can be slow with large models.
	* !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
	*
	* @param	string	$subject_regex
	* @param	string	$predicate_regex
	* @param	string	$object_regex
	* @return	object MemModel
	* @throws	PhpError
	* @throws  SqlError
	* @access	public
	*/
	function findRegex($subject_regex, $predicate_regex, $object_regex) {

		$mm =& $this->getMemModel();

		return $mm->findRegex($subject_regex, $predicate_regex, $object_regex);
	}


	/**
	* Return all tripels of a certain vocabulary.
	* $vocabulary is the namespace of the vocabulary inluding a # : / char at the end.
	* e.g. http://www.w3.org/2000/01/rdf-schema#
	* Return an empty model if nothing is found.
	*
	* @param	string	$vocabulary
	* @return	object MemModel
	* @throws	PhpError
	* @throws  SqlError
	* @access	public
	*/
	function findVocabulary($vocabulary) {

		$sql = 'SELECT "subject", "predicate", "object", "l_language", "l_datatype"
           FROM statements
           WHERE modelID = ' .$this->modelID .'
           AND predicate LIKE \'' .$vocabulary .'%\'';

		$recordSet = $this->dbConn->query($sql);

		if ($recordSet === false){
			$errmsg = $this->dbConn->errorInfo();
			$errmsg = $errmsg[0];
			trigger_error($errmsg, E_USER_ERROR);
		}
		// write the recordSet into memory Model
		else{
			return $this->_convertRecordSetToMemModel($recordSet);
		}
	}


	/**
	* Search for triples and return the first matching statement.
	* NULL input for any parameter will match anything.
	* Return an NULL if nothing is found.
	* You can set an search offset with $offset.
	*
	* @param	object Resource	$subject
	* @param	object Resource	$predicate
	* @param	object Node	$object
	* @param	integer	$offset
	* @return	object Statement
	* @throws  PhpError
	* @throws  SqlError
	* @access	public
	*/
	function findFirstMatchingStatement($subject, $predicate, $object, $offset = -1) {

		if ((!is_a($subject, 'Resource') && $subject != NULL) ||
		(!is_a($predicate, 'Resource') && $predicate != NULL) ||
		(!is_a($object, 'Node') && $object != NULL)) {

			$errmsg = RDFAPI_ERROR . '(class: DbModel; method: find): Parameters must be subclasses of Node or NULL';
			trigger_error($errmsg, E_USER_ERROR);
		}

		// first count it... thanks to PDO :/
		$sql = 'SELECT COUNT(*) FROM "statements" WHERE "modelID" = ' . $this->modelID;
		$sql .= $this->_createDynSqlPart_SPO($subject, $predicate, $object);
		$sql .= " LIMIT 1";
		if ($offset != -1){
			$sql .= " OFFSET ${offset}";	
		}
		
		$recordSet = $this->dbConn->query($sql);
		$count = (int) $recordSet->fetchColumn(0);
		$recordSet->closeCursor();
		
		// static part of the sql statement
		$sql = 'SELECT "subject", "predicate", "object", "l_language"
           FROM "statements"
           WHERE "modelID" = ' .$this->modelID;

		// dynamic part of the sql statement
		$sql .= $this->_createDynSqlPart_SPO($subject, $predicate, $object);

		// execute the query
		$sql .= " LIMIT 1";
		if ($offset != -1){
			$sql .= " OFFSET ${offset}";
		}
		$recordSet = $this->dbConn->query($sql);
		
		if ($recordSet === false){
			$errmsg = $this->dbConn->errorInfo();
			$errmsg = $errmsg[0];
		}
		else {
			if ($count == 0){
				return NULL;
			}
			else {
				$memModel = $this->_convertRecordSetToMemModel($recordSet);
				return $memModel->triples[0];
			}
		}
	}


	/**
	* Search for triples and return the number of matches.
	* NULL input for any parameter will match anything.
	*
	* @param	object Resource	$subject
	* @param	object Resource	$predicate
	* @param	object Node  	$object
	* @return	integer
	* @throws	PhpError
	* @throws  SqlError
	* @access	public
	*/
	function findCount($subject, $predicate, $object) {

		if ((!is_a($subject, 'Resource') && $subject != NULL) ||
		(!is_a($predicate, 'Resource') && $predicate != NULL) ||
		(!is_a($object, 'Node') && $object != NULL)) {

			$errmsg = RDFAPI_ERROR . '(class: DbModel; method: find): Parameters must be subclasses of Node or NULL';
			trigger_error($errmsg, E_USER_ERROR);
		}

		// static part of the sql statement
		$sql = 'SELECT COUNT(*)
           FROM "statements"
           WHERE "modelID" = ' .$this->modelID;

		// dynamic part of the sql statement
		$sql .= $this->_createDynSqlPart_SPO($subject, $predicate, $object);

		// execute the query
		$recordSet = $this->dbConn->query($sql);

		if ($recordSet === false){
			$errmsg = $this->dbConn->errorInfo();
			$errmsg = $errmsg[0];
			trigger_error($errmsg, E_USER_ERROR);
		}
		else{
			$count = $recordSet->fetchColumn(0);
			$recordSet->closeCursor();
			return $count;
		}
	}


	/**
	* Perform an RDQL query on this DbModel.
	* This method returns an associative array of variable bindings.
	* The values of the query variables can either be RAP's objects (instances of Node)
	* if $returnNodes set to TRUE, or their string serialization.
	*
	* @access	public
	* @param string $queryString
	* @param boolean $returnNodes
	* @return  array   [][?VARNAME] = object Node  (if $returnNodes = TRUE)
	*      OR  array   [][?VARNAME] = string
	*
	*/
	function rdqlQuery($queryString, $returnNodes = TRUE) {
		require_once(RDFAPI_INCLUDE_DIR.PACKAGE_RDQL);
		$parser = new RdqlParser();
		$parsedQuery =& $parser->parseQuery($queryString);

		// this method can only query this DbModel
		// if another model was specified in the from clause throw an error
		if (isset($parsedQuery['sources'][0]))
		if($parsedQuery['sources'][0] != $this->modelURI) {
			$errmsg = RDFAPI_ERROR . '(class: DbModel; method: rdqlQuery):';
			$errmsg .= ' this method can only query this DbModel';
			trigger_error($errmsg, E_USER_ERROR);
		}

		$engine = new RdqlDbEngine();
		$res =& $engine->queryModel($this, $parsedQuery, $returnNodes);

		return $res;
	}


	/**
	* Perform an RDQL query on this DBModel.
	* This method returns an RdqlResultIterator of variable bindings.
	* The values of the query variables can either be RAP's objects (instances of Node)
	* if $returnNodes set to TRUE, or their string serialization.
	*
	* @access	public
	* @param string $queryString
	* @param boolean $returnNodes
	* @return  object RdqlResultIterator = with values as object Node  (if $returnNodes = TRUE)
	*      OR  object RdqlResultIterator = with values as strings if (if $returnNodes = FALSE)
	*
	*/
	function rdqlQueryAsIterator($queryString, $returnNodes = TRUE) {
		require_once(RDFAPI_INCLUDE_DIR.PACKAGE_RDQL);
		return new RdqlResultIterator($this->rdqlQuery($queryString, $returnNodes));
	}

	/**
	* General method to replace nodes of a DbModel.
	* NULL input for any parameter will match nothing.
	* Example:  $m->replace($resource, NULL, $node, $replacement);
	*           Replaces all $node objects beeing subject or object in
	*           any triple of the model with the $replacement node.
	* Throw an error in case of a paramter mismatch.
	*
	* @param	object Resource	$subject
	* @param	object Resource	$predicate
	* @param	object Node	$object
	* @param	object Node	$replacement
	* @throws	PhpError
	* @throws  SqlError
	* @access	public
	*/
	function replace($subject, $predicate, $object, $replacement) {

		// check the correctness of the passed parameters
		if ( ((!is_a($subject, 'Resource') && $subject != NULL) ||
		(!is_a($predicate, 'Resource') && $predicate != NULL) ||
		(!is_a($object, 'Node') && $object != NULL)) ||
		(($subject != NULL && is_a($replacement, 'Literal')) ||
		($predicate != NULL && (is_a($replacement, 'Literal') ||
		is_a($replacement, 'BlankNode')))) )
		{
			$errmsg = RDFAPI_ERROR . '(class: DbModel; method: find): Parameter mismatch';
			trigger_error($errmsg, E_USER_ERROR);
		}

		if (!(!$subject && !$predicate && !$object)) {

			// create an update sql statement
			$comma = '';
			$sql = 'UPDATE "statements"
             SET ';
			if ($subject) {
				$sql .= " \"subject\" ='" .$replacement->getLabel() ."' ";
				$comma = ',';
			}
			if ($predicate) {
				$sql .= $comma ." \"predicate\"='" .$replacement->getLabel() ."' ";
				$comma = ',';
			}
			if ($object) {
				$quotedObject = $this->dbConn->quote($replacement->getLabel());
				$sql .= $comma .' "object"=' .$quotedObject. ' ';
				if (is_a($replacement, 'Literal')) {
					$sql .= ", \"l_language\"='" .$replacement->getLanguage() ."' ";
				}
			}
			$sql .= 'WHERE "modelID" = ' .$this->modelID;
			$sql .= $this->_createDynSqlPart_SPO($subject, $predicate, $object);

			// execute the query
			$rs = $this->dbConn->exec($sql);

			if ($rs === false){
				$errmsg = $this->dbConn->errorInfo();
				$errmsg = $errmsg[0];
				trigger_error($errmsg, E_USER_ERROR);
			}
		}
	}


	/**
	* Check if two models are equal.
	* Two models are equal if and only if the two RDF graphs they represent are isomorphic.
	*
	* Warning: This method doesn't work correct with models where the same blank node has different
	* identifiers in the two models. We will correct this in a future version.
	*
	* @param	object	model &$that
	* @return	boolean
	* @throws  PhpError
	* @access	public
	*/

	function equals(&$that)  {

		if (!is_a($that, 'Model')) {
			$errmsg = RDFAPI_ERROR . '(class: DbModel; method: equals): Model expected.';
			trigger_error($errmsg, E_USER_ERROR);
		}

		if ($this->size() != $that->size())
		return FALSE;

		include_once(RDFAPI_INCLUDE_DIR. "util/ModelComparator.php");
		return ModelComparator::compare($this,$that);
	}


	/**
	* Return a new MemModel that is the set-union the model with another model.
	*
	* The result of taking the set-union of two or more RDF graphs (i.e. sets of triples)
	* is another graph, which we will call the merge of the graphs.
	* Each of the original graphs is a subgraph of the merged graph. Notice that when forming
	* a merged graph, two occurrences of a given uriref or literal as nodes in two different
	* graphs become a single node in the union graph (since by definition they are the same
	* uriref or literal) but blank nodes are not 'merged' in this way; and arcs are of course
	* never merged. In particular, this means that every blank node in a merged graph can be
	* identified as coming from one particular graph in the original set of graphs.
	*
	* Notice that one does not, in general, obtain the merge of a set of graphs by concatenating
	* their corresponding N-triples documents and constructing the graph described by the merged
	* document, since if some of the documents use the same node identifiers, the merged document
	* will describe a graph in which some of the blank nodes have been 'accidentally' merged.
	* To merge Ntriples documents it is necessary to check if the same nodeID is used in two or
	* more documents, and to replace it with a distinct nodeID in each of them, before merging the
	* documents. (Not implemented yet !!!!!!!!!!!)
	*
	* @param	object Model	$model
	* @return	object MemModel
	* @throws PhpError
	* @access	public
	*
	*/
	function & unite(&$model)  {

		if (!is_a($model, 'Model')) {
			$errmsg = RDFAPI_ERROR . '(class: DbModel; method: unite): Model expected.';
			trigger_error($errmsg, E_USER_ERROR);
		}

		if (is_a($model, 'MemModel')) {

			$thisModel =& $this->getMemModel();
			return $thisModel->unite($model);
		}

		elseif (is_a($model, 'DbModel')) {

			$thisModel =& $this->getMemModel();
			$thatModel =& $model->getMemModel();
			return $thisModel->unite($thatModel);
		}
	}


	/**
	* Return a new MemModel that is the subtraction of another model from this DbModel.
	*
	* @param	object Model	$model
	* @return	object MemModel
	* @throws PhpError
	* @access	public
	*/

	function & subtract(&$model)  {

		if (!is_a($model, 'Model')) {
			$errmsg = RDFAPI_ERROR . '(class: DbModel; method: subtract): Model expected.';
			trigger_error($errmsg, E_USER_ERROR);
		}

		if (is_a($model, 'MemModel')) {

			$thisModel =& $this->getMemModel();
			return $thisModel->subtract($model);
		}

		elseif (is_a($model, 'DbModel')) {

			$thisModel =& $this->getMemModel();
			$thatModel =& $model->getMemModel();
			return $thisModel->subtract($thatModel);
		}
	}


	/**
	* Return a new MemModel containing all the statements which are in both
	* this model and the given model.
	*
	* @param	object Model	$model
	* @return	object MemModel
	* @throws  PhpError
	* @access	public
	*/
	function & intersect(&$model)  {

		if (is_a($model, 'MemModel')) {

			$thisModel =& $this->getMemModel();
			return $thisModel->intersect($model);
		}

		elseif (is_a($model, 'DbModel')) {

			$thisModel =& $this->getMemModel();
			$thatModel =& $model->getMemModel();
			return $thisModel->intersect($thatModel);
		}

		$errmsg = RDFAPI_ERROR . '(class: DbModel; method: intersect: Model expected.';
		trigger_error($errmsg, E_USER_ERROR);
	}


	/**
	* Add the given model to this DbModel.
	* This function monitors for SQL errors, and will commit if no errors have occured,
	* otherwise it will rollback.
	* If any statement of the model to be added to this model contains a blankNode
	* with an identifier already existing in this model, a new blankNode is generated.
	*
	* @param	object Model	$model
	* @throws  PhpError
	* @access	public
	*/
	function addModel(&$model)  {

		if (!is_a($model, 'Model')) {
			$errmsg = RDFAPI_ERROR . '(class: DbModel; method: addModel): Model expected.';
			trigger_error($errmsg, E_USER_ERROR);
		}

		$blankNodes_tmp = array();

		if (is_a($model, 'MemModel')) {

			$this->dbConn->startTrans();
			foreach ($model->triples as $statement)
			$this->_addStatementFromAnotherModel($statement, $blankNodes_tmp);
			$this->addParsedNamespaces($model->getParsedNamespaces());

			$this->dbConn->completeTrans();
		}

		elseif (is_a($model, 'DbModel')) {

			$this->dbConn->startTrans();
			$memModel =& $model->getMemModel();
			foreach($memModel->triples as $statement)
			$this->_addStatementFromAnotherModel($statement, $blankNodes_tmp);
			$this->addParsedNamespaces($model->getParsedNamespaces());
			$this->dbConn->completeTrans();
		}
	}


	/**
	* Reify the DbModel.
	* Return a new MemModel that contains the reifications of all statements of this DbModel.
	*
	* @return	object	MemModel
	* @access	public
	*/
	function & reify() {

		$memModel =& $this->getMemModel();
		return $memModel->reify();
	}

	/**
	* Remove this DbModel from database and clean up.
	* This function monitors for SQL errors, and will commit if no errors have occured,
	* otherwise it will rollback.
	*
	* @throws  SqlError
	* @access	public
	*/
	function delete() {

		try{
			$this->dbConn->beginTransaction();
			$this->dbConn->exec('DELETE FROM "models"
	                             WHERE "modelID"=' .$this->modelID);
			$this->dbConn->exec('DELETE FROM "statements"
	                             WHERE "modelID"=' .$this->modelID);
			$this->dbConn->exec('DELETE FROM "namespaces"
	                             WHERE "modelID"=' .$this->modelID);
	
			$this->dbConn->commit();
			$this->close();
		}
		catch (PDOException $e){
			$this->dbConn->rollBack();
			$errmsg = $this->dbConn->errorInfo();
			$errmsg = $errmsg[0];
			trigger_error($errmsg, E_USER_ERROR);
		}
	}


	/**
	* Close this DbModel
	*
	* @access	public
	*/
	function close() {

		unset($this);
	}


	// =============================================================================
	// **************************** private methods ********************************
	// =============================================================================





	/**
	* If the URI doesn't end with # : or /, then a # is added to the URI.
	* Used at setting the baseURI of this DbModel.
	*
	* @param   string  $uri
	* @return  string
	* @access	private
	*/
	function _checkBaseURI($uri)  {

		if ($uri != NULL) {
			$c = substr($uri, strlen($uri)-1 ,1);
			if (!($c=='#' || $c==':' || $c=='/' || $c=="\\"))
			$uri .= '#';
		}
		return $uri;
	}


	/**'
	* Return the flag of the Node object.
	* r - Resource, b - BlankNode, l - Literal
	*
	* @param   object Node $object
	* @return  string
	* @access	private
	*/
	function _getNodeFlag($object)  {

		return is_a($object,'BlankNode')?'b':(is_a($object,'Resource')?'r':'l');
	}


	/**
	* Convert an ADORecordSet to a memory Model.
	*
	* Every successful database query returns an ADORecordSet object which is actually
	* a cursor that holds the current row in the array fields[].
	* !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
	* !!! This method can only be applied to a RecordSet with array fields[]
	* !!! containing a representation of the database table: statements,
	* !!! with an index corresponding to following table columns:
	* !!! [0] - subject, [1] - predicate, [2] - object, [3] - l_language,
	* !!! [4] - l_datatype, [5] - subject_is, [6] - object_is
	* !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
	*
	* @param   object  ADORecordSet
	* @return  object  MemModel
	* @access	private
	*/
	function _convertRecordSetToMemModel(&$recordSet)  {

		$res = new MemModel($this->baseURI);
		while ($row = $recordSet->fetch()) {

			// subject
			$sub = new Resource($row[0]);

			// predicate
			$pred = new Resource($row[1]);

			// object
			$obj = new Literal($row[2], $row[3]);

			$statement = new Statement($sub, $pred, $obj);
			$res->add($statement);
		}
		$res->addParsedNamespaces($this->getParsedNamespaces());
		return $res;
	}


	/**
	* Create the dynamic part of an sql statement selecting triples with the
	* given parameters ($subject, $predicate, $object).
	*
	* @param	object Resource	$subject
	* @param	object Resource	$predicate
	* @param	object Node	$object
	* @return  string
	* @access	private
	*/
	function _createDynSqlPart_SPO($subject, $predicate, $object) {

		// conditions derived from the parameters passed to the function
		$sql = '';
		if ($subject != NULL)
		{
			$sLabel = $this->dbConn->quote($subject->getLabel());
			$sql .= ' AND "subject"=' . $sLabel;
		}
		if ($predicate != NULL)
		{
			$pLabel = $this->dbConn->quote($predicate->getLabel());
			$sql .= ' AND "predicate"=' .$pLabel;
		}
		if ($object != NULL) {
			if (is_a($object, 'Resource'))
			{
				$oLabel = $this->dbConn->quote($object->getLabel());
				$sql .= ' AND "object"=' .$oLabel;
			}
			else 
			{
				$quotedLiteral = $this->dbConn->quote($object->getLabel());
				$oLanguage = $this->dbConn->quote($object->getLanguage());
				$sql .= ' AND "object"=' .$quotedLiteral .'
                   AND "l_language"=' .$oLanguage;
			}
		}
		return $sql;
	}


	/**
	* Get a PDOStatement with array fields[] containing a representation of
	* the given DbModel stored in the table: statements, with an index corresponding
	* to following table columns:
	* [0] - subject, [1] - predicate, [2] - object, [3] - l_language,
	* (This method operates on data from a DbModel without loading it into a memory model
	*  in order to save resources and improve speed).
	*
	* @param	object DbModel	$DbModel
	* @return  object ADORecordSet
	* @access	private
	*/
	function _getRecordSet (&$dbModel) {

		$sql = 'SELECT "subject", "predicate", "object", "l_language"
           FROM "statements"
           WHERE "modelID" = ' .$dbModel->modelID;

		return $recordSet =& $this->dbConn->query($sql);
	}


	/**
	* Check if this DbModel contains the given row from the array fields[] of an ADORecordSet
	* The array index corresponds to following table columns:
	* [0] - subject, [1] - predicate, [2] - object, [3] - l_language,
	* [4] - l_datatype, [5] - subject_is, [6] - object_is
	*
	* @param   array  $row
	* @return  boolean
	* @access	private
	*/
	function _containsRow ($row) {

		$sql = 'SELECT modelID FROM statements
           WHERE modelID = ' .$this->modelID .'
           AND subject ='   .$this->dbConn->quote($row[0]) .'
           AND predicate =' .$this->dbConn->quote($row[1]) .'
           AND object ='    .$this->dbConn->quote($row[2]) .'
           AND l_language=' .$this->dbConn->quote($row[3]);

		$res = $this->dbConn->query($sql);
		if ($row = $res->fetch()){
			return true;
		}
		else{
			return false;
		}
	}

	/**
	* Returns the models namespaces.
	*
	* @author   Tobias Gauss <tobias.gauss@web.de>
	* @return mixed Array of key-value pairs. Namespace is the key,
	*               prefix the value. If no namespaces are found,
	*               boolean false is returned.
	*
	* @access   public
	*/
	function getParsedNamespaces(){
		$sql = 'SELECT * FROM "namespaces"
           WHERE "modelID" = ' .$this->modelID;
		$temp = false;
		$res  = $this->dbConn->query($sql);
		if($res !== false){
			while ($row = $res->fetch()) {
				$temp[$row[1]] = $row[2];
			}
		}
		return $temp;
	}



	/**
	* Adds the namespaces to the model. This method is called by
	* the parser. !!!! addParsedNamespaces() not overwrites manual
	* added namespaces in the model !!!!
	*
	* @author Tobias Gauss <tobias.gauss@web.de>
	* @param array $newNs Array of namespace => prefix assignments
	*
	* @access   public
	*/
	function addParsedNamespaces($newNs){
		if($newNs)
		foreach($newNs as $namespace => $prefix){
			$this->addNamespace($prefix, $namespace);
		}
	}


	/**
	* Adds a namespace and prefix to the model.
	*
	* @author   Tobias Gauss <tobias.gauss@web.de>
	* @param string $prefix Prefix
	* @param string $nmsp   Namespace URI
    *
	* @access   public
	*/
	function addNamespace($prefix,$nmsp){

		if($nmsp != '' && $prefix !=''){
			if($this->_checkNamespace($nmsp)){
				$sql = 'UPDATE "namespaces" SET "prefix"='.$this->dbConn->quote($prefix).' WHERE
				"modelID"='.$this->modelID.' AND "namespace"='.$this->dbConn->quote($nmsp);
			}else{
				$sql = 'INSERT INTO "namespaces"
                    ("modelID", "namespace", "prefix")
                    VALUES
                    (' .$this->modelID .','
				. $this->dbConn->quote($nmsp)   . ','
				. $this->dbConn->quote($prefix) . ')';
			}

			$rs = $this->dbConn->exec($sql);
			if ($rs === false){
				$errmsg = $this->dbConn->errorInfo();
				$errmsg = $errmsg[0];
				trigger_error($errmsg, E_USER_ERROR);	
			}
		}
	}

	/**
	* checks if a namespace is already in the model.
	*
	* @author   Tobias Gau�<tobias.gauss@web.de>
	* @access   private
	* @param    Array $newNs
	*/
	function _checkNamespace($nmsp){
		$res = true;
		$sql = 'SELECT * FROM "namespaces"
          	 WHERE "modelID" = ' .$this->modelID.' AND
			"namespace" = ' . $this->dbConn->quote($nmsp);
		$rs = $this->dbConn->exec($sql);
		if ($rs === false){
			$errmsg = $this->dbConn->errorInfo();
			$errmsg = $errmsg[0];
		}
		else{
			if($row = $rs->fetch()){
				$res = false;
			}
		}
		
		return $res;
	}

	/**
	* Returns a FindIterator for traversing the MemModel.
	* @access	public
	* @return	object	FindIterator
	*/
	function & iterFind($sub=null,$pred=null,$obj=null) {
		// Import Package Utility
		include_once(RDFAPI_INCLUDE_DIR.PACKAGE_UTILITY);

		$if = new IterFind($this,$sub,$pred,$obj);
		return $if;
	}

	/**
	* Removes a single namespace from the model
	*
	* @author Tobias Gauss <tobias.gauss@web.de>
	* @param string $nmsp Namespace URI
	*
	* @return mixed True if all went well, error message otherwise
	*
	* @access   public
	*/
	function removeNamespace($nmsp){

        $sql = 'DELETE FROM "namespaces"
           WHERE "modelID" = ' .$this->modelID.' AND "namespace"=' . $this->dbConn->quote($nmsp);

        $rs = $this->dbConn->exec($sql);
        if ($rs === false){
            $errmsg = $this->dbConn->errorInfo();
            $errmsg = $errmsg[0];
		}
        else {
            return true;
        }
	}




	/**
	* Add the given row from the array fields[] of an ADORecordSet to this DbModel
	* The array index corresponds to following table columns:
	* [0] - subject, [1] - predicate, [2] - object, [3] - l_language,
	* [4] - l_datatype, [5] - subject_is, [6] - object_is
	*
	* @param   array  $row
	* @throws  SqlError
	* @access	private
	*
	function _insertRow ($row) {

	$quotedObject = $this->dbConn->qstr($row[2]);
	$sql = "INSERT INTO statements VALUES
	(" .$this->modelID .","
	."'" .$row[0] ."',"
	."'" .$row[1] ."',"
	.""  .$quotedObject .","
	."'" .$row[3] ."',"
	."'" .$row[4] ."',"
	."'" .$row[5] ."',"
	."'" .$row[6] ."')";

	$rs =& $this->dbConn->execute($sql);
	if (!$rs)
	$this->dbConn->errorMsg();
	}
	*/

} // end: Class DbModel
?>
