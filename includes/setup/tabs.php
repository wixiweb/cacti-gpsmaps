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

function gpsmap_show_tab () {
	global $config;
	if (api_user_realm_auth('gpsmap.php')) {
		$cp = false;
		if (basename($_SERVER['PHP_SELF']) == 'gpsmap.php'){
			$cp = true;
		}
		print '<a href="' . $config['url_path'] . 'plugins/gpsmap/gpsmap.php"><img src="' . $config['url_path'] . 'plugins/gpsmap/images/tab_gpsmap' . ($cp ? '_down': '') . '.gif" alt="GPS Map" align="absmiddle" border="0"></a>';
	}
}

?>