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

//This file takes care of the individual assignments for each custom icon set for the map templates
//This pulls the template from MySQL and attaches the Up/Down/Recover icon reference.
//Basically this makes a mapping between template and icon.

$customiconlist = "gpsmap.customIcons = {};\n";

$result = mysql_query('SELECT * FROM gpsmap_templates ORDER BY templateID');
while($row = mysql_fetch_array($result,MYSQL_BOTH)) {
    $icon = array();
    
    $icon = explode('.', $row['upimage']);
    $customiconlist .= "gpsmap.customIcons['" . $row['templateID'] . "up'] = gpsmap." . $icon[0] . ";\n";

    $icon = explode('.', $row['downimage']);
    $customiconlist .= "gpsmap.customIcons['" . $row['templateID'] . "down'] = gpsmap." . $icon[0] . ";\n";

    $icon = explode('.', $row['recoverimage']);
    $customiconlist .= "gpsmap.customIcons['" . $row['templateID'] . "recovering'] = gpsmap." . $icon[0] . ";\n";
}

//DO NOT REMOVE THESE
$customiconlist .= "gpsmap.customIcons['up'] = gpsmap.Green;\n";
$customiconlist .= "gpsmap.customIcons['recovering'] = gpsmap.Orange;\n";
$customiconlist .= "gpsmap.customIcons['down'] = gpsmap.Red;\n";
$customiconlist .= "gpsmap.customIcons['disabled'] = gpsmap.Black;\n";
$customiconlist .= "gpsmap.customIcons['undefined'] = gpsmap.Black;\n";

echo $customiconlist;
