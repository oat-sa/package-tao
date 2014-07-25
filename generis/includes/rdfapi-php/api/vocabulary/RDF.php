<?php
/**
*   Resource Description Framework (RDF) Vocabulary
*
*   @version $Id: RDF.php 431 2007-05-01 15:49:19Z cweiske $
*   @author Daniel Westphal (dawe@gmx.de)
*   @package vocabulary
*
*   Wrapper, defining resources for all terms of the
*   Resource Description Framework (RDF).
*   For details about RDF see: http://www.w3.org/RDF/.
*   Using the wrapper allows you to define all aspects of
*   the vocabulary in one spot, simplifing implementation and
*   maintainence.
*/


// RDF concepts (constants are defined in constants.php)
$RDF_Alt = new Resource(_RDF_NAMESPACE_URI . _RDF_ALT);
$RDF_Bag = new Resource(_RDF_NAMESPACE_URI . _RDF_BAG);
$RDF_Property = new Resource(_RDF_NAMESPACE_URI . _RDF_PROPERTY);
$RDF_Seq = new Resource(_RDF_NAMESPACE_URI . _RDF_SEQ);
$RDF_Statement = new Resource(_RDF_NAMESPACE_URI . _RDF_STATEMENT);
$RDF_List = new Resource(_RDF_NAMESPACE_URI . _RDF_LIST);
$RDF_nil = new Resource(_RDF_NAMESPACE_URI . _RDF_NIL);
$RDF_type = new Resource(_RDF_NAMESPACE_URI . _RDF_TYPE);
$RDF_rest = new Resource(_RDF_NAMESPACE_URI . _RDF_REST);
$RDF_first = new Resource(_RDF_NAMESPACE_URI . _RDF_FIRST);
$RDF_subject = new Resource(_RDF_NAMESPACE_URI . _RDF_SUBJECT);
$RDF_predicate = new Resource(_RDF_NAMESPACE_URI . _RDF_PREDICATE);
$RDF_object = new Resource(_RDF_NAMESPACE_URI . _RDF_OBJECT);
$RDF_Description = new Resource(_RDF_NAMESPACE_URI . _RDF_DESCRIPTION);
$RDF_ID = new Resource(_RDF_NAMESPACE_URI . _RDF_ID);
$RDF_about = new Resource(_RDF_NAMESPACE_URI . _RDF_ABOUT);
$RDF_aboutEach = new Resource(_RDF_NAMESPACE_URI . _RDF_ABOUT_EACH);
$RDF_aboutEachPrefix = new Resource(_RDF_NAMESPACE_URI . _RDF_ABOUT_EACH_PREFIX);
$RDF_bagID = new Resource(_RDF_NAMESPACE_URI . _RDF_BAG_ID);
$RDF_resource = new Resource(_RDF_NAMESPACE_URI . _RDF_RESOURCE);
$RDF_parseType = new Resource(_RDF_NAMESPACE_URI . _RDF_PARSE_TYPE);
$RDF_Literal = new Resource(_RDF_NAMESPACE_URI . _RDF_PARSE_TYPE_LITERAL);
$RDF_Resource = new Resource(_RDF_NAMESPACE_URI . _RDF_PARSE_TYPE_RESOURCE);
$RDF_li = new Resource(_RDF_NAMESPACE_URI . _RDF_LI);
$RDF_nodeID = new Resource(_RDF_NAMESPACE_URI . _RDF_NODEID);
$RDF_datatype = new Resource(_RDF_NAMESPACE_URI . _RDF_DATATYPE);
$RDF_seeAlso = new Resource(_RDF_NAMESPACE_URI . _RDF_SEEALSO);



?>