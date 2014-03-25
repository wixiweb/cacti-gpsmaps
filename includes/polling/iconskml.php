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

function iconskml(){
//get all icons in the icon folder and create an icon list
global $config;
$kmlDomain = read_config_option("gpsmap_kmldomain");
$iconlist = "";
$icons = opendir($config['base_path'] . '/plugins/gpsmap/images/icons');
while (false !== ($icon = readdir($icons))){
		if ($icon != "." && $icon != ".."){
			list($icon,$tail) = explode('.',$icon);
			switch($tail){
			case "png":
			case "jpg":
			case "jpeg":
			case "gif":
			$iconlist .= "<Style id=\"".$icon."\">";
			$iconlist .= "<IconStyle id=\"my".$icon."\">";
			$iconlist .= "<Icon>";
			$iconlist .= "<href>http://".$kmlDomain.$config['url_path']."plugins/gpsmap/images/icons/".$icon.".".$tail."</href>";
			$iconlist .= "<scale>1.0</scale>"; 
			$iconlist .= "</Icon>";
			$iconlist .= "</IconStyle>";
			$iconlist .= "</Style>\n";
			break;
			default:
			//Not an icon we want to load
			break;
			}

		}
}

return $iconlist;
}
?> 