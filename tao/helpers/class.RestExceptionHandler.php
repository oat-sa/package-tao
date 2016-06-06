<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ExceptionHandler
 *
 * @author plichart
 */
class tao_helpers_RestExceptionHandler extends common_exception_ExceptionHandler{
   
    public function handle(common_Exception $exception){
	
	switch (get_class($exception)) {
	
	case "common_exception_BadRequest":{
	    header("HTTP/1.0 400 Bad Request" );break;
	}
	case "common_exception_MissingParameter":{
	    header("HTTP/1.0 400 Bad Request" );break;
	}
	case "common_exception_InvalidArgumentType":{
	    header("HTTP/1.0 400 Bad Request" );break;
	}
	case "common_exception_NotAcceptable":{
	    header("HTTP/1.0 406 Not Acceptable" );break;
	}
	case "common_exception_Unauthorized":{
	    header("HTTP/1.0 401 Unauthorized" );break;
	}
	case "common_exception_NotFound":{
	    header("HTTP/1.0 404 Not Found" );break;
	}
	case "common_exception_MethodNotAllowed":{
	    header("HTTP/1.0 405 Not Found" );break;
	}
	case "common_exception_NotAcceptable":{
	    header("HTTP/1.0 406 Not Acceptable" );break;
	}
	case "common_exception_TimeOut":{
	    header("HTTP/1.0 408 Request Timeout" );break;
	}
	case "common_exception_Conflict":{
	    header("HTTP/1.0 409 Conflict" );break;
	}
	case "common_exception_UnsupportedMediaType":{
	    header("HTTP/1.0 415 Unsupported Media Type" );break;
	}
	case "common_exception_NotImplemented":{
	    header("HTTP/1.0 501 Not Implemented" );break;
	}
	case "common_exception_PreConditionFailure":{
	    header("HTTP/1.0 412 Precondition Failed" );break;
	}
	case "common_exception_NoContent":{
	    header("HTTP/1.0 204 No Content" );break;
	}
	//throw this one
	case "common_exception_teapotAprilFirst":{
	    header("HTTP/1.0 418 I'm a teapot (RFC 2324)" );break;
	}
	default: {
	    header("HTTP/1.0 500 Internal Server Error" );
	}
	}
    }
}

?>
