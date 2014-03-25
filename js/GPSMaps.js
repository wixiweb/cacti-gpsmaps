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

var gpsmap = {

    map : null,
    refreshMap : '',
    initialLat : 0,
    initialLng : 0,
    initialZoom : 0,
    liColor : '',
    liWidth : '',
    liOpa : '',
    fillColor : '',
    fillOpa : '',
    circleQuality : '',
    enableWeather : '',
    coverageOverlay : '',
    downloadURL : '',
    markerArray : [],
    APArray : [],
    t_error : 0,

    // -------------------------------------------------------------------------
    loader : function() {  
        this.resize();
        this.gload();
        if (this.refreshMap !== '0') {
            setInterval(function() {gpsmap.gload();}, (this.refreshMap * 60000));
        }
    },
            
    // -------------------------------------------------------------------------
    gload : function() {
        var self = this;

        this.map = new google.maps.Map(
            document.getElementById('map'),
            {
                center: new google.maps.LatLng(self.initialLat, self.initialLng),
                zoom: self.initialZoom,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            }
        );

        this.loadMarkersData(this.downloadURL, function(data) {
            var xml = self.parseMarkersDataToXml(data),
                markers = xml.documentElement.getElementsByTagName('marker');
            ;

            self.putMarkersOnMap(markers);

            // if radius is set, we need to draw a circle.
            if (self.coverageOverlay) {
                for (var i = 0; i < self.APArray.length; i++) {
                    self.drawCoverage(self.APArray[i]);
                }
            }
        });
    },

    // -------------------------------------------------------------------------
    drawCoverage : function(xmlMarker) {
        var center = new google.maps.LatLng(
            parseFloat(xmlMarker.getAttribute('lat')),
            parseFloat(xmlMarker.getAttribute('lng'))
        );

        var radius = xmlMarker.getAttribute('radius');
        var start = parseInt(xmlMarker.getAttribute('start'));
        var stop = parseInt(xmlMarker.getAttribute('stop'));
        var points = [];
        var degrees = stop - start;

        // calculating km/degree
        var latConv = google.maps.geometry.spherical.computeDistanceBetween(
            center,
            new google.maps.LatLng(center.lat() + 0.1, center.lng())
        ) / 100;
        var lngConv = google.maps.geometry.spherical.computeDistanceBetween(
            center,
            new google.maps.LatLng(center.lat(), center.lng() + 0.1)
        ) / 100;

        // loop 
        if (degrees > 360) {
            degrees = 360;
        }

        var step = parseInt(degrees / this.circleQuality);
        if (step === 0) {
            step = 1;
        }

        // if not a circle
        if (degrees < 360) {
            // opens the area
            points.push(center);
        }

        for (var i = start; i < stop; i += step) {
            points.push(
                new google.maps.LatLng(
                    center.lat() + (radius / latConv * Math.cos(i * Math.PI / 180)),
                    center.lng() + (radius / lngConv * Math.sin(i * Math.PI / 180))
                )
            );
        }

        if (degrees === 360) {
            // closes the circle
            points.push(points[0]); 
        }
        else {
            // closes the area
            points.push(center);
        }

        new google.maps.Polygon({
            clickable: false,
            fillColor : '#' + this.fillColor,
            fillOpacity : this.fillOpa,
            map : this.map,
            paths : points,
            strokeColor : '#' + this.liColor,
            strokeOpacity : this.liOpa,
            strokeWeight : this.liWidth
        });
    },

    // -------------------------------------------------------------------------
    setOuterBounds : function(num) {
        var marker = this.markerArray[num],
            n_outer = marker.startcoord.lat() + .5 * ((marker.n_outer - marker.s_outer + .00075) / Math.pow(2, -(17 - this.map.getZoom()))),
            s_outer = marker.startcoord.lat() - .5 * ((marker.n_outer - marker.s_outer) / Math.pow(2, -(17 - this.map.getZoom()))),
            e_outer = marker.startcoord.lng() + .5 * ((marker.e_outer - marker.w_outer + .00025) / Math.pow(2, -(17 - this.map.getZoom()))),
            w_outer = marker.startcoord.lng() - .5 * ((marker.e_outer - marker.w_outer + .00025) / Math.pow(2, -(17 - this.map.getZoom())))
        ; 

        return new google.maps.LatLngBounds(
            new google.maps.LatLng(s_outer, w_outer),
            new google.maps.LatLng(n_outer, e_outer)
        );
    },

    // -------------------------------------------------------------------------
    createMarker : function(xmlMarker) {
        var self = this,
            name = xmlMarker.getAttribute('name'),
            status = xmlMarker.getAttribute('status'),
            point = new google.maps.LatLng(
                parseFloat(xmlMarker.getAttribute('lat')),
                parseFloat(xmlMarker.getAttribute('lng'))
            ),
            joined = xmlMarker.getAttribute('templateId') + status,
            mapMarker = new google.maps.Marker({
                position: point,
                icon: (self.customIcons[joined] == undefined)
                    ? self.customIcons[status]
                    : self.customIcons[joined]
                ,
                title: name
            })
        ;

        mapMarker.startimport = 1;
        mapMarker.importance = 1;
        mapMarker.targeted = 0;
        mapMarker.startcoord = point;
        mapMarker.line = null;
        mapMarker.shifted = 0;
        mapMarker.name = name;
        mapMarker.html = '<a href="../../graph_view.php?action=preview&host_id='
                       + xmlMarker.getAttribute('id')
                       + '"><b>'
                       + name 
                       + '</b></a><br />'
                       + xmlMarker.getAttribute('address')
                       + '<br /> Avalability: '
                       + xmlMarker.getAttribute('availability')
                       + '<br /> Type: '
                       + xmlMarker.getAttribute('type')
                       + '<br /> Latency: '
                       + xmlMarker.getAttribute('latency')
        ;

        var shift = this.t_error + .0002,
            n_coord = mapMarker.getPosition().lat() + this.t_error,
            s_coord = mapMarker.getPosition().lat() - this.t_error,
            e_coord = mapMarker.getPosition().lng() + this.t_error,
            w_coord = mapMarker.getPosition().lng() - this.t_error
        ;

        mapMarker.inBounds = new google.maps.LatLngBounds(
            new google.maps.LatLng(s_coord, w_coord),
            new google.maps.LatLng(n_coord, e_coord)
        );

        mapMarker.n_outer = mapMarker.startcoord.lat() + shift;
        mapMarker.s_outer = mapMarker.startcoord.lat() - shift;
        mapMarker.e_outer = mapMarker.startcoord.lng() + shift;
        mapMarker.w_outer = mapMarker.startcoord.lng() - shift;
        mapMarker.outBounds = null;

        return mapMarker;
    },

    // -------------------------------------------------------------------------
    resize : function() {
        var frame = document.getElementById('map'),
            htmlheight = document.body.parentNode.scrollHeight,
            windowheight
        ;                     

        if (window.innerHeight) {
            windowheight = window.innerHeight;
        }
        else if (document.documentElement && document.documentElement.clientHeight) {
            windowheight = document.documentElement.clientHeight;
        }
        else if (document.body) {
            windowheight = document.body.clientHeight;
        }

        if (htmlheight < windowheight) {
            document.body.style.height = windowheight + 'px';
            frame.style.height = parseInt(windowheight - 105) + 'px';
        }
        else {
            document.body.style.height = htmlheight + 'px';
            frame.style.height = parseInt(htmlheight - 105) + 'px'; 
        }
    },

    // -------------------------------------------------------------------------
    loadMarkersData : function(url, callback) {
        var xhr = (window.XMLHttpRequest)
                ? new XMLHttpRequest()
                : new ActiveXObject('Microsoft.XMLHTTP')
            ,
            onError = function(httpStatus, httpText) {
                alert(
                    'A network error has occured while retrieving markers data : '
                    + httpStatus
                    + ' - '
                    + httpText
                );
            }
        ;

        if (xhr.addEventListener) {
            xhr.addEventListener('load', function(e) {
                if (e.target.status !== 200 && e.target.status !== 304 && e.target.status !== 0) {
                    onError(e.target.status, e.target.statusText);
                }
                callback(e.target.responseText);
            }, false);
            xhr.addEventListener('error', function(e) {
                onError(e.target.status, 'error');
            }, false);
        } else if (el.attachEvent)  {
            xhr.attachEvent('load', function(e) {
                if (e.target.status !== 200 && e.target.status !== 304 && e.target.status !== 0) {
                    onError(e.target.status, e.target.statusText);
                }
                callback(e.target.responseText);
            }, false);
            xhr.attachEvent('error', function(e) {
                onError(e.target.status, 'error');
            }, false);
        }

        xhr.open('GET', url, true);
        xhr.setRequestHeader('X_REQUESTED_WITH', 'XMLHttpRequest');
        xhr.send();
    },

    // -------------------------------------------------------------------------
    parseMarkersDataToXml : function(xmlText) {
        // https://gist.github.com/stevenaw/1305672
        try {
            var xml = null;
            if (window.DOMParser) {
                var parser = new DOMParser();
                xml = parser.parseFromString(xmlText, 'text/xml');
                var found = xml.getElementsByTagName('parsererror');

                if (!found || !found.length || !found[ 0 ].childNodes.length) {
                    return xml;
                }

                return null;
            }
            else {
                xml = new ActiveXObject('Microsoft.XMLDOM');
                xml.async = false;
                xml.loadXML(xmlText);

                return xml;
            }
        }
        catch (e) {
            return null;
        }
    },

    //-------------------------------------------------------------------------- 
    putMarkersOnMap : function(markers) {
        var infoWindow = new google.maps.InfoWindow({}),
            infoBubble = new InfoBubble({})
        ;

        for (var i = 0; i < markers.length; i++) {
            var self = this,
                mapMarker = this.createMarker(markers[i])
            ;

            mapMarker.setMap(this.map);

            this.markerArray.push(mapMarker);
            if (markers[i].getAttribute('radius') > 0) {
                this.APArray.push(markers[i]);
            }

            google.maps.event.addListener(mapMarker, 'click', function(e) {
                var step = 0;

                infoWindow.close();
                infoBubble.close();
                while (infoBubble.tabs_.length) {
                    infoBubble.removeTab(infoBubble.tabs_.length - 1);
                }

                for (var x = 0; x < self.markerArray.length; x++) {
                    if (this.inBounds.contains(self.markerArray[x].getPosition()) === true
                        && self.markerArray[x].getVisible() === true
                    ) {
                        step++;
                        self.markerArray[x].targeted = 1;
                        self.markerArray[x].outBounds = self.setOuterBounds(x);
                    }
                }

                if (step === 1) {
                    this.targeted = 0;
                    this.outBounds = null;

                    infoWindow.setContent(this.html);
                    infoWindow.open(self.map, this);
                }
                else if (step > 1) {
                    var tempCount = 0;
                    for (var x = 0; x < self.markerArray.length; x++) {
                        if (self.markerArray[x].targeted === 1) {
                            tempCount++;
                            infoBubble.addTab(
                                'Point ' + tempCount,
                                '<div style="width:' + (88 * step) + 'px;"></div>' + self.markerArray[x].html
                            );
                        }
                        self.markerArray[x].targeted = 0;
                        self.markerArray[x].outBounds = null;
                    }
                    infoBubble.open(self.map, this);
                }
            });
        }
    }
};