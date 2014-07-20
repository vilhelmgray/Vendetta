<?php
/***********************************************************************
    head.php echoes the HTML header information for pages in Vendetta.
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
require_once(V_FUNC.'file.php');
//load meta information
$metadesc = trim(loadFile(V_CONF.'metadesc.inc'));
?>
<meta http-equiv="Content-type" content="text/html;charset=<?php echo V_ENCODING;?>"/>
<meta http-equiv="Content-Style-Type" content="text/css"/>
<meta name="description" content="<?php echo $metadesc;?>"/>
<link rel="icon" type="<?php echo V_FAVTYPE;?>" href="<?php echo V_VENROOT.V_FAVPATH;?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo V_VENSTYLES;?>vendetta_main.css"/>
