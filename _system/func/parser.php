<?php
/***********************************************************************
    parser.php modifies strings and syntactic structures.
    Copyright (C) 2010  William Breathitt Gray
************************************************************************
    This file is part of Vendetta.

    Vendetta is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as
    published by the Free Software Foundation, either version 3 of the
    License, or (at your option) any later version.

    Vendetta is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public
    License along with Vendetta.  If not, see
    <http://www.gnu.org/licenses/>.
************************************************************************
***********************************************************************/
require_once 'file.php';

/*****************************************************************
  Description: converts characters to HTML entities
  Arguments: $str - string or file path
	     $path - determine if $str is a file path
	     $style - determine whether to convert quotes
		      (0=leave all alone,
		       1=convert double leave single alone,
		       2=convert single leave double alone,
		       3=convert all)
	     $decode - convert HTML entities to characters
  Return: converted string
*****************************************************************/
function purifyhtml($str, $path=false, $style=3, $decode=false){
	if($path){
		$str = loadFile($str);
	}
	if($decode){
		$constr = html_entity_decode($str, $style);
	}else{
		$constr = htmlentities($str, $style);
	}
	return $constr;
}

/*****************************************************************
  Description: converts PHP form to HTML entities
  Arguments: $str - string or file path
             $path - determine if $str is a file path
             $decode - convert HTML entities to PHP form
  Return: converted string
*****************************************************************/
function purifyPHP($str, $path=false, $decode=false){
	//create and sort arrays
	$php = array('<?','?>','<%','%>');
	$html = array('&lt;?','?&gt;','&lt;%','%&gt;');
	ksort($php);
	ksort($html);
	//load file
	if($path){
		$str = loadFile($str);
	}
	//configure replacement
	if($decode){
                $patterns = $html;
		$replacements = $php;
	}else{
		$patterns = $php;		
		$replacements = $html;
	}
	//prepare regular expressions
	foreach($patterns as &$element){
		$element = '/'.preg_quote($element).'/';
	}
	unset($element);
	//perform replacements
	$constr = preg_replace($patterns,$replacements,$str);
	return $constr;
}

/*****************************************************************
  Description:  splits a string into equal sections
  Arguments: $str - string
             $sections - number of sections string will be split
  Return: array containing each section of the original string
	  (if $sections is less than 2 or greater than the length
	  of the string, the original string will be returned)
*****************************************************************/
function splitString($str, $sections = 2){
        if(($sections < 2) || ($sections > strlen($str))){
                return array($str);
        }
        $box = array();
        $interval = (int)(strlen($str)/$sections);
        for($x = 0; $x < $sections; $x++){
                if($x == ($sections - 1)){
                        $piece = substr($str, ($x * $interval));
                }else{
                        $piece = substr($str, ($x * $interval), $interval);
                }
                array_push($box, $piece);
        }
        return $box;
}
?>
