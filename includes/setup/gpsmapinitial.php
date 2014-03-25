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

//Setup of base parameters and get info.
$parameter = "";
$iplevel = "";  
$coverage = "";
$body = "";

if (!isset($_REQUEST["subnet"])){
	$parameter = "";
}else{
	$parameter = $_REQUEST["subnet"];
}

//GPS SETTINGS *Other settings in show.php*
$initialzoom = read_config_option("gpsmap_zoom");

//DISPLAY SETTINGS
$enableAll = read_config_option("gpsmap_enableall");
$coverageMap = read_config_option("gpsmap_coveragemap");
if($enableAll === "on"){$enableAll = true;}else{$enableAll = false;}
if($coverageMap === "on"){$coverageMap = true;}else{$coverageMap = false;}
//File Output
$kmlCreation = read_config_option("gpsmap_kmlexport");
if($kmlCreation === "on"){$kmlCreation = 1;}else{$kmlCreation = 0;}
$kmlDomain = read_config_option("gpsmap_kmldomain");
   
//OVERLAY SETTINGS
$fillColor = read_config_option("gpsmap_fillcolor");
$liColor = read_config_option("gpsmap_licolor");
$liWidth = read_config_option("gpsmap_liwidth");
$fillOpa = read_config_option("gpsmap_fillopa");
$liOpa = read_config_option("gpsmap_liopa");
$circleQuality = read_config_option("gpsmap_circlequality");
$terror = read_config_option("gpsmap_terror");

//WEATHER SETTINGS
$enableWeather = read_config_option("gpsmap_enableWeather");

//Map Refresh
$refreshMap= read_config_option("gpsmap_refreshMap");

?>