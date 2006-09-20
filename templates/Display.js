/* these are all methods required 
 * for displaying a map
 * they extend BitMap.Map
 */ 

MochiKit.Base.update(BitMap.Map.prototype, {

	"attachIcons": function(){
		var i = this.iconstyles;
		if (i.length > 0){
  			for (n=0; n<i.length; n++){
  				if (i[n].icon_style_type != null && i[n].icon_style_type == 0){
  					this.defineGIcon(n);
  				}
  			}
		}
	},

	//@todo - these image paths may not be universal enough, may need to get the root from kernel
	"defineGIcon": function(i){
		var IconStyle = this.iconstyles[i];
		IconStyle.icon = new GIcon();
		IconStyle.icon.image = IconStyle.image;
		IconStyle.icon.iconSize = new GSize(IconStyle.icon_w, IconStyle.icon_h);
		IconStyle.icon.iconAnchor = new GPoint(IconStyle.icon_anchor_x, IconStyle.icon_anchor_y);
		IconStyle.icon.shadow = IconStyle.shadow_image;
		IconStyle.icon.shadowSize = new GSize(IconStyle.shadow_w, IconStyle.shadow_h);
		IconStyle.icon.infoShadowAnchor = new GPoint(IconStyle.shadow_anchor_x, IconStyle.shadow_anchor_y);
		IconStyle.icon.infoWindowAnchor = new GPoint(IconStyle.infowindow_anchor_x, IconStyle.infowindow_anchor_y);
	},



	"addMarker": function(i){
		if (this.markers[i]!= null && this.markers[i].plot_on_load == true){
			var M = this.markers[i];
			
			//a variable to set a marker to open on initialization
			//not implemented! script will break if removed    
			var o = false;
			var icon = null;
			if (M.icon_id != 0 && M.icon_id != null){
				var is = this.iconstyles;
				for (var b=0; b<is.length; b++){
					if ( is[b].icon_id == M.icon_id ){
						icon = b;
					}
				}
			}
			if (M.style_id == 0 || typeof( M.style_id ) == 'undefined' || M.style_id == null){
				this.defineGMarker(i, icon);
				if (o == true) {M.gmarker.openInfoWindowHtml( M.gmarker.my_html );};
			}else{
				var s;
				var MarkerStyles = this.markerstyles; 
				for (var c=0; c<MarkerStyles.length; c++){
					if ( MarkerStyles[c].style_id == M.style_id ){
						s = c;
					}
				}
				if ( MarkerStyles[s].marker_style_type == 0){
					this.defineGxMarker(n, icon, s);
					if (o == true) {M.gmarker.openInfoWindowHtml(M.gmarker.my_html);};
				}else if ( MarkerStyles[s].marker_style_type == 1){
					this.definePdMarker(n, icon, s);
					if (o == true) {
						M.gmarker.showTooltip();
						M.gmarker.hideTooltip();
						M.gmarker.showDetailWin();
					};
				}
			}
		}
	},



	"defineGMarker": function(i, n){
	  var M = this.markers[i];
	  var p = new GLatLng(parseFloat(M.lat), parseFloat(M.lng));
	  var myicon = (n != null)?this.iconstyles[n].icon:null;
	  var mytitle;
	  //add marker roll over
	  if (typeof(M.label_data) != 'undefined' && M.label_date != null){
	    mytitle = M.label_data;
	  }else if (typeof(M.title) != 'undefined' && M.title != null){
	    mytitle = M.title;
	  }
		  	
	  M.gmarker = new GMarker(p, {icon: myicon, title:mytitle});
	
	  var mytitle = ["<h1 class='marker-title'>", M.title, "</h1>"].join("");
	  var desc = (M.content_description != '')?M.content_description+" ":'';
	  var stars = '';
	  if (typeof(document.getElementById('iwindow-stars')) != 'undefined' && M.stars_pixels != null){
	    var starsElm = document.getElementById('iwindow-stars').cloneNode(true);
	    var divs = starsElm.getElementsByTagName('div');
	    divs.item(0).id = null;
	    divs.item(1).style.width = M.stars_pixels + "px";
	    stars = starsElm.innerHTML;
	  }
	  var image = '';
	  if (M.marker_type == 1){
			var urlSrc = Marker.photo_url;
			var pos = urlSrc.lastIndexOf('.');
			var str_1 = urlSrc.substring(0, pos);
			var str_2 = urlSrc.substring(pos, urlSrc.length);
			var medUrl = str_1 + "_medium" + str_2;
			image = ["<p><a onClick=\"javascript: window.open('", urlSrc, "','", /* @todo: PathToRoot here */ "')\"><img src='", medUrl, "'></a></p>"].join("");
	  }
	  var d = (new Date(M.created * 1000)).toString();  
		var di = d.lastIndexOf('GMT');
		var ds = d.substring(0, di-10);  
	  var creator = (M.creator_real_name != '')?["<div>", desc, "created by:", M.creator_real_name, " on:", ds, "</div>"].join(""):'';
	  M.created_date = ds;
	  var u = (new Date(M.last_modified * 1000)).toString();  
		var ui = d.lastIndexOf('GMT');
		var us = d.substring(0, di-10);  
	  M.modified_date = us;
	  var link = (M.display_url != '')?["<div><a href='", M.display_url, "'/>Permalink</a></div>"].join(""):'';
	  var data = ( typeof(M.parsed_data)!= 'undefined' && M.parsed_data != '')?M.parsed_data:'';    
	  M.gmarker.my_html = ["<div style='white-space: nowrap;'>", mytitle, creator, link, stars, image, data, "</div>"].join("");
	
	  this.map.addOverlay(M.gmarker);
	},



	"defineGxMarker": function(n, i, s){
		var M = this.markers[n];
	
	  var point = new GLatLng(parseFloat(M.lat), parseFloat(M.lng));
		var icon = null;
		if (i != null){
			icon = this.iconstyles[i].icon;
		}
		var mytip = "<div class='tip-"+this.markerstyles[s].name + "'>" + M.label_data + "</div>";
	  M.gmarker = new GxMarker(point, icon, mytip);
	  M.gmarker.marker_style_type = 0;
	
		var imgLink ='';
		if (M.marker_type == 1){
			var urlSrc = M.photo_url;
			var pos = urlSrc.lastIndexOf('.');
			var str_1 = urlSrc.substring(0, pos);
			var str_2 = urlSrc.substring(pos, urlSrc.length); 
			var medUrl = str_1 + "_medium" + str_2;
			var imgLink = "<p><img src='"+medUrl+"'></p>"
		}
	
	  M.gmarker.my_html = "<div style='white-space: nowrap;'><h1 class='markertitle'>"+M.title+"</h1>" + imgLink + "<p>"+M.parsed_data+"</p></div>";
	  this.map.addOverlay(M.gmarker);
	},



	"definePdMarker": function(n, i, s){
		var M = this.markers[n];
	
		//PdMarker Style
	  var point = new GLatLng(parseFloat(M.lat), parseFloat(M.lng));
		var icon = null;
		if (i != null){
			icon = this.iconstyles[i].icon;
		}
	  M.gmarker = new PdMarker(point, icon);
	  M.gmarker.marker_style_type = 1;
	  M.gmarker.setTooltipClass( "tip-"+this.markerstyles[s].name );
	  M.gmarker.setDetailWinClass( "win-"+this.markerstyles[s].name );
	  M.gmarker.setTooltip( "<div>" + M.label_data + "</div>");
	
		var imgLink ='';
		if (M.marker_type == 1){
			var urlSrc = M.photo_url;
			var pos = urlSrc.lastIndexOf('.');
			var str_1 = urlSrc.substring(0, pos);
			var str_2 = urlSrc.substring(pos, urlSrc.length); 
			var medUrl = str_1 + "_medium" + str_2;
			var imgLink = "<p><img src='"+medUrl+"'></p>"
		}
	
	  M.gmarker.my_html = "<div style='white-space: nowrap;'><h1 class='markertitle'>"+M.title+"</h1>" + imgLink + "<p>"+M.parsed_data+"</p></div>";
	  M.gmarker.setDetailWinHTML( M.marker.my_html );
	  //rollover-icon: M.marker.setHoverImage("http://www.google.com/mapfiles/dd-start.png");
	  this.map.addOverlay(M.gmarker);
	},


	"attachPolylines": function(){
		//get the array we are working on
		var a = this.polylines;
		//if the length of the array is > 0
		if (a.length > 0){
	  	//loop through the array
			for(n=0; n<a.length; n++){
	  		//if the array item is not Null
				if (a[n]!= null && a[n].plot_on_load == true){
					this.attachPolyline(n);
				}
			}
		}
	},
	
		"attachPolyline": function(n){
		var a = this.polylines;
		if (a[n].style_id == 0){
			this.defineGPolyline(n);
		}else{
			var s;
			var PolylineStyles = this.polylinestyles;
			for (var b=0; b<PolylineStyles.length; b++){
				if ( PolylineStyles[b].style_id == a[n].style_id ){
					s = b;
				}
			}
			if ( PolylineStyles[s].polyline_style_type == 0){
				this.defineGPolyline(n, s);
			}else{
				this.defineXPolyline(n, s);
			}
		}
	},



	"defineGPolyline": function(n, s){
		var a = this.polylines;
	
	  var pointlist = new Array();
	  for (p = 0; p < a[n].points_data.length; p+=2 ){
	  	var point = new GPoint(
	  		parseFloat(a[n].points_data[p]),
	  		parseFloat(a[n].points_data[p+1])
	  	);
			pointlist.push(point);
	  };
	
		if ( s != null ){
			var PolylineStyle = this.polylinestyles[s];	
	    var linecolor = "#"+PolylineStyle.color;
	    var lineweight = PolylineStyle.weight;
	    var lineopacity = PolylineStyle.opacity;
	  };
	
	  a[n].polyline = new GPolyline(pointlist, linecolor, lineweight, lineopacity);
	  this.map.addOverlay(a[n].polyline);
	},


//@todo not sure if this can be supported in V2, requires Xmaps Lib
	"defineXPolyline": function(n, s){
		var a = this.polylines;
	
		//make the array of points needed
	  var pointlist = new Array();
	  for (p = 0; p < a[n].points_data.length; p+=2 ){
	  	var point = new GPoint(
	  		parseFloat(a[n].points_data[p]),
	  		parseFloat(a[n].points_data[p+1])
	  	);
			pointlist.push(point);
	  };
	
		//if we are given a style_id we look up the styles otherwise defaults kick in
		var PolylineStyle = this.polylinestyles[s];	
	  var linecolor = "#"+PolylineStyle.color;
	  var txfgcolor = "#"+PolylineStyle.text_fgstyle_color;
	  var txbgcolor = "#"+PolylineStyle.text_bgstyle_color;
	
	  var linestyle = {
			color: linecolor,
			weight: PolylineStyle.weight,
			opacity: PolylineStyle.opacity,
	    /* @todo this prolly needs to be parsed as it should be comma delim
	     * pattern: [bLStyData[s].pattern];
			 */
			segmentCount: PolylineStyle.segment_count,
			beginArrow: PolylineStyle.begin_arrow,
	    endArrow: PolylineStyle.end_arrow,
			arrowsEvery: PolylineStyle.arrows_every,
			text: a[n].border_text,
			textEvery: PolylineStyle.text_every,
			textFgStyle: { color: txfgcolor, weight: PolylineStyle.text_fgstyle_weight, opacity: PolylineStyle.text_fgstyle_opacity },
			textBgStyle: { color: txbgcolor, weight: PolylineStyle.text_bgstyle_weight, opacity: PolylineStyle.text_bgstyle_opacity }
	  };
	
	  a[n].polyline = new XPolyline(pointlist, linestyle);
	  this.map.addOverlay(a[n].polyline);
	},
	
	
	/*@todo all Polygon constructors below might have to change
	 *      the current Polygon constructors use the Xmaps Lib
	 *      but there may not be an Xmaps Lib for V2
	 */  
	"attachPolygons": function(){
		//get the array we are working on
		var a = this.polygons;
	
		//if the length of the array is > 0
		if (a.length > 0){
	  	//loop through the array
			for(n=0; n<a.length; n++){
	  		//if the array item is not Null
				if (a[n]!= null){
					this.attachPolygon(n);
				}
			}
		}
	},


	"attachPolygon": function(n){
		var s;
		var p;
		var Polygon = this.polygons[n];
		var PolylineStyles = this.polylinestyles;
		var PolygonStyles = this.polygonstyles;
		for (var b=0; b<PolylineStyles.length; b++){
			if ( PolylineStyles[b].style_id == Polygon.polylinestyle_id ){
				s = b;
			}
		}
		for (var c=0; c<PolygonStyles.length; c++){
			if ( PolygonStyles[c].style_id == Polygon.style_id ){
				p = c;
			}
		}
		this.defineXPolygon(n, s, p);
	},


	"defineXPolygon": function(n, s, p){
		var fillstyle = {};
		var linestyle = {};
	
		var a = this.polygons;
	
		//Create XPolygon styles
	 	if (p != null){
		  var PolygonStyle = this.polygonstyles[p];
			var fillcolor = "#"+PolygonStyle.color;
			fillstyle = {
	  		color: fillcolor,
	  		weight: PolygonStyle.weight,
	  		opacity: PolygonStyle.opacity
			}
		}
	
	 	if (s != null){
		  var PolylineStyle = this.polylinestyles[s];
	    var linecolor = "#"+PolylineStyle.color;
	    var txfgcolor = "#"+PolylineStyle.text_fgstyle_color;
	    var txbgcolor = "#"+PolylineStyle.text_bgstyle_color;
	    linestyle = {
	  		color: linecolor,
	  		weight: PolylineStyle.weight,
	  		opacity: PolylineStyle.opacity,
	        /* @todo this prolly needs to be parsed as it should be comma delim
	         * pattern: [bLStyData[s].pattern];
	    		 */
	  		segmentCount: PolylineStyle.segment_count,
	  		beginArrow: PolylineStyle.begin_arrow,
	      	endArrow: PolylineStyle.end_arrow,
	  		arrowsEvery: PolylineStyle.arrows_every,
	  		text: a[n].border_text,
	  		textEvery: PolylineStyle.text_every,
	  		textFgStyle: { color: txfgcolor, weight: PolylineStyle.text_fgstyle_weight, opacity: PolylineStyle[s].text_fgstyle_opacity },
	  		textBgStyle: { color: txbgcolor, weight: PolylineStyle.text_bgstyle_weight, opacity: PolylineStyle[s].text_bgstyle_opacity }
	    };
		};
	
		if (a[n].circle == true){
		//if its a circle
	  	var center = new GPoint(parseFloat(a[n].circle_center[0]), parseFloat(a[n].circle_center[1]));
	  	var radius = new XDistance(a[n].radius, XDistance.KM);
	  	a[n].polygon = new XPolygon.createRegularPolygonFromRadius(center, radius, 42, 0, linestyle, fillstyle);
	  }else{
		//if its not
	  	var pointlist = new Array();
	    for (q = 0; q < a[n].points_data.length; q+=2 ){
	    	var point = new GPoint(
	    		parseFloat(a[n].points_data[q]),
	    		parseFloat(a[n].points_data[q+1])
	    	);
	  		pointlist.push(point);
	    };
	    a[n].polygon = new XPolygon(pointlist, linestyle, fillstyle);
	  };
	  this.map.addOverlay(a[n].polygon);
	},
	
	
	
	//make side panel of markers
	//works only with one map on a page
	"attachSideMarkers": function(){
		var s = document.getElementById('gmap-sidepanel');
		//go through all markers and add marker to side list
		var count = this.markers.length;
		if (count > 0){
	   document.getElementById('gmap-map').style.marginRight = '200px';
		 BitMap.show('gmap-sidepanel');
	  }
		for ( var i=0; i<count; i++ ){
			//make the link
			var theNewLink = document.createElement('a');
			theNewLink.href = "javascript: BitMap.MapData[0].Map.markers["+i+"].gmarker.openInfoWindowHtml(BitMap.MapData[0].Map.markers["+i+"].gmarker.my_html);";
			theNewLink.innerHTML = this.markers[i].title;
			
			var theNewText = document.createElement('span');
			theNewText.innerHTML =  (this.markers[i].modified_date != null)?this.markers[i].modified_date:" no date avialable";
			theNewText.innerHTML += (this.markers[i].stars_rating != null)?[" rating: ", this.markers[i].stars_rating].join(""):" not rated";
			
			//make a br
			var BR = document.createElement('br');
			//add link and space to container
			s.appendChild(theNewLink);
			s.appendChild(theNewText);
			s.appendChild(BR);
		}
	},


	"clearSidepanel": function(){
		var s = document.getElementById('gmap-sidepanel');
		var count = s.childNodes.length;
		for (n=count; n>1; n--){
		 s.removeChild(s.childNodes[n-1]);
	  }
	},
	
	/*@todo Merge this function with the one above
	 */ 
	//make side panel of markers
	"attachSideMarkersOld": function(){

		//add tracking var to get count of side sets
		var x = 0;
	
	  var MarkerSets = BitMap.MapData[this.index].MarkerSets;
		//go through all marker sets
		for ( var n=0; n<MarkerSets.length; n++ ){
			//if show set == y
			var MarkerSet = MarkerSets[n];
			if ( MarkerSet.side_panel == true ){
				//up the counter
				x++;
				//add set container to side and mod id
				var theNewDiv = document.createElement('div');
				theNewDiv.id = 'sideset_'+ MarkerSet.set_id;
				theNewDiv.className = 'module box';
	
				var setTitle = "<h3 class='gmapsidetitle'>" + MarkerSet.name + "</h3>";
	 			
				var theIcon = "<img src='http://www.google.com/mapfiles/marker.png' class='gmapsideicon' style='width:20px; height:34px;'>"; 
				var IconStyles = BitMap.MapData[this.index].IconStyles;
				for (var m=0; m<IconStyles.length; m++){
				  var IconStyle = IconStyles[m];
					if ( IconStyle.icon_id == IconStyle.icon_id ){
						var theIcon = "<img src='" + IconStyle.image + "' class='gmapsideicon' style='width:" + IconStyle.icon_w + "px; height:" + IconStyle.icon_h + "px;'>"; 
					}
				}
	
				var setDesc = "<div class='gmapsidedesc'>" + theIcon + " " + MarkerSet.description + "<div style='clear:both'></div></div>";
				var setList = "<div class='boxcontent gmapsidelist' id='listset_" + MarkerSet.set_id + "'></div>";
				theNewDiv.innerHTML += setTitle + setDesc + setList;
				document.getElementById('mapsidepanel').appendChild(theNewDiv);
			}
		}
	
		if ( x != 0 ){
				document.getElementById('mapsidepanel').className = 'mapsidepanel';
			if (bBrowser == 'op'){
				document.getElementById('map').className = 'map-op';
			}
			//go through all markers
			for ( var q=0; q<this.markers.length; q++ ){
				//sort alphabetically
				//if show set == y and show marker == y
				var Marker = this.markers[q];
				if ( Marker.side_panel == true && Marker.explode == true ) {
	
					if (Marker.marker_type == 1){
						var urlSrc = Marker.photo_url;
						var pos = urlSrc.lastIndexOf('.');
						var str_1 = urlSrc.substring(0, pos);
						var str_2 = urlSrc.substring(pos, urlSrc.length); 
						var thumbUrl = str_1 + "_thumb" + str_2;
						var imgLink = "<br/><img src='" + thumbUrl + "'>"
					}else{
						var imgLink ='';
					}
	
					//add marker to side list 
						var theNewLink = document.createElement('a');
			      theNewLink.href = "javascript: BitMap.MapData[0].Map.markers["+i+"].gmarker.openInfoWindowHtml(BitMap.MapData[0].Map.markers["+i+"].gmarker.my_html);";
						theNewLink.innerHTML = Marker.title + imgLink;
	
	//					var theText = document.createTextNode( bMData[q].title);
	//					theNewLink.appendChild(theText);
	
						var BR = document.createElement('br');
						document.getElementById('listset_'+ Marker.set_id).appendChild(theNewLink);
						document.getElementById('listset_'+ Marker.set_id).appendChild(BR);
	
						/*
						var openWindowLink = "<a href='javascript: bMData["+q+"].marker.openInfoWindowHtml(bMData["+q+"].marker.my_html'>"+bMData[q].title+"</a>";
						var attachLink = "<a href='javascript: attachMarker(" + bMData[q].array_n + ", true);'>attach</a>";
						document.getElementById('listset_'+ bMData[q].set_id).innerHTML = openWindowLink + " " + attachLink;
						*/
	
						//copy model html div
						//attach to document
	  				//if marker is set to init
						if ( Marker.plot_on_load == true ) {
	  					//set loaded to true
						}else{
	  					//set loaded to false
						}
				}
			}
		}
	}
});
