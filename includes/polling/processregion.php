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

//this function is called to process the nodes and get them ready for analysis
//---------------------------------------------------------------
function region($subnet){
$towerIds = getTowerIds();
global $config;
global $body;
global $enableAll;
global $kmlCreation;
include_once($config['base_path'] . '/plugins/gpsmap/class/hosts_class.php');
$kmlDomain = read_config_option("gpsmap_kmldomain");
$iparray = array();
$ipwriteout = array();
$hostArrays = array();
$hostArray = array();
$towerArray = array();
$back = "";
//we are going to parse every node and sort them by region numbers, IE the IP range seleced in the setup.
if($enableAll){
$result = mysql_query("SELECT * FROM `host` LEFT JOIN gpsmap_templates ON host.host_template_id = gpsmap_templates.templateID ORDER BY hostname");
}else{
$result = mysql_query("SELECT * FROM `host` LEFT JOIN gpsmap_templates ON host.host_template_id = gpsmap_templates.templateID WHERE disabled='' ORDER BY hostname");
}
while($row = mysql_fetch_array($result,MYSQL_ASSOC)){
	if(isset($row['latitude'])&&isset($row['longitude'])&&($row['latitude'] != '')&&($row['latitude'] != '')&&($row['latitude'] != '0')&&($row['longitude'] != '0')){
	if (list($first, $second, $third, $fourth) = explode('.',gethostbyname($row["hostname"]))){
		$status = $row['status'];
		$upimage = ($row['upimage'] ? $row['upimage']: "Green");
		$downimage = ($row['downimage'] ? $row['downimage']: "Red");
		$recoverimage = ($row['recoverimage'] ? $row['recoverimage']: "Yellow"); // ((condition) ? (true) : (false))
		if ($status == 3){
			$status = "up"; 
		}elseif($status == 1){
			$status = "down";
		}elseif($status == 2){
			$status = "recovering";
		}else{ $status = "undefined"; }
		if($row['disabled'] == "on"){
			$status = "disabled";     
		}
		$start = $row['start'];
		$lat = coordCheck($row['latitude']);
		$lon = coordCheck($row['longitude']);
		$stop = $row['stop'];
		$group = $row['groupnum'];
		if(in_array($row['host_template_id'],$towerIds)){
				$towerArray[] =  new host($row['id'],$row['host_template_id'],$lat,$lon,$first.$second.$third.$fourth,$row['description'],$row['hostname'],0,$row['availability'],$status,$row['cur_time'],$row['GPScoverage'],$upimage,$downimage,$recoverimage,$start,$stop,$group);
			}else{
				$hostArray[] = new host($row['id'],$row['host_template_id'],$lat,$lon,$first.$second.$third.$fourth,$row['description'],$row['hostname'],0,$row['availability'],$status,$row['cur_time'],$row['GPScoverage'],$upimage,$downimage,$recoverimage,0,360,$group);
		}
	}
	}
}	
$hostArrays[]=$towerArray;
$hostArrays[]=$hostArray;
$preempt = str_word_count($subnet, 0, '.');
if($subnet == "all"){
	$preempt = 0;
}
//This section deals with traversal of the subnets
//sort by top level, and display all top IP range so they can be selected, and iterate this down through level 4
//if subnet level is met we want to display the lower ones.                                    
//for 1 we want to display all top level IP
foreach($hostArrays as $hostArray){
	foreach($hostArray as $host){
	list($first, $second, $third, $fourth) = explode('.',$host->hostname);				
			switch ($preempt){
			case 0:
				if(!in_array($first.'.',$iparray)){
					$iparray[] = $first.'.';
				}
			break;
			case 1:
				if(!strcasecmp($subnet,$first.'.')){
					if(!in_array($first.'.'.$second.'.',$iparray)){
						$iparray[] = $first.'.'.$second.'.';
					}
					
				}else{
					//set disable if it does not match
					$host->showMap = 0;
				}
			break;			
			case 2:
				if(!strcasecmp($subnet,$first.'.'.$second.'.')){
					if(!in_array($first.'.'.$second.'.'.$third.'.',$iparray)){
						$iparray[] = $first.'.'.$second.'.'.$third.'.';
					}
				}else{
					$host->showMap = 0;
				}
			break;
			case 3: 
				if(!strcasecmp($subnet,$first.'.'.$second.'.'.$third.'.')){
					if(!in_array($first.'.'.$second.'.'.$third.'.'.$fourth.'.',$iparray)){
						$iparray[] = $first.'.'.$second.'.'.$third.'.'.$fourth;
						$ipwriteout[] = "<a href=\"../../graph_view.php?action=preview&host_id=".$host->id."\">IP ".$first.'.'.$second.'.'.$third.'.'.$fourth."</a><br/>";
					}
				}else{
					$host->showMap = 0;
				}
			break;
			default:
				$host->showMap = 0;
			break;
		}
	}
}
//process data collected and create interface output
foreach($iparray as $ipout){
if($preempt != 3){
	$ipwriteout[] = "<a href=\"./gpsmap.php?subnet=".$ipout."\">IP ".$ipout."</a>-(<a href=\"http://".$kmlDomain.$config['url_path']."plugins/gpsmap/XML/".$ipout.".xml\">X</a>-<a href=\"http://".$kmlDomain.$config['url_path']."plugins/gpsmap/XML/".$ipout.".kml\">K</a>)<br/>";
}
}

//print out the information we have gathered.
$body .= '<div id="gpstopmenu" style="border:1px solid black; overflow: auto; width:100%; background-color:lightgrey;">';
$body .= "<div id=\"gpsnav\"style=\"width:75px; overflow:auto; background-color:lightgrey; float:left; position:relative;\"><a href=\"./gpsmap.php\"><u>Start Over</u></a><br/><br/>";
$body .= "<img src=\"./images/print.gif\" class=\"print\" alt=\"print\" title=\"print\" onclick=\"window.open('print.php')\" />";
$body .= "</div>";
$tempHold = " ";
$i = 1;
foreach($ipwriteout as $ipoutput){
	$tempHold .= $ipoutput;
	if($i%6 == 0){
		$body .= "<div id=\"iplevels\" style=\"width:120px; overflow: auto; float:left; background-color:lightgrey; position: relative;\">".$tempHold."</div>";
		$tempHold = " ";   
	}
$i++;
}                     
//print out what is left   
$body .= "<div id=\"iplevels\" style=\"width:120px; overflow:auto; float:left; background-color:lightgrey; position: relative;\">".$tempHold."</div>";
$tempHold = " ";   
$body .="</div>";

if($subnet == ""){
	createDoc($hostArrays,"all");
}else{
	createDoc($hostArrays,$subnet);
}
if($subnet == ""){
	$subnet = "all";
}
$filename = $config['base_path'] ."/plugins/gpsmap/XML/".$subnet."-top.html";
$f = fopen($filename,"w");
fwrite($f, $body);
fclose($f);

$body ="";
}
?>