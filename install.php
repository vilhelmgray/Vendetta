<?php
/***********************************************************************
    install.php installs Vendetta.
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

require_once 'config.php';
require_once V_FUNC.'crypto.php';
require_once V_FUNC.'parser.php';
$start = $_POST['start'];
//check if user is ready to start
if(!(isset($start) && strcmp($start, 'Yes') == 0)){
	$start = 0;
}else{
	$start = 1;
}
//begin webpage
include(V_INC.'top.php');
?>

<head>
	<title>Vendetta Installation</title>
	<?php include(V_INC.'head.php'); ?>
	<link rel="stylesheet" type="text/css" href="<?php echo V_VENROOT;?>INSTALL/install.css" />
</head>
<body>
	<div id="title">Vendetta Installation</div>
	<?php
	if($start == 0){
		echo '<p>Before you start, there are a few things you will need to do:</p>
			<ul>
				<li>
				Edit the <span class="code">config.php</span> file. The constants <span class="strong">V_DBHOST</span>,
				<span class="strong">V_DBUSER</span>, and <span class="strong">V_DBPASS</span> must be set accordingly.
				</li>
			</ul>
			<p>When you are ready to begin the installation, click the <span class="em">Begin Installation button.</span></p>
			<div id="agreement">
			<form action="install.php" method="post">
				<fieldset>
					<legend>Agreement</legend>
					<textarea rows="25" cols="72" readonly="readonly">';
		echo purifyhtml(V_ROOT.'COPYING',true);
        	echo '			</textarea><br/>
					<input id="sign" name="start" type="checkbox" value="Yes"/>
					<label for="sign">
						<span class="strong">
						<span class="code">
							I understand and agree to abide by the terms and conditions of this license.
						</span>
						</span>
					</label>
					<br/>
					<br/>
					<span class="failure">WARNING: Old tables may be overwritten by new ones.</span><br/>
					<input type="submit" value="Begin Installation"/>
				</fieldset>
			</form>
		</div>';
	}else{
		// Connect to MySQL server
		echo '<p>Connecting to MySQL server using <span class="code">'.V_SQLUSER.'@'.V_SQLHOST.'</span>...';
		if(!($link = mysql_connect(V_SQLHOST, V_SQLUSER, V_SQLPASS))){
			die('<span class="failure">Failed!</span><br/><span class="strong">Error:</span> '.mysql_error().'</p></body></html>');
		}
		echo '<span class="success">Connection established.</span></p>';
		// Firsts attempts to select database; if failure, it creates the database
		echo '<p>Looking for database <span class="code">'.V_DBNAME.'</span>...';
		if(mysql_select_db(V_DBNAME)){
			echo '<span class="message">Database selected.</span></p>';
		}else{
			// print out reason why database could not be found
			echo '<span class="warning">Database not found.</span><br/><span class="strong">Error:</span> '.mysql_error().'</p>';
			// create database
			echo '<p>Creating database <span class="code">'.V_DBNAME.'</span>...';
			if(!mysql_query('CREATE DATABASE '.V_DBNAME)){
				die('<span class="failure">Failed!</span><br/><span class="strong">Error:</span> '.mysql_error().'</p></body></html>');
			}
			echo '<span class="success">Success!</span>';
		
			// select new database
			echo '<br/>Looking for database <span class="code">'.V_DBNAME.'</span>...';
	        if(!mysql_select_db(V_DBNAME)){
				die('<span class="failure">Failed!</span><br/><span class="strong">Error:</span> '.mysql_error().'</p></body></html>');
			}
			echo '<span class="message">Database selected.</span></p>';
		}
	
		// set tables array to hold table names and MySQL creation commands
		$tables = array(array('_bans','_boards','_groups','_log','_users'),array());
		// delete tables if they already exist
		echo '<p>Deleting old tables (warnings are normal if tables do not exist):<ul>';
		for($x = 0;$x < count($tables[0]);$x++){
			echo '<li>';
			if(!mysql_query('DROP TABLE '.$tables[0][$x])){
				echo '<span class="strong">Warning:</span> '.mysql_error();
			}else{
				echo '<span class="warning">Table \''.$tables[0][$x].'\' was deleted.</span>';
			}
			echo '</li>';
		}
		echo '</ul></p>';
		// create tables
		echo '<p>Creating tables:';
		// create bans table
		$tables[1][] = 'CREATE TABLE _bans(
					ip INT(4) UNSIGNED,
					reason TEXT,
					modnote TINYTEXT,
					added DATETIME,
					expire DATETIME
				)';
		// create boards table
		$tables[1][] = 'CREATE TABLE _boards(
					board VARCHAR(32),
					status ENUM("locked","open") DEFAULT "open"
				)';
		//create groups table
		$tables[1][] = 'CREATE TABLE _groups(
					groupname VARCHAR(32),
					controlPanel ENUM("n","y") DEFAULT "n",
					placeBan ENUM("n","y") DEFAULT "n",
					removeBan ENUM("n","y") DEFAULT "n",
					createBoard ENUM("n","y") DEFAULT "n",
					deleteBoard ENUM("n","y") DEFAULT "n",
					deleteThread ENUM("n","y") DEFAULT "n",
					deletePost ENUM("n","y") DEFAULT "n"
				)';
		// create log table
		$tables[1][] = 'CREATE TABLE _log(
					name VARCHAR(32),
					ip INT(4) UNSIGNED,
					event TINYTEXT,
					time DATETIME
				)';
		// create users table
		$tables[1][] = 'CREATE TABLE _users(
					user VARCHAR(32),
					password CHAR(64),
					pepper CHAR(32),
					ip INT(4) UNSIGNED,
					login DATETIME,
					expire DATETIME,
					session CHAR(40),
					lastIP INT(4) UNSIGNED,
					lastLogin DATETIME,
					position VARCHAR(32),
					boards TEXT
				)';
		// execute MYSQL commands
		echo '<ul>';
		for($x = 0; $x < count($tables[0]); $x++){
			echo '<li>';
			if(!mysql_query($tables[1][$x])){
				die('<span class="failure">Could not create '.$tables[0][$x].' table!</span><br/>
					<span class="strong">Error:</span> '.mysql_error().'</li></p></body></html>');
			}else{
				echo '<span class="message">Table \''.$tables[0][$x].'\' was created.</span>';
			}
			echo '</li>';
		}
		echo '</ul></p>';

		// insert inital values into database
		echo '<p>Initializing database:<ul>';
		$initialize = array(array(),array());
		// create inital administrator position
		$initialize[0][] = 'administrator group';
		$initialize[1][] = 'INSERT INTO _groups(
						groupname,controlPanel,placeBan,removeBan,createBoard,deleteBoard,deleteThread,deletePost)
						VALUES(
						"Administrator","y","y","y","y","y","y","y")';
		$initialize[0][] = 'moderator group';
		$initialize[1][] = 'INSERT INTO _groups(
						groupname,controlPanel,placeBan,removeBan,deleteThread,deletePost)
						VALUES(
						"Moderator","y","y","y","y","y")';
		// create inital administrator account
		$initialize[0][] = 'initial administrator account';
		// create hash
		$soup = crypto("nimda", V_CIPHER, V_SALT, NULL, V_HASHLOOP, V_HASHSPLIT, V_SALTSPLIT, V_PEPPERSPLIT);
		if(!isset($soup[1])) die($soup[0].'</body></html>'); // make sure hash was created
		// insert admin into users table
		$initialize[1][] = 'INSERT INTO _users(
					user,password,pepper,position)
					VALUES(
					"admin","'.hash('sha256',$soup[0]).'","'.$soup[2].'","Administrator")';
		// execute MySQL commands
		for($x = 0; $x < count($initialize[0]); $x++){
			echo '<li>Inserting '.$initialize[0][$x].' into the database...';
			if(!mysql_query($initialize[1][$x])){
				die('<span class="failure">Could not insert '.$initialize[0][$x].' into the database.</span><br/>
					<span class="strong">Error:</span> '.mysql_error().'</li></p></body></html>');
			}else{
				echo '<span class="success">Success!</span>';
			}
			echo '</li>';
		}
		echo '</ul></p>';
		mysql_close($link);

		// Installation complete
		echo '<hr/>
			<p>The installation has finished successfully.
			Please login at <a href="manage.php"><span class="code">manage.php</span></a>
			using the initial administrator account.</p>
			<p><span class="strong">Username:</span> <span class="code">admin</span></p><br/>
			<span class="strong">Password:</span> <span class="code">nimda</span><br/>
			<p>Once you logon for the first time, it may be a good idea to change your password
			and create a new administrator account.</p>';
	}
	?>
	<?php include(V_INC.'footer.php');?>
</body>
</html>
