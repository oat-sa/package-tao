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
 * Classe permettant de tester les donn�es des variables
 * 
 * PHP version 5
 * 
 * @author Luc DEHAND
 */
class TestVariables 
{
	/**
 	 * Tableau contenant les erreurs d�tect�es
	 **/
  	private $errTab;

	/**
	 * Variable contenant le nombre d'erreurs d�tect�es 
	 */
  	private $nombre; 

	/**
	 * Nom du style CSS par d�faut quand il y a une erreur 
	 */
  	private $style; 

	/**
	 * Message d'erreur 
	 */
	private $message;


	/**
	 * Constructeur de la classe
	 * @param 	string 	$pStyle 	Style par d�faut (en cas d'erreur)
	 */
	function __construct($pStyle)
	{
	    $this->nombre 		= 0;
	    $this->style		= $pStyle;
	    $this->errTab 		= array();
	    $this->message		= "";
	 }
	 
	 /**
	  * Retourne le nombre d'erreurs
	  * @return integer 			Le nombre d'erreur
	  */
	 function getNombre()
	 {
	 	return $this->nombre;
	 }
	 
	 /**
	  * Retourne le message d'erreur
	  * @return string 				Le message d'erreur
	  */
	function getMessage()
	{
		return $this->message;
	}
	
	/**
	 * Ajoute un message d'erreur
	 * @param 	string 	$pMessage 	Message d'erreur
	 */
	function setMessage($pMessage)
	{
		$this->message = $pMessage;
	}

	/**
	 * Fonction qui permet de securiser un champ de mani�re � pouvoir �tre stock� dans une base de donn�es.
	 * Si vous voulez autoriser les tag HTML alors il faut mettre la ligne du strip_tags en commentaire.
	 * @param 	string 	$pVar 		Valeur de la variable
	 * @param 	string 	$pName 		Nom de la variable
	 * @return 	string 				La variable apr�s passage dans la moulinette pour �tre enregistr� dans la base de donn�es
	 */
	function secure($pVar, $pName, $pStatic=false)
	{	
		if (!is_numeric($pVar) && !is_array($pVar))
		{
			// stripslashes =  Supprime les anti-slash d'une cha�ne
			// htmlentities =  Convertit tous les caract�res �ligibles en entit�s HTML
			$pVar = stripslashes(htmlentities($pVar));
			// trim =  Supprime les espaces en d�but et fin de cha�ne
			$pVar = trim($pVar);
			//$pVar=strip_tags($pVar);
		}
		// on ajoute la variable et sa valeur dans le tableau des variables
		$this->add($pVar, $pName);
	    
		return $pVar;
	 }
	 
	 static function secureStatic($pVar) 
	 {
	 	if (!is_numeric($pVar) && !is_array($pVar))
		{
			// stripslashes =  Supprime les anti-slash d'une cha�ne
			// htmlentities =  Convertit tous les caract�res �ligibles en entit�s HTML
			$pVar = stripslashes(htmlentities($pVar));
			// trim =  Supprime les espaces en d�but et fin de cha�ne
			$pVar = trim($pVar);
			//$pVar=strip_tags($pVar);
		}
	    
		return $pVar;
	 }
	
	/**
  	 * Fonction qui ajoute la valeur et un style (vide) � un nom de champ de formulaire
  	 * @param 	string 	$pVar 		Valeur de la variable
  	 * @param 	string 	$pName 		Nom de la variable
  	 * @param 	string 	$pStyle 	Style d'erreur
  	 */
	function add($pVar, $pName, $pStyle = "")
  	{
		// ajout de la valeur � la variable
		$this->errTab[$pName][0] = $pVar;
		// associe un style � la variable
		$this->errTab[$pName][1] = $pStyle;
  	}

	/**
	 * Fonction qui teste si le champ n'est pas nul (vide)
	 * @param 	string 	$pVar 		Valeur de la variable
  	 * @param 	string 	$pName 		Nom de la variable
	 * @return 	boolean 			Vrai si la variable n'est pas vide / Faux si la variable est vide
	 */
	 function isNotEmpty($pVar, $pName)
	 {
	 	if (strlen(trim($pVar)) == 0)
	 	{
			$this->add($pVar, $pName, $this->style);
			$this->nombre++;
			return false;
		}    
		return true;
	 }

	/**
  	 * Fonction qui permet d'effectuer un test sur un champ de type EMAIL
  	 * @param 	string 	$pVar 		Valeur de la variable
  	 * @param 	string 	$pName 		Nom de la variable
  	 * @return 	boolean 			Vrai si la variable est bien de type EMAIL / Faux si la variable n'est pas de type EMAIL
	 */
  	function mailtest($pVar, $pName)
  	{
		if (($pVar != "") 
		&& !eregi("^[_a-z0-9-]+(\\.[_a-z0-9-]+)*@([0-9a-z](-?[0-9a-z])*\\.)+[a-z]{2}([zmuvtg]|fo|me)?$", $pVar))
		{
			$this->add($pVar, $pName, $this->style);
			$this->nombre++;
			return false;
		}
		return true;
  	}

	/**
  	 * Fonction qui permet d'effectuer un test sur un champ de type INT
  	 * @param 	string 	$pVar 		Valeur de la variable
  	 * @param 	string 	$pName 		Nom de la variable
  	 * @return 	boolean 			Vrai si la variable est bien de type INT / Faux si la variable n'est pas de type INT
	 */
  	function inttest($pVar, $pName)
  	{
		if (($pVar != "") && ((!is_numeric($pVar)) || (strpos($pVar, '.') != FALSE) || (strpos($pVar, ',') != FALSE)))
		{
			$this->add($pVar, $pName, $this->style);
			$this->nombre++;
			return false;
		}
		return true;
  	}
  	
  	/**
  	 * Fonction qui permet d'effectuer un test sur un champ de type FLOAT
  	 * @param 	string 	$pVar 		Valeur de la variable
  	 * @param 	string 	$pName 		Nom de la variable
  	 * @return 	boolean 			Vrai si la variable est bien de type FLOAT / Faux si la variable n'est pas de type FLOAT
	 */
  	function floattest($pVar, $pName)
  	{
		if (($pVar != "") && (!is_numeric($pVar)))
		{
			$this->add($pVar, $pName, $this->style);
			$this->nombre++;
			return false;
		}    
		return true;
  	}

	/**
  	 * Fonction qui permet d'effectuer un test sur un champ de type TIME
  	 * @param 	integer 	$pVarh 		Valeur de la variable (heures)
  	 * @param 	integer 	$pVarm 		Valeur de la variable (minutes)
  	 * @param 	integer 	$pVars 		Valeur de la variable (secondes)
  	 * @param 	string 		$pName 		Nom de la variable
  	 * @return 	boolean 				Vrai si la variable est bien de type TIME / Faux si la variable n'est pas de type TIME
	 */
  	function timetest($pVarh, $pVarm, $pVars, $pName)
  	{
  		if (!(($pVarh == "") && ($pVarm == "") && ($pVars == "")))
  		{
			if (($pVarh == "") || ($pVarm == "") ||
			    (!is_numeric($pVarh)) || (!is_numeric($pVarm)) || (!is_numeric($pVars)) ||
	    		    ($pVarh > 24) || ($pVarm > 59) || ($pVars > 59) || 
	    		    ($pVarh < 0) || ($pVarm < 0) || ($pVars < 0))
			{
				$this->add(0, $pName, $this->style);
				$this->nombre++;
				return false;
			}
  		}
  		return true;
  	}

	/**
  	 * Fonction qui permet d'effectuer un test sur un champ de type URL
  	 * @param 	string 	$pVar 		Valeur de la variable
  	 * @param 	string 	$pName 		Nom de la variable
  	 * @return 	boolean 			Vrai si la variable est bien de type URL / Faux si la variable n'est pas de type URL
	 */
  	function urltest($pVar, $pName)
  	{
		if (($pVar != "") && ($pVar != "http://") 
		&& (substr($pVar, 0, 4) != "www.") && (substr($pVar, 0, 7) != "http://"))
		{
			$this->add($pVar, $pName, $this->style);
			$this->nombre++;
			return false;
		}    
		return true;
  	}
    
	/**
  	 * Fonction qui permet d'effectuer un test sur un champ de type DATE
  	 * @param 	integer 	$pVar1 		Valeur de la variable (jour)
  	 * @param 	integer 	$pVar2 		Valeur de la variable (mois)
  	 * @param 	integer 	$pVar3 		Valeur de la variable (ann�e)
  	 * @param 	integer 	$pName 		Nom de la variable
  	 * @return 	boolean 				Vrai si la variable est bien de type DATE / Faux si la variable n'est pas de type DATE
	 */
  	function datetest($pVar1, $pVar2, $pVar3, $pName)
  	{
  		if (!(($pVar1 == "") && ($pVar2 == "") && ($pVar3 == "")))
  		{
			if (($pVar1 == "") || ($pVar2 == "") || ($pVar3 == "") ||
			    (!is_numeric($pVar1)) || (!is_numeric($pVar2)) || (!is_numeric($pVar3)) ||
	    		    (!checkdate($pVar2, $pVar1, $pVar3)))
			{
				$this->add(0, $pName, $this->style);
				$this->nombre++;
				return false;
			}    
  		}
  		return true;
  	}

	/**
  	 * Fonction qui recherche un style CSS associ� � un champ donn�
  	 * @param 	string 	$pName 		Nom de la variable
  	 * @param 	string 	$pDefault 	Style par d�faut
  	 * @return 	boolean 			Retourne le style associ� au nom $pName
	 */
  	function fieldError($pName, $pDefault = "")
  	{
		if (isset($this->errTab[$pName]))
		{
			return ($this->errTab[$pName][1]);
		}
		return $pDefault;
	}
	
	/**
	 * Fonction qui recherche la valeur associ�e � un champ donn�
	 * @param 	string 	$pName 			Nom de la variable
  	 * @param 	string 	$pDefault 		Valeur par d�faut
  	 * @return 	boolean 				Retourne la valeur associ�e au nom $pName
	 */
  	function fieldValue($pName, $pDefault = "")
  	{
		if (isset($this->errTab[$pName]))
		{
			return ($this->errTab[$pName][0]);
		}
		return $pDefault;
	}
}
?>