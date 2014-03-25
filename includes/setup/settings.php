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

function gpsmap_draw_navigation_text ($nav) {
   $nav['gpsmap.php:'] = array('title' => 'GPS Map', 'mapping' => 'index.php:', 'url' => 'gpsmap.php', 'level' => '1');
   $nav['gpstemplates.php:'] = array('title' => 'GPS Templates', 'mapping' => 'index.php:', 'url' => 'gpstemplates.php', 'level' => '1');
   $nav['gpstemplates.php:save'] = array('title' => 'GPS  Templates', 'mapping' => 'index.php:', 'url' => 'gpstemplates.php', 'level' => '1');
   $nav['gpstemplates.php:add'] = array('title' => 'GPS  Templates', 'mapping' => 'index.php:', 'url' => 'gpstemplates.php', 'level' => '1');
   $nav['gpstemplates.php:actions'] = array('title' => 'GPS  Templates', 'mapping' => 'index.php:', 'url' => 'gpstemplates.php', 'level' => '1');
   $nav['gpstemplates_add.php:'] = array('title' => 'Create New Template', 'mapping' => 'index.php:', 'url' => 'gpstemplates_add.php', 'level' => '1');
   return $nav;
}

function gpsmap_config_arrays () {
   global $menu;
   $menu['Templates']['plugins/gpsmap/gpstemplates.php'] = 'Map Templates';
}

function gpsmap_api_device_save ($save) {

    if (isset($_POST['GPScoverage']))
	$save['GPScoverage'] = "on";        
    else
 	$save['GPScoverage'] = "off";        

    if (isset($_POST['latitude']))
        $save['latitude'] = form_input_validate($_POST['latitude'], 'latitude', '', true, 3);
    else
        $save['latitude'] = form_input_validate('', 'latitude', '', true, 3);
	
    if (isset($_POST['longitude']))
        $save['longitude'] = form_input_validate($_POST['longitude'], 'longitude', '', true, 3);
    else
        $save['longitude'] = form_input_validate('', 'longitude', '', true, 3);
    
    if (isset($_POST['start']))
        $save['start'] = form_input_validate($_POST['start'], 'start', '', true, 3);
    else
        $save['start'] = form_input_validate('', 'start', '', true, 3);
        
    if (isset($_POST['stop']))
        $save['stop'] = form_input_validate($_POST['stop'], 'stop', '', true, 3);
    else
        $save['stop'] = form_input_validate('', 'stop', '', true, 3);

    if (isset($_POST['rdistance']))
        $save['rdistance'] = form_input_validate($_POST['rdistance'], 'distance', '', true, 3);
    else
        $save['rdistance'] = form_input_validate('', 'rdistance', '', true, 3);

    if (isset($_POST['groupnum']))
        $save['groupnum'] = form_input_validate($_POST['groupnum'], 'groupnum', '', true, 3);
    else
        $save['groupnum'] = form_input_validate('', 'groupnum', '', true, 3);
    return $save;
}

function gpsmap_config_settings () { 
    //this puts the following form elements on the Settings->GPS Map tab in cacti
    global $tabs, $settings;
    
    if (isset($_SERVER['PHP_SELF'])
        && basename($_SERVER['PHP_SELF']) !== 'settings.php'
    ) {
        return;
    }

    $tabs['gpsmap'] = 'GPS Map';
                               
    $settings['gpsmap'] = array(
        'gpsmap_header' => array(
            'friendly_name' => 'GPS Map (* Required)',
            'method'        => 'spacer',
        ),
        'gpsmap_apikey' => array(
            'friendly_name' => 'Google API Key',
            'description'   => 'The Google Maps API Key is not required but highly recommended, get more info at <a href=\'https://developers.google.com/maps/documentation/javascript/tutorial#api_key\'>Google Documentation</a>.',
            'method'        => 'textbox',
            'max_length'    => 100,
        ),
        'gpsmap_latutude' => array(
            'friendly_name' => 'Initial Latitude *',
            'description'   => 'Defines the centering of the map',
            'method'        => 'textbox',
            'max_length'    => 12,
        ),
        'gpsmap_longitude' => array(
            'friendly_name' => 'Initial Longitude *',
            'description'   => 'Defines the centering of the map',
            'method'        => 'textbox',
            'max_length'    => 12,
        ),
        'gpsmap_zoom' => array(
            'friendly_name' => 'Initial elevation *',
            'description'   => 'Defines the elevation of the map from 0 - 12',
            'method'        => 'textbox',
            'default'       => '12',
            'max_length'    => 2,
        ),
        'gpsmap_hostspacer' => array(
            'friendly_name' => 'Display Settings',
            'method'        => 'spacer',
        ),
        'gpsmap_enableall' => array(
            'friendly_name' => 'Allow display of disabled hosts',
            'description'   => '',
            'method'        => 'checkbox',
            'default'       => 'off',
        ),
        'gpsmap_coveragemap' => array(
            'friendly_name' => 'Coverage Overlay',
            'description'   => 'Draws a transparent circle around an AP with a radius equal to the furthest node in the same subnet',
            'method'        => 'checkbox',
        ),
        'gpsmap_refreshMap' => array(
            'friendly_name' => 'Map Refresh',
            'description'   => 'Refreshes map after set minutes. Reccomend set to poller interval, 0 to disable.',
            'method'        => 'textbox',
            'default'       => '5',
            'max_length'    => 2,
        ),
        'gpsmap_outputheader' => array(
            'friendly_name' => 'File Output',
            'method'        => 'spacer',
        ),
        'gpsmap_kmldomain' => array(
            'friendly_name' => 'KML usage Domain',
            'description'   => 'Put the domain name of the current webserver. IE 1.2.3.4 or domain.com',
            'method'        => 'textbox',
            'default'       => 'example.com',
            'max_length'    => 35,
        ),
        'gpsmap_overlayspacer' => array(
            'friendly_name' => 'Overlay Settings',
            'method'        => 'spacer',
        ),
        'gpsmap_terror' => array(
            'friendly_name' => 'Radius for Tabs (Required)',
            'description'   => 'Defines radius to combine points into one. (default: 0.0003)',
            'method'        => 'textbox',
            'default'       => '.0003',
            'max_length'    => 7,
        ),
        'gpsmap_fillcolor' => array(
            'friendly_name' => 'Fill Color',
            'description'   => 'The overlay circle fill color as FFFFFF',
            'method'        => 'textbox',
            'default'       => '0055ff',
            'max_length'    => 7,
        ),
        'gpsmap_licolor' => array(
            'friendly_name' => 'Ring Color',
            'description'   => 'Defines the outer rim color as FFFFFF',
            'method'        => 'textbox',
            'default'       => '0055ff',
            'max_length'    => 7,
        ),
        'gpsmap_liwidth' => array(
            'friendly_name' => 'Ring Width',
            'description'   => 'Outter Ring width',
            'method'        => 'textbox',
            'default'       => '2',
            'max_length'    => 7,
        ),
        'gpsmap_fillopa' => array(
            'friendly_name' => 'Fill Opacity',
            'description'   => 'Defines the fill opacity between 0 and 1',
            'method'        => 'textbox',
            'default'       => '.2',
            'max_length'    => 2,
        ),
        'gpsmap_liopa' => array(
            'friendly_name' => 'Ring Opacity',
            'description'   => 'Defines the ring opacity between 0 and 1',
            'method'        => 'textbox',
            'default'       => '.8',
            'max_length'    => 2,
        ),
        'gpsmap_circlequality' => array(
            'friendly_name' => 'Quality',
            'description'   => 'number of divisions in circle (preferably > 15) greater numbers can slow down browser performace.',
            'method'        => 'textbox',
            'default'       => '15',
            'max_length'    => 3,
        ),
    );

}