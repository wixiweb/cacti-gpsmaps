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

$typeArray = createTypeArray();
$towerArray = $hostArrays[0];
$hostArray = $hostArrays[1];
foreach ($hostArray as $host){
	if(($host->showMap == "1")&&($host->coverage == "1")){
		foreach($towerArray as $tower){
				if($host->group == $tower->group){
					$distance = calcMeters($tower->lat,$tower->long, $host->lat, $host->long);
					if ($distance > $tower->radius){
						$tower->radius = $distance;
					}
				}
		}
		
	}
}
foreach($towerArray as $host){
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
			$doc .= 'radius="' . $host->radius .'"  ';
			$doc .= 'status="' . parseToXML($host->status) . '" ';
			$doc .= 'latency="' . parseToXML($host->latency) . '" ';
			$doc .= 'start="' . parseToXML($host->start) . '" ';
			$doc .= 'stop="' . parseToXML($host->stop) . '" ';
			$doc .= 'group="' . parseToXML($host->group) . '" ';
			$doc .= '/>'; 
			$doc .= "\n";
		
	}
}
?>