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

function gpsmap_poller_bottom() {
global $config;
//Here we are getting the available hostnames (Numbers only) and
//Processing them to create our XML index arrays. So that it can  
//pass an partial IP as a parameter to the region() function in 
//the processregion.php file. Start with high subnet and work down.
include($config['base_path'] . '/plugins/gpsmap/includes/polling/functions.php');

$result =  db_fetch_assoc("SELECT `hostname` FROM `host`");
$firstArray = array();
$secondArray = array();
$thirdArray = array();
foreach($result as $row){
	list($first, $second, $third, $fourth) = explode('.',gethostbyname($row["hostname"]));
	if(!in_array($first.'.',$firstArray)){
		$firstArray[] = $first.'.';
	}
	if(!in_array($first.'.'.$second.'.',$secondArray)){
		$secondArray[] = $first.'.'.$second.'.';
	}
	if(!in_array($first.'.'.$second.'.'.$third.'.',$thirdArray)){
		$thirdArray[] = $first.'.'.$second.'.'.$third.'.';
	}
}
callRegion("all");
for($i=0; $i<count($firstArray); $i++){
	callRegion($firstArray[$i]);
	
}

for($i=0; $i<count($secondArray); $i++){
	callRegion($secondArray[$i]);
}
for($i=0; $i<count($thirdArray); $i++){
	callRegion($thirdArray[$i]);
}
}


?>