<?php
/***********************************************************************
    manage.php allows users to administrate Vendetta.
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
require_once('config.php');
require_once(V_FUNC.'login.php');

//begin webpage
include(V_INC.'top.php');

//check if installation is finished
/*if(file_exists(V_ROOT.'install.php')){
        die('<head></head><body>You are seeing this message because either you have not executed the install file yet (which you can do so <a href="'.V_VENROOT.'install.php">here</a>), or you have executed the install file already, but not yet <strong>deleted</strong> <code>install.php</code>.</body></html>');
}*/

$sid = htmlspecialchars($_COOKIE['sid']);
//check if cookie exists
if(!empty($sid)){
	$check = readSession($sid, V_DBNAME, V_SQLHOST, V_SQLUSER, V_SQLPASS);
}else{
	//get login information
	$login = $_POST['login'];
	if(isset($login) && $login == 1){
		$login = true;
		$pass = htmlspecialchars($_POST['pass']);
		if(empty($pass)){
			unset($pass);
		}
		$user = htmlspecialchars($_POST['user']);
		if(empty($user)){
        		unset($user);
		}
	}else{
		$login = false;
	}
}
?>
<head>
	<title><?php echo V_NAME;?> - Manage</title>
	<?php include(V_INC.'head.php');?>
	<link rel="stylesheet" type="text/css" href="<?php echo V_VENROOT;?>_system/styles/vendetta_manage.css"/>
</head>
<body>
	<?php
	if(isset($check)){
		if($check[0]){
			//if session is real
			echo "hi\n";
		}else{
			if($check[1]){
				//if an error occurred
				echo '<span class="debug">DEBUG ERROR: ';
				if(V_DEBUG){
					echo $check[2];
				}else{
					echo 'Set V_DEBUG to true to review error messages.';
				}
				echo '</span>';
			}else{
				echo '<span class="warn">WARNING: '.$check[2].'</span>';
			}
			//delete cookie
			setcookie('sid', '', time()-3600);
			include(V_INC.'login.html');
		}
	}else{
		if(!(isset($pass) || isset($user))){ //if both are unset
			include(V_INC.'login.html');
			if($login){
				echo '<div id="loginwarn">WARNING: You need to enter a username and password.</div>';
			}
		}elseif(!(isset($pass) && isset($user))){ //if either is unset
			include(V_INC.'login.html');
			echo '<div id="loginwarn">WARNING: You need to enter a ';
			if(!isset($pass)){
				echo 'password.';
			}else{
				echo 'username.';
			}
			echo '</div>';
		}else{ //if both are set
			//attempt login
			$hash = array(V_CIPHER, V_SALT, V_HASHLOOP, V_HASHSPLIT, V_SALTSPLIT, V_PEPPERSPLIT);
			$attempt = login($user, $pass, V_DBNAME, V_SQLHOST, V_SQLUSER, V_SQLPASS, $hash);
			if($attempt[0]){ //if login was successful
				$session = makeSession($user, V_SESSION, V_DBNAME, V_SQLHOST, V_SQLUSER, V_SQLPASS);
				if($session[0]){
					echo "<p>The login was success.
						<a href=\"manage.php\">Click here to continue.</a></p>\n";
					echo '<script type="text/javascript">
						/* <![CDATA[ */
						<!--
							window.location = "manage.php";
						//-->
						/* ]]> */
						</script>';
				}else{
					echo '<span class="debug">DEBUG ERROR: ';
					if(V_DEBUG){
						echo $session[1];
					}else{
						echo 'Set V_DEBUG to true to review error messages.';
					}
					echo '</span';
				}
			}else{ //if login failed
				include(V_INC.'login.html');
				if(!$attempt[1]){ //if username or password is incorrect
					echo '<div id="loginwarn">WARNING: ';
					if(V_DEBUG){ //if debugging is turned on
						echo $attempt[2]; //reveal whether username or password is at fault
					}else{
						echo 'The login entered was incorrect.';
					}
					echo '</div>';
				}else{ //if an error occurred
					echo '<span class="debug">DEBUG ERROR: ';
					if(V_DEBUG){ //if debugging is turned on
						echo $attempt[2];
					}else{
						echo 'Set V_DEBUG to true to review error messages.';
					}
					echo '</span>';
				}
			}	
		}
	}
	?>
	<?php include(V_INC.'footer.php');?>
</body>
</html>
