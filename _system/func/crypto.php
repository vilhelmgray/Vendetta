<?php
/***********************************************************************
    crypto.php provides a hashing function.
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

require_once 'parser.php';

/*****************************************************************
  Description: creates a salted hash
  Arguments: $string - string to be hashed
	     $method - hashing method (SHA1, SHA256, SHA512 etc)
  	     $salt - primary salt (random salt will be generated
	             if set to NULL)
	     $pepper - secondary salt (random salt will be
	               generated if set to NULL)
	     $cycles - number of times to hash
	     $hsec - number of sections to split hash
	     $ssec - number of sections to split salt
	     $psec - number of sections to split pepper
  Returns: array containing hash, salt, and pepper, in
           that order
  On Error: returns array containing the error
*****************************************************************/
function crypto($string, $method, $salt=NULL, $pepper=NULL, $cycles = 1, $hsec=2, $ssec=2, $psec=2){
	set_error_handler('handleErrors');
	if($cycles < 1)
		return array('Warning: Set to perform less than 1 cycle');

	if(empty($salt) && strcmp($salt,"0")){
		$salt = randomString(32, false);
	}
	if(empty($pepper) && strcmp($pepper,"0")){
		$pepper = randomString(32, false);
	}
	
	$ssplit = splitString($salt, $ssec);
	$psplit = splitString($pepper, $psec);
	
	$hash = $string;
	for($y = 0; $y < $cycles; $y++)
	{
		$hsplit = splitString(hash($method, $hash), $hsec);
		for($x=0;isset($ssplit[$x+1])||isset($psplit[$x+1])||isset($hsplit[$x+1]);$x+=2){
			$soup = '';
			if(isset($ssplit[$x])){
				$soup .= $ssplit[$x];
			}
			if(isset($hsplit[$x+1])){
				$soup .= $hsplit[$x+1];
			}
			if(isset($psplit[$x])){
				$soup .= $psplit[$x];
			}
			if(isset($ssplit[$x+1])){
				$soup .= $ssplit[$x+1];
			}
			if(isset($hsplit[$x])){
				$soup .= $hsplit[$x];
			}
			if(isset($psplit[$x+1])){
				$soup .= $psplit[$x+1];
			}
		}
		$hash = hash($method,$soup);
	}
	return array($hash, $salt, $pepper);
}

/*****************************************************************
  Description:	creates a random string of characters
  Arguments: $length - size of string
             $strchars - when false, the characters ", ', and \
                         will not be in the returned string
  Return: random string of size $length
*****************************************************************/
function randomString($length = 32, $strchars = true){
	$randstr = '';
	for($x = 0; $x < $length; $x++){
		do{
			$char = chr(mt_rand(33,126));
		}while(($strchars == false) && ($char == chr(34) || $char == chr(39) || $char == chr(92)));
		$randstr .= $char;
	}
	return $randstr;
}
	

// Handle Errors	
function handleErrors($errno, $errstr, $errfile, $errline){
	switch($errno){
		case E_NOTICE:
		case E_USER_NOTICE:
			die('Notice: '.$errstr.' in file '.$errfile.' on line '.$errline);
			break;
		case E_WARNING:
		case E_USER_WARNING:
			die('Warning: '.$errstr.' in file '.$errfile.' on line '.$errline);
			break;
		case E_ERROR:
		case E_USER_ERROR:
			die('Error: '.$errstr.' in file '.$errfile.' on line '.$errline);
			break;
		default:
			die('Unknown error: '.$errstr.' in file '.$errfile.' on line '.$errline);
			break;
	}
	return true;
}
?>
