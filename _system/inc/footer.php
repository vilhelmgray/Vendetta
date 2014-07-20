<?php
/***********************************************************************
    footer.php echoes the footer for pages in Vendetta.
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
require_once V_FUNC.'file.php';
require_once V_FUNC.'parser.php';
//load user-defined footer
$footer = purifyPHP(trim(loadFile(V_CONF.'footer.html')));
?>
<div id="footer">
<?php echo $footer;?>
<br/>
<span class="footer">
|
Vendetta <?php echo V_VERSION;?>
 |
Created in <?php printf("%.2f", round((microtime(true) - $time), 2));?> seconds
 |
<a href="http://www.gnu.org/licenses/agpl.html">
	<img style="border-style:none"
	     src="<?php echo V_VENIMAGES;?>agplv3-88x31.png"
	     alt="GNU Affero General Public License"/>
</a>
</span>
</div>
