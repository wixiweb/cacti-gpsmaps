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

chdir('../../');
include_once('./include/auth.php');

$ds_actions = array(
	1 => 'Edit',
	2 => 'Delete'
	);

$action = '';
if (isset($_POST['action'])) {
	$action = $_POST['action'];
} else if (isset($_GET['action'])) {
	$action = $_GET['action'];
}

if (isset($_POST['drp_action']) && $_POST['drp_action'] == 1) {
	$action = 'edit';
}
if (isset($_POST['drp_action']) && $_POST['drp_action'] == 2) {
	$action = 'delete';
}
switch ($action) {
	case 'add':
		template_add();
		break;
	
	case 'delete':
		template_delete();
		break;
	case 'edit':
		template_edit();
		break;
	case 'save':
		gpsmap_save_template();

		break;
	default:		
		include_once('./include/top_header.php');
		templates();
		include_once('./include/bottom_footer.php');
		break;
}

//------------------------------------------------------------------------------
function templates(){

global $config, $colors, $ds_actions;

html_start_box('<strong>GPS Templates</strong>' , '100%', $colors['header'], '3', 'center', 'gpstemplates.php?action=add');
html_header_checkbox(array('Host Template', 'Up Image', 'Recovering Image', 'Down Image', 'Is AP'));

$template_list = db_fetch_assoc('SELECT * FROM gpsmap_templates ORDER BY templateID');
$i=0;
if (sizeof($template_list) > 0) {
	foreach ($template_list as $template) {
		if($template['AP']){$isAP = "True";}else{$isAP="False";} 
		form_alternate_row_color($colors["alternate"], $colors["light"], $i, 'line' . $template["templateID"]); $i++;
		form_selectable_cell($template['templateName'], $template["templateID"]);
		form_selectable_cell($template['upimage'], $template["templateID"]);
		form_selectable_cell($template['recoverimage'], $template["templateID"]);
		form_selectable_cell($template['downimage'], $template["templateID"]);
		form_selectable_cell($isAP, $template["templateID"]);
		form_checkbox_cell($template['templateID'], $template["templateID"]);
		form_end_row();
	}
}else{
	print "<tr><td><em>No Data Templates</em></td></tr>\n";
}
html_end_box(false);

/* draw the dropdown containing a list of available actions for this form */
draw_actions_dropdown($ds_actions);

print "</form>\n";
}

//------------------------------------------------------------------------------
function template_add(){
	
global $config, $templates;

	//get host templates from DB
	$templates = db_fetch_assoc("SELECT id, name FROM host_template ORDER BY name");
	
	
	$iconArray = getIcons();
	//get all icons in the icon folder
	

	include($config['include_path'] . '/top_header.php');
	html_start_box('<strong>Template Creation Wizard</strong>', '50%', $colors['header'], '3', 'center', '');
	echo '<tr><td><form action="gpstemplates.php" method="post" name="gpsform">';
	print '<center><h3>Please select a Host Template</h3></center>';


	/* display the template dropdown */
	?>
	<center><table>
		<tr>
			<td width='70' style='white-space:nowrap;'>
				&nbsp;<b>Host Template:</b>
			</td>
			<td style='width:1;'>
				<select name="hostid">
					<option value=""></option><?php
					foreach ($templates as $row) {
						echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
					}?>
				</select>
			</td>
		</tr>
		<tr>
			<td width='70' style='white-space:nowrap;'>
				&nbsp;<b>Up Image:</b>
			</td>
			<td style='width:1;'>
				<select name="upimage">
					<option value=""></option><?php
					foreach ($iconArray as $icon) {
						echo "<option value='". $icon ."'>" . $icon . "</option>";
					}?>
				</select>
			</td>
		</tr>
		<tr>
			<td width='70' style='white-space:nowrap;'>
				&nbsp;<b>Recovering Image:</b>
			</td>
			<td style='width:1;'>
				<select name="recoverimage">
					<option value=""></option><?php
					foreach ($iconArray as $icon) {
						echo "<option value='". $icon ."'>" . $icon . "</option>";
					}?>
				</select>
			</td>
		</tr>
		<tr>
			<td width='70' style='white-space:nowrap;'>
				&nbsp;<b>Down Image:</b>
			</td>
			<td style='width:1;'>
				<select name="downimage">
					<option value=""></option><?php
					foreach ($iconArray as $icon) {
						echo "<option value='". $icon ."'>" . $icon . "</option>";
					}?>
				</select>
			</td>
		</tr>
		<tr>
			<td width='70' style='white-space:nowrap;'>
				&nbsp;<b>Device Type an Access Point</b>
			</td>
			<td style='width:1;'>
				<select name="AP">
					<option value="0">False</option>
					<option value="1">True</option>
				</select>
			</td>
		</tr>
	<?php

		echo '<tr><td colspan=2><input type=hidden name="action" value="save"><br><center><input type=image src="../../images/button_create.gif" alt="Create"></center></td></tr>';
		echo '<tr><td colspan=2><br><br><br></td></tr>';
		echo '</table></form></td></tr>';
		html_end_box();
}

//------------------------------------------------------------------------------
function template_delete() {
	foreach($_POST as $t=>$v) {
		if (substr($t, 0,4) == 'chk_') {
			$id = substr($t, 4);
			db_execute('DELETE FROM gpsmap_templates WHERE templateID=' . $id);
		}
	}
	Header('Location: gpstemplates.php');
	exit;
}

//------------------------------------------------------------------------------
function template_edit() {
    global $colors, $config;

   foreach($_POST as $t=>$v) {
	if (substr($t, 0,4) == 'chk_') {
		$id = substr($t, 4);
	break;//only edit one at a time
	}
   }
    
    $result = mysql_query("select * from `gpsmap_templates` where templateID = " . $id);
    $template_item_data =  mysql_fetch_array($result,MYSQL_BOTH);
	
	//get all icons in the icon folder
	$iconArray = getIcons();

	include($config['include_path'] . '/top_header.php');
	html_start_box('<strong>Template Edit</strong>', '50%', $colors['header'], '3', 'center', '');
	echo '<tr><td><form action="gpstemplates.php" method="post" name="gpsform">';
	print '<center><h3>Editing ' .  $template_item_data['templateName']  . '</h3></center>';

	/* display the template dropdown */
	?>
	<center><table>
		<tr>
			<td width='70' style='white-space:nowrap;'>
				&nbsp;<b>Host Template:</b>
			</td>
			<td style='width:1;'>
				<select name="hostid">
					<?php echo "<option value='" . $template_item_data['templateID'] . "'>" . $template_item_data['templateName'] . "</option>";?>
				</select>
			</td>
		</tr>
		<tr>
			<td width='70' style='white-space:nowrap;'>
				&nbsp;<b>Up Image:</b>
			</td>
			<td style='width:1;'>
				<select name="upimage">
					<?php echo "<option value='" . $template_item_data['upimage'] . "'>" . $template_item_data['upimage'] . "</option>";
					foreach ($iconArray as $icon) {
						echo "<option value='". $icon ."'>" . $icon . "</option>";
					}?>
				</select>
			</td>
		</tr>
		<tr>
			<td width='70' style='white-space:nowrap;'>
				&nbsp;<b>Recovering Image:</b>
			</td>
			<td style='width:1;'>
				<select name="recoverimage">
					<?php echo "<option value='" . $template_item_data['recoverimage'] . "'>" . $template_item_data['recoverimage'] . "</option>";
					foreach ($iconArray as $icon) {
						echo "<option value='". $icon ."'>" . $icon . "</option>";
					}?>
				</select>
			</td>
		</tr>
		<tr>
			<td width='70' style='white-space:nowrap;'>
				&nbsp;<b>Down Image:</b>
			</td>
			<td style='width:1;'>
				<select name="downimage">
					<?php echo "<option value='" . $template_item_data['downimage'] . "'>" . $template_item_data['downimage'] . "</option>";
					foreach ($iconArray as $icon) {
						echo "<option value='". $icon ."'>" . $icon . "</option>";
					}?>
				</select>
			</td>
		</tr>
		<tr>
			<td width='70' style='white-space:nowrap;'>
				&nbsp;<b>Device Type an Access Point</b>
			</td>
			<td style='width:1;'>
				<select name="AP">
					<?php if($template_item_data['AP'] == 1){
		 				 echo "<option value='1'>True</option>";
					}else{
						echo "<option value='0'>False</option>";
					} ?>
					<option value="0">False</option>
					<option value="1">True</option>
				</select>
			</td>
		</tr>
	<?php

		echo '<tr><td colspan=2><input type=hidden name="action" value="save"><br><center><input type=image src="../../images/button_save.gif" alt="Create"></center></td></tr>';
		echo '<tr><td colspan=2><br><br><br></td></tr>';
		echo '</table></form></td></tr>';
		html_end_box();


}
		

//------------------------------------------------------------------------------
function gpsmap_save_template(){
global $config;

$hostid = $_POST["hostid"];
$AP = $_POST["AP"];
$upimage = ($_POST["upimage"] ? $_POST["upimage"]: "Google Green");
$recoverimage = ($_POST["recoverimage"] ? $_POST["recoverimage"]: "Google Yellow");
$downimage = ($_POST["downimage"] ? $_POST["downimage"]: "Google Red");
$result = mysql_query("SELECT name FROM host_template WHERE id='$hostid'");
$row = mysql_fetch_array($result,MYSQL_BOTH);
$tempname = $row['name'];

db_execute('DELETE FROM gpsmap_templates WHERE templateID=' . $hostid);
if (db_execute("INSERT INTO gpsmap_templates(templateID,templateName,upimage,recoverimage,downimage,AP) VALUES ('$hostid','$tempname','$upimage','$recoverimage','$downimage','$AP')") == 1){

	header('Location: gpstemplates.php');

}else{
	include($config['include_path'] . '/top_header.php');
	echo 'There were issues while inserting information into the database.';
	html_end_box();	
}
}

//------------------------------------------------------------------------------
function getIcons(){
	$iconArray = array();
	$icons = opendir('./plugins/gpsmap/images/icons');
	while (false !== ($icon = readdir($icons))){
		if ($icon != "." && $icon != ".."){
		$iconExplode = explode('.',$icon);
		$iconExplode[1] = strtolower($iconExplode[1]);
			switch($iconExplode[1]){
			case "png":
			case "jpg":
			case "jpeg":
			case "gif":
			$iconArray[] = $icon;
			break;
			default:
			//Not an icon we want to load
			break;
			}
		}
	}
	closedir($icons);
	return $iconArray;
}

?>