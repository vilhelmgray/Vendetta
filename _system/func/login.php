<?php
/***********************************************************************
    login.php controls the log in and log out of users.
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
require_once 'crypto.php';

/*****************************************************************
  Description: logs in a user to the system
  Arguments: $user - username
	     $pass - password
	     $dbname - MySQL database name
	     $sqlhost - MySQL database hostname
	     $sqluser - MySQL database username
	     $sqlpass - MySQL database password
	     $hash - array containing settings for crypto
  Return: array containing up to three values -
	  1. boolean true if successful, boolean false if not
	  2. boolean true if error occurred, boolean false if not
	  3. string containing an explanation of the failure
*****************************************************************/
function login($user, $pass, $dbname, $sqlhost, $sqluser, $sqlpass, $hash){
	$login = array(false, true); //set default values
	//connect to database
	if(!($link = mysql_connect($sqlhost, $sqluser, $sqlpass))){
		//if an error occurred
		array_push($login, mysql_error());
		mysql_close($link);
		return $login;
	}
	//connect to database
	if(!mysql_select_db($dbname)){
		//if an error occurred
		array_push($login, mysql_error());
		mysql_close($link);
		return $login;
	}
	//retrieve user data
	if($sql = mysql_query('SELECT user,pepper,password FROM _users WHERE user="'.$user.'"')){
		$login[1] = false; //there were no errors	
		$row = mysql_fetch_row($sql);
		mysql_close($link);
		if(empty($row[0])){ //if user was not found
			array_push($login, 'The username entered was not found.');
			return $login;
		}
		//check password
		$password = crypto($pass, $hash[0], $hash[1], $row[1], $hash[2],$hash[3], $hash[4], $hash[5]);
		if(!isset($password[1])){ //if an error occurred
			array_push($login, $password[0]);
			return $login;
		}
		$password[0] = hash('sha256', $password[0]);
		if(strcmp($password[0],$row[2])){ //if password is incorrect
			array_push($login, 'The password entered was incorrect.');
			return $login;
		}
		//login was successful
		$login[0] = true;
		return $login;
	}
	//if an error occurred
	array_push($login, mysql_error());
	mysql_close($link);
	return $login;
}

/*****************************************************************
  Description: creates a new session and sets browser cookie
  Arguments: $user - username
	     $expire - expiration date in seconds for session
	     $dbname - MySQL database name
	     $sqlhost - MySQL database hostname
	     $sqluser - MySQL database username
	     $sqlpass - MySQL database password
  Return: array containing up to three values -
          1. boolean true if successful, boolean false if not
          3. string containing an explanation of the failure
*****************************************************************/
function makeSession($user, $expire, $dbname, $sqlhost, $sqluser, $sqlpass){
	$session = array(false); //set default values
	//connect to database
	if(!($link = mysql_connect($sqlhost, $sqluser, $sqlpass))){
		//if an error occurred
                array_push($session, mysql_error());
                mysql_close($link);
                return $session;
        }
        //connect to database
        if(!mysql_select_db($dbname)){
		//if an error occurred
                array_push($session, mysql_error());
                mysql_close($link);
                return $session;
        }
	//save last login information
	if(!mysql_query('UPDATE _users SET lastLogin = login, lastIP = ip WHERE user = "'.$user.'"')){
		//if an error occurred
		array_push($session, mysql_error());
                mysql_close($link);
                return $session;
	}
	//record login time, set expiration, and log IP
	$now = time();
	$expire = date('Y-m-d H:i:s', $now + $expire); //adjust to DATETIME format
	$now = date('Y-m-d H:i:s', $now); //adjust to DATETIME format
	$ip = sprintf("%u", ip2long($_SERVER['REMOTE_ADDR'])); //save IP as an unsigned integer
	if(!mysql_query('UPDATE _users SET login = "'.$now.'", expire = "'.$expire.'", ip = "'.$ip.'" WHERE user = "'.$user.'"')){
		//if an error occurred
		array_push($session, mysql_error());
                mysql_close($link);
                return $session;
	}
	//create session and set cookie
	do{
		$sid = hash('SHA1', randomString()); //create random SHA1 hash
		//see if session ID is already in use
		if(!($sql = mysql_query('SELECT session FROM _users WHERE session = "'.$sid.'"'))){
			//if there was an error
			array_push($session, mysql_error());
			mysql_close($link);
			return $session;
		}
		$row = mysql_fetch_row($sql);
	}while(!empty($row[0])); //if session ID is already in use, try a different one
	if(mysql_query('UPDATE _users SET session = "'.$sid.'" WHERE user = "'.$user.'"')){
		setcookie('sid', $sid, strtotime($expire)); //create cookie storing SID
		$session[0] = 1; //success
		return $session;
	}
	//an error occurred
	array_push($session, mysql_error());
	mysql_close($link);
	return $session;
}

/*****************************************************************
  Description: checks an existing browser cookie and session
  Arguments: $sid - session ID
	     $dbname - MySQL database name
             $sqlhost - MySQL database hostname
             $sqluser - MySQL database username
             $sqlpass - MySQL database password
  Return: array containing up to three values -
          1. boolean true if session matches value in database,
	     time has not expired, and IP address has not changed;
	     otherwise, boolean false is returned
          2. boolean true if error occurred, boolean false if not
          3. string containing an explanation of the failure
*****************************************************************/
function readSession($sid, $dbname, $sqlhost, $sqluser, $sqlpass){
	$session = array(false, true); //set default values
	//connect to database
        if(!($link = mysql_connect($sqlhost, $sqluser, $sqlpass))){
                //if an error occurred
                array_push($session, mysql_error());
                mysql_close($link);
                return $session;
        }
        //connect to database
        if(!mysql_select_db($dbname)){
                //if an error occurred
                array_push($session, mysql_error());
                mysql_close($link);
                return $session;
        }
	if($sql = mysql_query('SELECT ip,expire FROM _users WHERE session="'.$sid.'"')){
		//no errors occurred
		$session[1] = 0;
		$row = mysql_fetch_row($sql);
		mysql_close($link);
		if(empty($row[0])){
			//an empty set was returned
			array_push($session, 'Session was not found.');
			return $session;
		}
		//check if IP was changed
		$ip = sprintf("%u", ip2long($_SERVER['REMOTE_ADDR']));
		if(strcmp($ip,$row[0])){
			//IP was changed
			array_push($session, 'IP address does not match the one stored.');
			return $session;
		}
		//check if session has expired
		$expire = strtotime($row[1]);
		if(time() >= $expire){
			//session has expired
			array_push($session, 'Session has expired.');
			return $session;
		}
		//success
		$session[0] = 1;
		return $session;
	}
	//an error occurred
        array_push($session, mysql_error());
        mysql_close($link);
	return $session;
}
?>
