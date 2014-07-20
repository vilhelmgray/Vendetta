<?php
/***********************************************************************
    file.php does various file operations.
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

/*****************************************************************
  Description: loads a file into a string
  Arguments: $file - path to file
  Return: string with the contents of the file
*****************************************************************/
function loadFile($file){
	$handle = fopen($file, "r");
	$contents = fread($handle, filesize($file));
	fclose($handle);
	return $contents;
}
?>
