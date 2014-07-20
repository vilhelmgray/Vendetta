<?php
/***********************************************************************
    vendetta.php is the main page of Vendetta.
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
require 'config.php';
//Check if installation is finished
if(file_exists(V_ROOT.'install.php')){
	die('<html><head></head><body>You are seeing this message because either you have not executed the install file yet (which you can do so <a href="'.V_VENROOT.'install.php">here</a>), or you have executed the install file already, but not yet <strong>deleted</strong> <code>install.php</code>.</body></html>');
}
//begin webpage
include(V_INC.'top.php');
?>
<head>
        <title><?php echo V_NAME;?></title>
        <?php include(V_INC.'head.php');?>
</head>
<body>
        <?php include(V_IRC.'footer.php');?>
</body>
</html>
