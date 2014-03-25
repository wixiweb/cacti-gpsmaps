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
include_once('./include/top_graph_header.php');
include_once('./plugins/gpsmap/includes/setup/show.php');
include_once('./plugins/gpsmap/includes/setup/gpsmapinitial.php');
$body = '';

//decide what needs to be shown
switch ($show) {
    //selected nodes	
    case 'setup':
        $body = 'Please make sure to properly configure GPS Map first under settings -> gpsmap';
        break;
    default:
        if (!$parameter) {
            $parameter = 'all';
        }
        $fileLocation = './plugins/gpsmap/XML/' . $parameter . '-top.html';
        $mapTop = file_get_contents($fileLocation);
        echo($mapTop);
        break;
}
    
//---------------------------------------------------------------

if ($show != 'setup') {?>

<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?<?php echo (empty($apiKey) === false) ? 'key=' . $apiKey . '&' : ''; ?>sensor=false&libraries=geometry"></script>
<script type="text/javascript" src="./js/GPSMaps.js"></script>
<script type="text/javascript" src="./js/infobubble.js"></script>
<script type="text/javascript">
    gpsmap.refreshMap      = '<?php echo $refreshMap; ?>';
    gpsmap.initialLat      = <?php echo $initialLat; ?>;
    gpsmap.initialLng      = <?php echo $initialLong; ?>;
    gpsmap.initialZoom     = <?php echo$initialzoom; ?>;
    gpsmap.liColor         = '<?php echo $liColor; ?>';
    gpsmap.liWidth         = '<?php echo $liWidth; ?>';
    gpsmap.liOpa           = '<?php echo $liOpa; ?>';
    gpsmap.fillColor       = '<?php echo $fillColor; ?>';
    gpsmap.fillOpa         = '<?php echo $fillOpa; ?>';
    gpsmap.circleQuality   = '<?php echo $circleQuality; ?>';
    gpsmap.enableWeather   = '<?php echo $enableWeather; ?>';
    gpsmap.coverageOverlay = '<?php echo $coverageMap; ?>';
    gpsmap.downloadURL     = '<?php echo './XML/', $parameter, '.xml'; ?>';
    gpsmap.t_error         = parseFloat('<?php echo $terror; ?>');
    
<?php include_once('plugins/gpsmap/includes/icons.php'); ?>
<?php include_once('plugins/gpsmap/includes/customicons.php'); ?>
    
    google.maps.event.addDomListener(window, 'load', function() {gpsmap.loader();});
    window.onresize = gpsmap.resize;
</script>
</head> 

<?php
}
$body .= '</script><div id="map" style="height:10px;"></div>';
echo $body;

