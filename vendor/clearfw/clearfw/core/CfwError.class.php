<?php
/*  
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
 * Copyright (c) 2006-2009 (original work) Public Research Centre Henri Tudor (under the project FP6-IST-PALETTE);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php
# ***** BEGIN LICENSE BLOCK *****
# This file is part of "ClearFw".
# Copyright (c) 2007 CRP Henri Tudor and contributors.
# All rights reserved.
#
# "ClearFw" is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License version 2 as published by
# the Free Software Foundation.
#
# "ClearFw" is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with "ClearFw"; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
# ***** END LICENSE BLOCK *****
/**
 * error handling
 * FIXME: refactoring necessaire avec la classe TestVariables
 */
class CfwError {

	/**
	 * This is the instance of this singleton class
	 */
	private static $instance;

	/**
	 *
	 */
	private $nbError;

	/**
	 * Error message
	 */
	private $message;

	/**
	 * Style
	 */
	 private $style;

	/**
	 * Exception
	 */
	 private $exceptionClass;

	/**
	 *
	 */
	 private $variablesTest;

	/**
	 * Constructor
	 */
    function __construct() {
    	$this->init();
    }

    /**
     * Initialisation
     */
    function init() {
    	$this->nbError			= 0;
    	$this->message			= '';
    	$this->variablesTest	= array();
    	$this->style			= 'error';
    }

    /**
     *
     */
    public static function getInstance() {
    	if (!isset(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
    }

    /**
     * Save error class in session
     */
    public function saveSession() {
    	$_SESSION['error']	= $this;
    }

    /**
     * Delete error class session
     */
    public function deleteSession() {
    	unset($_SESSION['error']);
    }

    /**
     * Load error class session
     */
    public function loadSession() {
    	if (isset($_SESSION['error'])) {
    		$this->nbError			= $_SESSION['error']->getNbError();
    		$this->variablesTest	= $_SESSION['error']->getVariablesTest();
    		$this->message			= $_SESSION['error']->getMessage();
    		$this->exceptionClass	= $_SESSION['error']->getExceptionClass();
    	}
    }

    /**
     * Modify message
     * @param	string		$pMessage		message
     */
    public function setMessage($pMessage) {
    	$this->message	= $pMessage;
    }

    /**
     * Returns message
     * @return string 						message
     */
    public function getMessage() {
    	return $this->message;
    }

    /**
     * Return variablesTest
     * @return	array
     */
    public function getVariablesTest() {
    	return $this->variablesTest;
    }

    /**
     * @return	Exception				Exception class
     */
    public function getExceptionClass() {
    	return $this->exceptionClass;
    }

    /**
     *
     */
     public function setExceptionClass($pException) {
     	$this->exceptionClass	= $pException;
     }

    /**
     * Modify
     * @param	string		$pNbError
     */
    public function setNbError($pNbError) {
    	$this->nbError	= $pNbError;
    }

    /**
     * Returns
     * @return integer
     */
    public function getNbError() {
    	return $this->nbError;
    }

    /**
     *
     */
    public function add($pValue, $pName, $pMessage = "", $pStyle = "", $i = "") {
    	if ($i === "") {
    		$this->variablesTest[$pName]['value']		= $pValue;
    		$this->variablesTest[$pName]['style']		= $pStyle;
    	} else {
    		if (isset($this->variablesTest[$pName]['value']) 
    		&& !is_array($this->variablesTest[$pName]['value'])) {
    			throw new Exception('[Error::add] '. $pName . ' is not an array !');
    		}
    		
    		$this->variablesTest[$pName]['value'][$i]	= $pValue;
    		$this->variablesTest[$pName]['style'][$i]	= $pStyle;
    	}
    	$this->message						   .= $pMessage;
    }

    /**
     * @param	string		$pName
     */
    public function delete($pName) {
    	if (isset($this->variablesTest[$pName])) {
    		unset($this->variablesTest[$pName]);
    	}
    }

    /**
	 * Fonction qui recherche un style CSS associé à un champ donné.
	 * @param 	string		$pName 		Nom de la variable
	 */
  	function fieldError($pName, $i = "") {
    	if (isset($this->variablesTest[$pName])) {
      		if ($i === "") {
      			return ($this->variablesTest[$pName]['style']);
      		} else {
      			return ($this->variablesTest[$pName]['style'][$i]);
      		}
    	}
    	return "";
	}

	/**
	 * Fonction qui recherche une valeur
	 * @param 	string		$pName 		Nom de la variable
	 */
  	function fieldValue($pName, $i = "")	{
    	if (isset($this->variablesTest[$pName])) {
      		if ($i === "") {
      			return ($this->variablesTest[$pName]['value']);
      		} else {
      			return ($this->variablesTest[$pName]['value'][$i]);
      		}
    	}
    	return "";
	}

    /**
     * Secure a variable
     * @param	string		$pValue			Variable value
     * @param	string		$pName			Variable name
     * @return	string						Variable secure
     */
    function secure($pValue, $pName) {
    	if (!is_numeric($pValue) && !is_array($pValue)) {
    		# stripslashes =  Un-quote string
			# htmlentities =  Convert all applicable characters to HTML entities
			$pValue 	= stripslashes(htmlspecialchars($pValue));

			# trim =  Strip whitespace (or other characters) from the beginning and end of a string
			$pValue 	= trim($pValue);
			#$pValue	=strip_tags($pValue);
    	} else if (is_array($pValue)) {
    		# TODO sécuriser chaque champs
    	}

    	$this->add($pValue, $pName);

    	return $pValue;
    }

    /**
     * Vérifie qu'une valeur n'est pas vide
     */
    function isNotEmpty($pValue, $pName, $pMessage = "", $i = "") {
    	if (strlen(trim($pValue)) == 0) {
      		$this->add($pValue, $pName, $pMessage, $this->style, $i);
      		$this->nbError++;
    	}
  	}

  	/**
  	 * Function which verify if variable is a date
  	 * @param	date	$pDate			Date
  	 * @param	string	$pName			Name
  	 * @param	string	$pMessage		Message if $pDate is not a date
  	 */
  	function datetest($pDate, $pName, $pMessage = "", $i = "") {
  		if ($pDate != "") {
	    	$exp	= explode('/', $pDate);
	    	$var1	= isset($exp[0])?$exp[0]:"";
	    	$var2	= isset($exp[1])?$exp[1]:"";
	    	$var3	= isset($exp[2])?$exp[2]:"";

	    	if (($var1 == "") || ($var2 == "") || ($var3 == "") ||
	    		(!is_numeric($var1)) || (!is_numeric($var2)) || (!is_numeric($var3)) ||
	    		(!checkdate($var2,$var1,$var3))) {
	      		$this->add($pDate, $pName, $pMessage, $this->style, $i);
	      		$this->nbError++;
	      	}
  		}
  	}

  	/**
  	 * Vérifie qu'une variable est de type mail
  	 * @param	string		$pEmail				Email
  	 * @param	string		$pName				Nom du champ du formulaire
  	 * @param	string		$pMessage			Message
  	 */
  	function mailtest($pEmail, $pName, $pMessage = "", $i = "") {
  		if (($pEmail != "") && !eregi("^[_a-z0-9-]+(\\.[_a-z0-9-]+)*@([0-9a-z](-?[0-9a-z])*\\.)+[a-z]{2}([zmuvtg]|fo|me)?$", $pEmail)) {
      		$this->add($pEmail, $pName, $pMessage, $this->style, $i);
      		$this->nbError++;
    	}
  	}

  	/**
  	 * Vérifie qu'une variable est de type url
  	 * @param	string		$pUrl				Url
  	 * @param	string		$pName				Nom du champ du formulaire
  	 * @param	string		$pMessage			Message
  	 */
  	function urltest($pUrl, $pName, $pMessage = "", $i = "") {
  		if ((!empty($pUrl)) && ($pUrl != "http://") && (substr($pUrl, 0, 4) != "www.") && (substr($pUrl, 0, 7) != "http://")) {
      		$this->add($pUrl, $pName, $pMessage, $this->style, $i);
      		$this->nbError++;
    	}
  	}

  	/**
  	 * Vérifie que la variable passée en paramètre est de type image
  	 * @param string $pImage nom de l'image
  	 * @param string $pName nom du label associé
  	 * @param string $pMessage message
  	 */
  	function imageTest($pImage, $pName, $pMessage = "", $i = "") {
  		$extension = files::getExtension($pImage);
		if (!($extension == 'jpg' || $extension == 'gif' || $extension == 'png')) {
			$this->add($pImage, $pName, $pMessage, $this->style, $i);
			$this->nbError++;
		}
  	}
  	
  	
  	/**
  	 * Vérifie qu'une variable est de type Time
  	 * @param			$pHour			Heure
  	 * @param			$pMinute		Minute
  	 * @param			$pName			Nom du champ du formulaire
  	 * @param			$pMessage		Message
  	 */
  	 function timetest($pHour, $pMinute, $pName, $pMessage = "", $i = "") {
  	 	if ($pHour == "" && $pMinute == "") {
  	 		return false;
  	 	}
  	 	
  	 	if ($pHour == "") {
  	 		$pHour	= 0;
  	 	}
  	 	
  	 	if ($pMinute == "") {
  	 		$pMinute = 0;
  	 	}
  	 	
  	 	if (!is_numeric($pHour) || $pHour < 0 || $pHour > 24) {
  	 		$this->add("", $pName, $pMessage, $this->style, $i);
      		$this->nbError++;
  	 	}
  	 	
  	 	if (!is_numeric($pMinute) || $pMinute < 0 || $pMinute > 60) {
  	 		$this->add("", $pName, $pMessage, $this->style, $i);
      		$this->nbError++;
  	 	}
  	 	return true;
  	 }
  	 
  	/**
  	 * Vérifie qu'une variable est de type int
  	 * @param	string		$pInt				Int
  	 * @param	string		$pName				Nom du champ du formulaire
  	 * @param	string		$pMessage			Message
  	 */
  	function inttest($pInt, $pName, $pMessage = "", $i = "") {
  		if ((!empty($pInt)) && (!is_numeric($pInt))) {
      		$this->add($pInt, $pName, $pMessage, $this->style, $i);
      		$this->nbError++;
    	}
  	}
}
?>