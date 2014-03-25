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

if (!isset($_REQUEST['show'])) { 
    $show = '';
}
else {
    $show = $_REQUEST['show'];
}

// GPS SETTINGS
$apiKey = read_config_option('gpsmap_apikey');
$initialLong = read_config_option('gpsmap_longitude');
$initialLat = read_config_option('gpsmap_latutude');

// insurance that the basic options are set.
if($initialLong == '' || $initialLat == ''){
    $show = 'setup';
}
