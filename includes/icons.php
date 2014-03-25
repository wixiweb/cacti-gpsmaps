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
//get all icons in the icon folder and create an icon list.
//This a process to dynamically create the javascript for each icon.
//This is separate from the function in customicons.php
$server = $_SERVER['SERVER_NAME'];

$icons = opendir('plugins/gpsmap/images/icons');
while (false !== ($icon = readdir($icons))) {
    if ($icon !== '.' && $icon !== '..') {
        list($icon, $tail) = explode('.', $icon);

        switch ($tail) {
            case "png":
            case "jpg":
            case "jpeg":
            case "gif":

                echo 'gpsmap.' , $icon , ' = {', PHP_EOL,
                     "url : 'http://" , $server , $url_path , '/plugins/gpsmap/images/icons/' , $icon, '.', $tail, "',", PHP_EOL,
                     'size : new google.maps.Size(12, 20),', PHP_EOL,
                     'anchor : new google.maps.Point(6, 20)};', PHP_EOL, PHP_EOL
                ;

                break;
            default:
                //Not an icon we want to load
                break;
        }
    }
}
closedir($icons);
