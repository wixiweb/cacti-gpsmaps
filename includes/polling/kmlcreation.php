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

$kmldoc = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
$kmldoc .= "<kml xmlns=\"http://www.opengis.net/kml/2.2\">\n";             
$kmldoc .= "<Document>"; 
$kmldoc .= "<name>GPSMaps Points</name>\n";

//default google point info for KML

include_once($config['base_path'] ."/plugins/gpsmap/includes/polling/iconskml.php"); 
$kmldoc .= iconskml();  
                 
//default
$kmldoc .= "<Style id=\"pushpin\">";
$kmldoc .= "<IconStyle id=\"mystyle\">";
$kmldoc .= "<Icon>";
$kmldoc .= "<href>http://maps.google.com/mapfiles/kml/pushpin/ylw-pushpin.png</href>";
$kmldoc .= "<scale>1.0</scale>";
$kmldoc .= "</Icon>";                            
$kmldoc .= "</IconStyle>";
$kmldoc .= "</Style>\n";            

foreach($hostArrays as $hostArray){
foreach($hostArray as $host){
$icon = array();

//Check if google icon is used first
//else a custom icon is used and we need to parse.
if($host->status == "up"){
	$icon = explode('.',$host->upimage);
}elseif($host->status == "down"){
	$icon = explode('.',$host->downimage);
}elseif($host->status == "recovering"){
	$icon = explode('.',$host->recoverimage);
}else{ 
	$icon = explode('.',$host->upimage); 
}
if(!strncmp("Google",$icon[0],6)){
		$icon[0] = strtolower(substr($icon[0],6));
}

$kmldoc .= "<Placemark>";
$kmldoc .= '<name>' . parseToXML($host->description) . '</name>';
$kmldoc .= "<styleUrl>". parseToXml($icon[0]) ."</styleUrl> ";
$kmldoc .= '<description>'. parseToXML($host->description) . "\n" . 'Availability: '. $host->avail . "\n" .'Address: ' . parseToXML($host->hostname) . '</description>';
$kmldoc .= '<Point>';                                                       
$kmldoc .= '<coordinates>'. parseToXML($host->long) .',' . parseToXML($host->lat) . '</coordinates>';
$kmldoc .= '</Point>';
$kmldoc .= '</Placemark>';               
$kmldoc .= "\n";
}
}
$kmldoc .= "</Document></kml>";
$f = fopen("./plugins/gpsmap/XML/".$preemptive.".kml","w");
fwrite($f, $kmldoc);
fclose($f);

?>