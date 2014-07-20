<?php
/***********************************************************************
    top.php echoes the top HTML information for pages in Vendetta.
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
//record start time of page creation
$time = microtime(true);

//figure out if browser supports XML
$xml = false;
$mime = 'text/html';
$accept = $_SERVER['HTTP_ACCEPT'];
$useragent = $_SERVER['HTTP_USER_AGENT'];
//make sure browser sent information
if(isset($accept) && isset($useragent)){
	//if browser supports XML
	if(stristr($accept, 'application/xhtml+xml') || stristr($useragent, 'W3C_Validator')){
		$xml = true;
		$mime = 'application/xhtml+xml';
	}
}
header('Content-Type: '.$mime.';charset='.V_ENCODING);
header("Vary: Accept");
if($xml){
	//declare XML document
	echo '<?xml version="1.0" encoding="'.V_ENCODING.'"?>'."\r\n";
}
?>
<!--
    Vendetta <?php printf(V_VERSION."\r\n");?>
    Copyright (C) 2010  William Breathitt Gray

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
-->
<?php
	if($xml){ //if XML is supported by browser
		echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">'."\n";
		echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">';
	}else{
		echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">'."\n";
		echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">';
	}
?>
