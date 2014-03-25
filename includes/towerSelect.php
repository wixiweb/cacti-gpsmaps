<?php
/*
 +-------------------------------------------------------------------------+
 | Copyright (C) 2009-2013 Andrew Aloia                                    |
 | Copyright (C) 2014 Wixiweb                                              | 
 |                                                                         |
 | This program is free software; you can redistribute it and/or           |
 | modify it under the terms of the GNU General Public License             |
 | as published by the Free Software Foundation; either version 2          |
 | of the License, or (at your option) any later version.                  |
 |                                                                         |
 | This program is distributed in the hope that it will be useful,         |
 | but WITHOUT ANY WARRANTY; without even the implied warranty of          |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the           |
 | GNU General Public License for more details.                            |
 +-------------------------------------------------------------------------+
 | http://www.cacti.net/                                                   |
 +-------------------------------------------------------------------------+
*/
?><html>
<?php

if ($_POST){
//SQL Stuff
global $config, $database_default;
include_once($config["library_path"] . "/database.php");

}else{
//SQL stuff
global $config, $database_default;
include_once($config["library_path"] . "/database.php");

$sql = "SELECT name,id FROM host_template";
$result = mysql_query($sql) or die (mysql_error());

//Begin form
$body .= "<form action=\"towerSelect.php\" method=\"post\">";

//Printout template types
while($row = mysql_fetch_array($result,MYSQL_BOTH)){
$body .= $row['name'] + ": <input type=\"text\" name=\""+ $row['id'] + "\" />";
}
$body .= "<input type=\"submit\" />";
$body .= "</form>";
print($body);
}
?>
</html>