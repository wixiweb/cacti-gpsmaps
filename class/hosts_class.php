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

class host{
	var $id = "0";
	var $type = "0";
	var $lat = "0";
	var $long = "0";
	var $iprange = "";
	var $description = "";
	var $hostname = "";
	var $radius = "0";
	var $avail = "";
	var $status = "";
	var $showMap = "1";
	var $latency = "0";
	var $coverage = "1";
	var $upimage = "";
	var $downimage = "";
	var $recoverimage = "";
	var $start = "0";
	var $stop = "360";
	var $group = "0";

function host($id,$type,$lat,$long,$iprange,$description,$hostname,$radius,$avail,$status,$latency,$coverage,$upimage,$downimage,$recoverimage,$start,$stop,$group){
	
	$this->id = $id;
	$this->type = $type;
	$this->lat = $lat;
	$this->long = $long;
	$this->iprange = $iprange;
	$this->description = $description;
	$this->hostname = $hostname;
	$this->radius = $radius;
	$this->avail = $avail;
	$this->status = $status;
	$this->latency = $latency;
	if($coverage === "on")
		$this->coverage = 1;
	else
		$this->coverage = 0;
	$this->upimage = $upimage;
	$this->downimage = $downimage;
	$this->recoverimage = $recoverimage;
	$this->start = $start;
	$this->stop = $stop;
	$this->group = $group;

}
}
?>
