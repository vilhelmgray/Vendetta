<?php
/***********************************************************************
    config.php loads the main configuration settings for Vendetta.
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

$cf = array(); // Array to hold all configuration settings

// Caching: enabling will cache the configuration, skipping it the next time around
$cf['V_APC'] = false;
$cacheLoaded = false;

if($cf['V_APC']){
	// check if values are in cache
	if(apc_load_constants('config')){
		$cacheLoaded = true;
	}
}

if(!$cacheLoaded){
	// Display debugging messages
	$cf['V_DEBUG'] = true;
	
	// MySQL information
	$cf['V_SQLHOST'] = 'localhost'; // MySQL database hostname
	$cf['V_SQLUSER'] = 'user'; // MySQL database username
	$cf['V_SQLPASS'] = 'pass'; // MySQL database password
	$cf['V_DBNAME'] = 'vendetta';

	// Website address (no trailing /)
	// Example: www.example.org
	// Example: localhost
	$cf['V_SITEROOT'] = 'localhost';
	// Directory where Vendetta is installed, from site root (no trailing /)
	// Example: 'imageboard/vendetta' if located at http://www.example.org/imageboard/vendetta/
	// Example: '' if located at site root (http://www.example.org/)
	$cf['V_VENROOT'] = '';
	
	// Imageboard information
	$cf['V_NAME'] = 'Vendetta'; // Name of your site
	
	// Timezone
	$cf['V_TIMEZONE'] = 'UTC'; // timezone identifier, like UTC or Europe/Lisbon

	// Path to favicon from V_VENROOT directory
	$cf['V_FAVPATH'] = 'favicon.png';
	// MIME type
	// Example:  'image/vnd.microsoft.icon' for ICO file
	$cf['V_FAVTYPE'] = 'image/png';
	
	// Session expiration
	// lifetime = seconds * minutes * hours * days
	// Example: 60 * 60 * 24 * 5 = 5 days
	$cf['V_SESSION'] = 60 * 60; // lifetime of session in seconds
	
	/*********************************************************************
	             EDIT ONLY BEFORE INSTALLATION BELOW THIS LINE
	*********************************************************************/
	// Salt to strengthen hash
        $cf['V_SALT'] = 'somereallylongandrandomstringofcharacters'; // enter a string of random characters (recommended size >= 32 characters)
	//Number of times to loop hashing function
	$cf['V_HASHLOOP'] = 4096; //Must be >= 1 (CAUTION: large values may affect performance)
	// Number of sections to split hash
	$cf['V_HASHSPLIT'] = 2; //Must be >= 2 and <= the number of characters in a hash of the cipher described in V_CIPHER
	// Number of sections to split salt
	$cf['V_SALTSPLIT'] = 2; //Must be >= 2 and <= the number of characters in V_SALT
	// Number of sections to split pepper
	$cf['V_PEPPERSPLIT'] = 2; //Must be >= 2 and <= 32

	/*********************************************************************
	                     DO NOT EDIT BELOW THIS LINE
	*********************************************************************/
	// Combine V_SITEROOT and V_VENROOT to get absolute path to vendetta directory
	$cf['V_VENROOT'] = 'http://'.$cf['V_SITEROOT'].'/'.$cf['V_VENROOT'].'/';
	// Finds canonicalized absolute path for root
	$cf['V_ROOT'] = dirname(realpath(__FILE__)).'/';
	
	// Locations of system folders
	$cf['V_CONF'] = $cf['V_ROOT'].'_system/conf/'; //configurations
	$cf['V_FUNC'] = $cf['V_ROOT'].'_system/func/'; //functions
	$cf['V_INC'] = $cf['V_ROOT'].'_system/inc/'; //includes
	$cf['V_STYLES'] = $cf['V_ROOT'].'_system/styles/'; //stylesheets
	// Location of system folders based on web address
	$cf['V_VENIMAGES'] = $cf['V_VENROOT'].'_system/images/'; //images
	$cf['V_VENSTYLES'] = $cf['V_VENROOT'].'_system/styles/'; //stylesheets

	// Encoding
	$cf['V_ENCODING'] = 'UTF-8';

	//set default timezone
	date_default_timezone_set($cf['V_TIMEZONE']);

	// Hash cipher
    $cf['V_CIPHER'] = 'SHA1'; // MD5, SHA1, SHA256, SHA512 (any other cipher supported by hash())

	// Vendetta Version
	$cf['V_VERSION'] = '0.0.0';
		
	// will cache values if APC is enabled
	if($cf['V_APC']){
		apc_define_constants('config', $cf);
	}
	// sets values as CONSTANTS
	while(list($key, $value) = each($cf)){
		define($key, $value);
	}
	unset($cf);
}
?>
