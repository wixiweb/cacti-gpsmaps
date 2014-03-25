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

//---------------------------------------------------------------
function callRegion($subnet){
global $config;
include_once($config['base_path'] ."/plugins/gpsmap/includes/polling/pollinginitial.php");
include_once($config['base_path'] ."/plugins/gpsmap/includes/polling/processregion.php");	
region($subnet);
}
//---------------------------------------------------------------
function getTowerIds(){
$towerIds = array();
$result = mysql_query("SELECT `templateID` FROM `gpsmap_templates` WHERE `AP`=1");
if (mysql_num_rows($result)== 0){
	//add false towerID to relieve error, towerID goes by host type.
	$towerIds[] = 9999;
}else{
	while($row = mysql_fetch_array($result,MYSQL_NUM)){
		$towerIds[] = $row[0];
	}
}
return $towerIds;
}

//---------------------------------------------------------------
function calcMeters ($Lat1, $Lon1, $Lat2, $Lon2){
$difference= (6378.7*3.1415926*sqrt(($Lat2-$Lat1)*($Lat2-$Lat1) + cos($Lat2/57.29578)*cos($Lat1/57.29578)*($Lon2-$Lon1)*($Lon2-$Lon1))/180);
return $difference;
}

//---------------------------------------------------------------
function coordCheck($coords){
	$match = preg_match('#((-\d{1,3})|(\d{1,3}))(.)(\d+)#',$coords);
	if($match){
		return $coords;	
	}else{
	//return 0.00 as coords, user can fix problem.
	return "0.000";
	}
}

//---------------------------------------------------------------
function createDoc($hostArrays,$preemptive){
	xmlCreate($hostArrays,$preemptive);   
	kmlCreate($hostArrays,$preemptive);	
}

//---------------------------------------------------------------
function parseToXML($htmlStr) 
{ 
$xmlStr=str_replace('<','&lt;',$htmlStr); 
$xmlStr=str_replace('>','&gt;',$xmlStr); 
$xmlStr=str_replace('"','&quot;',$xmlStr); 
$xmlStr=str_replace("'",'&#39;',$xmlStr); 
$xmlStr=str_replace("&",'&amp;',$xmlStr);
return $xmlStr; 
} 

//---------------------------------------------------------------
function xmlCreate($hostArrays,$preemptive){
global $towerId;
global $config;
$doc = '<markers>';
$doc .= createXMLNodes($hostArrays[1]);
$doc .= coverageXML($hostArrays);
$doc .= '</markers>';
//write that xml
$filename = $config['base_path'] ."/plugins/gpsmap/XML/".$preemptive.".xml";
$f = fopen($filename,"w");
fwrite($f, $doc);
fclose($f);
}


//---------------------------------------------------------------
function coverageXML($hostArrays){
$doc = '';
global $config;
require($config['base_path'] ."/plugins/gpsmap/includes/polling/coveragexml.php");
return $doc;
}

//---------------------------------------------------------------
function kmlCreate($hostArrays,$preemptive){
global $config;
require($config['base_path'] ."/plugins/gpsmap/includes/polling/kmlcreation.php");
}


//---------------------------------------------------------------
function createTypeArray(){
	$typeArray = array();
	$result = mysql_query("SELECT `id`,`name` FROM `host_template`");
	while($row = mysql_fetch_array($result,MYSQL_NUM)){
		$typeArray[$row[0]] = $row[1];
		}
	return $typeArray;
}
//---------------------------------------------------------------
function createXMLNodes($hostArray){
		//step through each node selected and get info and shove it to xml
		$doc = "";
		$typeArray = createTypeArray();
	foreach($hostArray as $host){
		if($host->showMap == 1){
				//Add a new node to XML
				$doc .= '<marker ';
				//gotta determine type based on host type, 
				$type = $host->type;
				
				//insure nothing will be invalid.
				$doc .= 'id="' . parseToXML($host->id) . '" ';
				$doc .= 'name="' . parseToXML($host->description) . '" ';
				$doc .= 'address="' . parseToXML($host->hostname) . '" ';
				$doc .= 'lat="' . parseToXML($host->lat) . '" ';
				$doc .= 'lng="' . parseToXML($host->long) . '" ';
				if ($typeArray[$type]){
					$doc .= 'type="' . parseToXML($typeArray[$type]) . '" ';
				}else{
					$doc .= 'type="' . parseToXML("Unknown") . '" ';
				}
				$doc .= 'templateId="' . parseToXML($type) . '" ';
				$doc .= 'availability="' . $host->avail .'" ';
				$doc .= 'radius="0" ';
				$doc .= 'status="' . parseToXML($host->status) . '" ';
				$doc .= 'latency="' . parseToXML($host->latency) . '" ';
				$doc .= 'group="' . parseToXML($host->group) . '" ';
             	              $doc .= '/>'; 
                	       $doc .= "\n";
			
                }
	}
	return $doc;
}


?>