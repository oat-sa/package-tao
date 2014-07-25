<?php
/**
*   Resource Description Framework (RDF) Vocabulary (Resource)
*
*   @version $Id: RDF_C.php 431 2007-05-01 15:49:19Z cweiske $
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
class RDF{

	// RDF concepts (constants are defined in constants.php)
	function ALT()
	{
		return  new Resource(_RDF_NAMESPACE_URI . _RDF_ALT);

	}

	function BAG()
	{
		return  new Resource(_RDF_NAMESPACE_URI . _RDF_BAG);

	}

	function PROPERTY()
	{
		return  new Resource(_RDF_NAMESPACE_URI . _RDF_PROPERTY);

	}

	function SEQ()
	{
		return  new Resource(_RDF_NAMESPACE_URI . _RDF_SEQ);

	}

	function STATEMENT()
	{
		return  new Resource(_RDF_NAMESPACE_URI . _RDF_STATEMENT);

	}

	function _RDF_LIST()
	{
		return  new Resource(_RDF_NAMESPACE_URI . _RDF_LIST);

	}

	function NIL()
	{
		return  new Resource(_RDF_NAMESPACE_URI . _RDF_NIL);

	}

	function TYPE()
	{
		return  new Resource(_RDF_NAMESPACE_URI . _RDF_TYPE);

	}

	function REST()
	{
		return  new Resource(_RDF_NAMESPACE_URI . _RDF_REST);

	}

	function FIRST()
	{
		return  new Resource(_RDF_NAMESPACE_URI . _RDF_FIRST);

	}

	function SUBJECT()
	{
		return  new Resource(_RDF_NAMESPACE_URI . _RDF_SUBJECT);

	}

	function PREDICATE()
	{
		return  new Resource(_RDF_NAMESPACE_URI . _RDF_PREDICATE);

	}

	function OBJECT()
	{
		return  new Resource(_RDF_NAMESPACE_URI . _RDF_OBJECT);

	}

	function DESCRIPTION()
	{
		return  new Resource(_RDF_NAMESPACE_URI . _RDF_DESCRIPTION);

	}

	function ID()
	{
		return  new Resource(_RDF_NAMESPACE_URI . _RDF_ID);

	}

	function ABOUT()
	{
		return  new Resource(_RDF_NAMESPACE_URI . _RDF_ABOUT);

	}

	function ABOUT_EACH()
	{
		return  new Resource(_RDF_NAMESPACE_URI . _RDF_ABOUT_EACH);

	}

	function ABOUT_EACH_PREFIX()
	{
		return  new Resource(_RDF_NAMESPACE_URI . _RDF_ABOUT_EACH_PREFIX);

	}

	function BAG_ID()
	{
		return  new Resource(_RDF_NAMESPACE_URI . _RDF_BAG_ID);

	}

	function RESOURCE()
	{
		return  new Resource(_RDF_NAMESPACE_URI . _RDF_RESOURCE);

	}

	function PARSE_TYPE()
	{
		return  new Resource(_RDF_NAMESPACE_URI . _RDF_PARSE_TYPE);

	}

	function LITERAL()
	{
		return  new Resource(_RDF_NAMESPACE_URI . _RDF_PARSE_TYPE_LITERAL);

	}

	function PARSE_TYPE_RESOURCE()
	{
		return  new Resource(_RDF_NAMESPACE_URI . _RDF_PARSE_TYPE_RESOURCE);

	}

	function LI()
	{
		return  new Resource(_RDF_NAMESPACE_URI . _RDF_LI);

	}

	function NODE_ID()
	{
		return  new Resource(_RDF_NAMESPACE_URI . _RDF_NODEID);

	}

	function DATATYPE()
	{
		return  new Resource(_RDF_NAMESPACE_URI . _RDF_DATATYPE);

	}

	function SEE_ALSO()
	{
		return  new Resource(_RDF_NAMESPACE_URI . _RDF_SEEALSO);
	}
}


?>