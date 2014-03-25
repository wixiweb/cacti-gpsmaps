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

function plugin_gpsmap_install () {
	global $config;
	api_plugin_register_hook('gpsmap', 'top_header_tabs',       'gpsmap_show_tab',             'includes/setup/tabs.php');
	api_plugin_register_hook('gpsmap', 'top_graph_header_tabs', 'gpsmap_show_tab',             'includes/setup/tabs.php');
	api_plugin_register_hook('gpsmap', 'config_arrays',         'gpsmap_config_arrays',        'includes/setup/settings.php');
	api_plugin_register_hook('gpsmap', 'config_settings',       'gpsmap_config_settings',      'includes/setup/settings.php');
	api_plugin_register_hook('gpsmap', 'draw_navigation_text',  'gpsmap_draw_navigation_text', 'includes/setup/settings.php');
	api_plugin_register_hook('gpsmap', 'api_device_save',       'gpsmap_api_device_save', 	'includes/setup/settings.php');
	api_plugin_register_hook('gpsmap', 'config_form', 	    'gpsmap_config_form',          'setup.php');
	api_plugin_register_hook('gpsmap', 'poller_bottom', 	    'gpsmap_poller_bottom', 	'includes/polling.php');
	
	api_plugin_register_realm('gpsmap',	'gpstemplates.php,gpstemplates_add.php','Configure GPSMap', 1);
	api_plugin_register_realm('gpsmap',	'gpsmap.php', 'View GPSMap', 1);

	include_once($config['base_path'] . '/plugins/gpsmap/includes/setup/database.php');
	gpsmap_setup_database();
}


function plugin_gpsmap_uninstall () {
	//gpsmap_remove_database();
}        
function plugin_gpsmap_check_config () {
	//Here we will check to ensure everything is configured 
	gpsmap_check_upgrade ();
	return true;
}

function plugin_gpsmap_upgrade () {
	// Here we will upgrade to the newest version
	gpsmap_check_upgrade ();
	return false;
} 
 
function plugin_gpsmap_version () {
	return gpsmap_version();
}

function gpsmap_check_upgrade () {
	global $config;
	$files = array('gpsmap.php','gpstemplates.php','poller.php');
	if (isset($_SERVER['PHP_SELF']) && !in_array(basename($_SERVER['PHP_SELF']), $files))
		return;
	$current = plugin_gpsmap_version();
	$current = $current['version'];
	$old = read_config_option('plugin_gpsmap_version', TRUE);
	if ($current != $old) {
		include_once($config['base_path'] . '/plugins/gpsmap/includes/database.php');
		gpsmap_upgrade_database ();
	}
}

function gpsmap_version () {
    return array(
        'name'      => 'gpsmap',
        'version'   => '1.8.0',
        'longname'  => 'GPS Mapping',
        'author'    => 'Andy Aloia / Wixiweb',
        'homepage'  => 'http://www.wixiweb.com/',
        'email'     => 'contact@wixiweb.fr',
        'url'       => 'https://github.com/wixiweb/cacti-gpsmaps'
    );
}

//defines latitude and longitude for devices
function gpsmap_config_form () {
	global $fields_host_edit;
	$fields_host_edit2 = $fields_host_edit;
	$fields_host_edit3 = array();
	foreach ($fields_host_edit2 as $f => $a) {
		$fields_host_edit3[$f] = $a;
		if ($f == 'disabled') {
			$fields_host_edit3['gpsSpacer'] = array(
            			'friendly_name' => 'GPS Settings',
			       'method' => 'spacer',
		            );
			$fields_host_edit3['GPScoverage'] = array(
				'friendly_name' => 'Overlay Inclusion',
				'description' => 'Disable to plot host only, not included in coverage overlay.',
				'method' => 'checkbox',
				'value' => '|arg1:GPScoverage|',
				'default' => 'on',
		        );

			$fields_host_edit3['latitude'] = array(
				'friendly_name' => 'Latitude',
				'description' => 'The devices latitude coordinates',
				'method' => 'textbox',
				'max_length' => 13,
				'value' => '|arg1:latitude|',
				'default' => '',
			);
			$fields_host_edit3['longitude'] = array(
				'friendly_name' => 'Longitude',
				'description' => 'The devices longitude coordinates',
				'method' => 'textbox',
				'max_length' => 13,
				'value' => '|arg1:longitude|',
				'default' => '',
			);
			//Only apply this rule if on a Host page with an ID number present and is an AP device
			global $url_path;
			if ((isset ($_GET["id"]))&&($_SERVER["PHP_SELF"] == $url_path."host.php")){
				$did = $_GET["id"];
				$result = mysql_query("SELECT AP FROM `host` RIGHT JOIN gpsmap_templates ON host.host_template_id = gpsmap_templates.templateID WHERE id=".$did."");
				$row = mysql_fetch_array($result,MYSQL_ASSOC);
				if($row['AP'] == 1){
					$fields_host_edit3['start'] = array(
					'friendly_name' => 'Starting Degree',
					'description' => 'Starting degree for directional area between 0-360',
					'method' => 'textbox',
					'max_length' => 4,
					'value' => '|arg1:start|',
					'default' => '0',
				);
				$fields_host_edit3['stop'] = array(
					'friendly_name' => 'Stopping Degree',
					'description' => 'Stopping degree for directional area, must be greater than the Starting Degree',
					'method' => 'textbox',
					'max_length' => 4,
					'value' => '|arg1:stop|',
					'default' => '360',
				);
				$fields_host_edit3['rdistance'] = array(
					'friendly_name' => 'Specify Radius',
					'description' => 'Manually specify radius for Access Point. Set to 0 to determine radius based on grouped devices',
					'method' => 'textbox',
					'max_length' => 10,
					'value' => '|arg1:rdistance|',
					'default' => '0',
				);
				}
			}
			$fields_host_edit3['groupnum'] = array(
				'friendly_name' => 'Group ID',
				'description' => 'Groups define what devices are included in the coverage overlay. Will be checked against AP device group number. 0 to disable',
				'method' => 'textbox',
				'max_length' => 3,
				'value' => '|arg1:groupnum|',
				'default' => '0',
			);
						
		} 
	}
	$fields_host_edit = $fields_host_edit3;
}

?>
