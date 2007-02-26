<?php
$tables = array(
//@todo should allow_comments be here, or will this be covered by Liberty?
//maptype is keyed to custom maptype_id. 0, 1, and 2 are reserved for Google's Street, Satellite, and Hybrid types
//show_controls in gmaps takes s,l,z,n  small, large, zoom + -, or none
'gmaps' => "
  gmap_id I4 PRIMARY,
  content_id I4 NOTNULL,
  description C(200),
  width I4 DEFAULT 0,
  height I4 DEFAULT 0,
  zoom I4 DEFAULT 16,
  maptype I4 DEFAULT 0,
  zoom_control C(1) DEFAULT 's',
  maptype_control C(5) DEFAULT 'true',
  overview_control C(5) DEFAULT 'true',
  scale C(5) DEFAULT 'true',
  allow_comments C(5) DEFAULT 'true'
  CONSTRAINT ', CONSTRAINT `gmaps_ref` FOREIGN KEY (`content_id`) REFERENCES `".BIT_DB_PREFIX."liberty_content`( `content_id` )'
",

/*
 * set_type in gmaps_sets_keychain can be: 
 * markers, polylines, polygons, maptype
 */
'gmaps_sets_keychain' => "
  gmap_id I4 NOTNULL,
  set_type c(32) NOTNULL,
  set_id I4 NOTNULL,
  plot_on_load C(5) DEFAULT 'true',
  side_panel C(5) DEFAULT 'true',
  explode C(5) DEFAULT 'true',
  cluster C (5) DEFAULT 'false'
  CONSTRAINTS ', CONSTRAINT `gmaps_sets_keychain_gmap_ref` FOREIGN KEY (`gmap_id`) REFERENCES `".BIT_DB_PREFIX."gmaps`( `gmap_id` )'
",

//maptypes and related data
'gmaps_maptypes' => "
  maptype_id I4 PRIMARY,
  name C(64),
  shortname C(4),
  description C(255),
  minzoom I4,
  maxzoom I4,
  errormsg C(255)
",

'gmaps_tilelayers' => "
  tilelayer_id I4 PRIMARY,
  minzoom I4,
  maxzoom I4,
  ispng L,
  tileurl X,
  opacity F
",

'gmaps_copyright' => "
  copyright_id I4 PRIMARY,
  minzoom I4,
  bounds X,
  notice C(32)
",

'gmaps_tilelayers_keychain' => "
  maptype_id I4 NOTNULL,
  tilelayer_id I4 NOTNULL
  CONSTRAINT ', CONSTRAINT `gmaps_tilelayers_keychain_maptype_ref` FOREIGN KEY (`maptype_id`) REFERENCES `".BIT_DB_PREFIX."gmaps_maptypes`( `maptype_id` )
              , CONSTRAINT `gmaps_tilelayers_keychain_tilelayers_ref` FOREIGN KEY (`tilelayers_id`) REFERENCES `".BIT_DB_PREFIX."gmaps_tilelayers`( `tilelayers_id` )'
",

'gmaps_copyrights_keychain' => "
  copyright_id I4 NOTNULL,
  tilelayer_id I4 NOTNULL
  CONSTRAINT ', CONSTRAINT `gmaps_copyrights_keychain_copyright_ref` FOREIGN KEY (`copyright_id`) REFERENCES `".BIT_DB_PREFIX."gmaps_copyrights`( `copyright_id` )
              , CONSTRAINT `gmaps_copyrights_keychain_tilelayers_ref` FOREIGN KEY (`tilelayers_id`) REFERENCES `".BIT_DB_PREFIX."gmaps_tilelayers`( `tilelayers_id` )'
",


//types has two options: 0 => Normal, 1 => AutoPhoto
'gmaps_markers' => "
  marker_id I8 PRIMARY,
  content_id I4 NOTNULL,
  marker_type I2 DEFAULT 0,
  label_data X,
  photo_url X,
  zindex I8 DEFAULT 0,
  allow_comments C(5) DEFAULT 'true'
  CONSTRAINT ', CONSTRAINT `gmaps_markers_ref` FOREIGN KEY (`content_id`) REFERENCES `".BIT_DB_PREFIX."liberty_content`( `content_id` )'
",

//types has two options: 0 => GIcon, 1 => XIcon
'gmaps_icon_styles' => "
  icon_id I4 PRIMARY,
  name C(64),
  icon_style_type I2 DEFAULT 0,
  image X 'icons/FlatColorPins/205.png',
  rollover_image X 'icons/FlatColorPins/090.png',
  icon_w I4 DEFAULT 20,
  icon_h I4 DEFAULT 34,
  shadow_image X DEFAULT 'http://www.google.com/mapfiles/shadow50.png',
  shadow_w I4 DEFAULT 37,
  shadow_h I4 DEFAULT 34,
  icon_anchor_x I4 DEFAULT 9,
  icon_anchor_y I4 DEFAULT 34,
  shadow_anchor_x I4 DEFAULT 18,
  shadow_anchor_y I4 DEFAULT 25,
  infowindow_anchor_x I4 DEFAULT 9,
  infowindow_anchor_y I4 DEFAULT 2,
  image_map X DEFAULT 0,	
  points X DEFAULT 0,
  scale F DEFAULT 1,
  outline_color C(6) DEFAULT 'ffffff',
  outline_weight I4 DEFAULT 2,
  fill_color C(6) DEFAULT 'ff3300',
  fill_opacity F DEFAULT .5
",

//type options: 0 => GMarker, 1 => PdMarker, 2 => XMarker
//lable hover opacity is for all
//label opacity is PdMarker Class only
//label hover styles is CSS for all
//window styles is CSS for PdMarker Class
'gmaps_marker_styles' => "
  style_id I4 PRIMARY,
  name C(64),
  marker_style_type I2 DEFAULT 0,
  label_hover_opacity I4 DEFAULT 70,
  label_opacity I4 DEFAULT 100,
  label_hover_styles C(255) DEFAULT 'border:none; color:black; background-color:#ccc',
  window_styles C(255) DEFAULT 'border:none; color:black; background-color:white'
",

'gmaps_marker_sets' => "
  set_id I4 PRIMARY,
  name C(64),
  description C(255),
  style_id I4 NOTNULL,
  icon_id I4 NOTNULL
  CONSTRAINT ', CONSTRAINT `gmaps_marker_sets_style_ref` FOREIGN KEY (`style_id`) REFERENCES `".BIT_DB_PREFIX."gmaps_marker_styles`( `style_id` )
              , CONSTRAINT `gmaps_marker_sets_icon_ref` FOREIGN KEY (`icon_id`) REFERENCES `".BIT_DB_PREFIX."gmaps_icon_styles`( `icon_id` )'
",

'gmaps_marker_keychain' => "
  set_id I4 NOTNULL,
  marker_id I8 NOTNULL
  CONSTRAINT ', CONSTRAINT `gmaps_marker_keychain_set_ref` FOREIGN KEY (`set_id`) REFERENCES `".BIT_DB_PREFIX."gmaps_marker_sets`( `set_id` )
              , CONSTRAINT `gmaps_marker_keychain_marker_ref` FOREIGN KEY (`marker_id`) REFERENCES `".BIT_DB_PREFIX."gmaps_markers`( `marker_id` )'
",

'gmaps_polylines' => "
  polyline_id I4 PRIMARY,
  user_id I4 NOTNULL,
  modifier_user_id I4 NOTNULL,
  created I8 NOTNULL,
  last_modified I8 NOTNULL,
  version I4 NOTNULL,
  name C(255),
  points_data X,
  border_text X,
  zindex I8 DEFAULT 0
",

//type options: 0 => Google Default, 1 => XPolyline
//opacity and text_bgstyle_opacity take a float from 0-1
//pattern takes an array. Default NULL
'gmaps_polyline_styles' => "
  style_id I4 PRIMARY,
  name C(64),
  polyline_style_type I4 DEFAULT 0,
  color C(6) DEFAULT 'ff3300',
  weight I4 DEFAULT 2,
  opacity F DEFAULT 1,
  pattern c(255) DEFAULT 0,
  segment_count I8 DEFAULT 0,
  begin_arrow C(5) DEFAULT 'false',
  end_arrow C(5) DEFAULT 'false',
  arrows_every I8 DEFAULT 0,
  font c(255) DEFAULT 'Arial',
  text_every I8 DEFAULT 0,
  text_fgstyle_color C(6) DEFAULT 'ffffff',
  text_fgstyle_weight I4 DEFAULT 1,
  text_fgstyle_opacity F DEFAULT 1,
  text_fgstyle_zindex I8 DEFAULT 0,
  text_bgstyle_color C(6) DEFAULT 'ff3300',
  text_bgstyle_weight I4 DEFAULT 2,
  text_bgstyle_opacity  F DEFAULT 1,
  text_bgstyle_zindex I8 DEFAULT 0
",

'gmaps_polyline_sets' => "
  set_id I4 PRIMARY,
  name C(64),
  description C(255),
  style_id I4 NOTNULL
  CONSTRAINT ', CONSTRAINT `gmaps_polyline_sets_style_ref` FOREIGN KEY (`style_id`) REFERENCES `".BIT_DB_PREFIX."gmaps_polyline_styles`( `style_id` )'
",

'gmaps_polyline_keychain' => "
  set_id I4 NOTNULL,
  polyline_id I4 NOTNULL
  CONSTRAINT ', CONSTRAINT `gmaps_polyline_keychain_set_ref` FOREIGN KEY (`set_id`) REFERENCES `".BIT_DB_PREFIX."gmaps_polyline_sets`( `set_id` )
              , CONSTRAINT `gmaps_polyline_keychain_polyline_ref` FOREIGN KEY (`polyline_id`) REFERENCES `".BIT_DB_PREFIX."gmaps_polylines`( `polyline_id` )'
",

//type options: 0 => Polygon, 1 => Circle
//points_data takes an array for polygon
//circle_center for circle
//radius for circle
//@todo wj:check radius after up and running - might require an XDistance (for circle)
'gmaps_polygons' => "
  polygon_id I4 PRIMARY,
  user_id I4 NOTNULL,
  modifier_user_id I4 NOTNULL,
  created I8 NOTNULL,
  last_modified I8 NOTNULL,
  version I4 NOTNULL,
  name C(64),
  circle C(5) DEFAULT 'true',
  points_data X DEFAULT 0,
  circle_center X DEFAULT 0,
  radius F DEFAULT 0,
  border_text X,
  zindex I8 DEFAULT 0
",

//opacity take a float from 0-1
'gmaps_polygon_styles' => "
  style_id I4 PRIMARY,
  name C(64),
  polygon_style_type I4 DEFAULT 0,
  color C(6),
  weight I4 DEFAULT 2,
  opacity F DEFAULT 1
",

'gmaps_polygon_sets' => "
  set_id I4 PRIMARY,
  name C(64),
  description C(255),
  style_id I4 NOTNULL,
  polylinestyle_id I4 NOTNULL
  CONSTRAINT ', CONSTRAINT `gmaps_polygon_sets_style_ref` FOREIGN KEY (`style_id`) REFERENCES `".BIT_DB_PREFIX."gmaps_polygon_styles`( `style_id` )
              , CONSTRAINT `gmaps_polygon_sets_polylinestyle_ref` FOREIGN KEY (`style_id`) REFERENCES `".BIT_DB_PREFIX."gmaps_polyline_styles`( `style_id` )'
",

'gmaps_polygon_keychain' => "
  set_id I4 NOTNULL,
  polygon_id I4 NOTNULL
  CONSTRAINT ', CONSTRAINT `gmaps_polygon_keychain_set_ref` FOREIGN KEY (`set_id`) REFERENCES `".BIT_DB_PREFIX."gmaps_polygon_sets`( `set_id` )
              , CONSTRAINT `gmaps_polygon_keychain_polygon_ref` FOREIGN KEY (`polygon_id`) REFERENCES `".BIT_DB_PREFIX."gmaps_polygons`( `polygon_id` )'
",
);


global $gBitInstaller;

foreach( array_keys( $tables ) AS $tableName ) {
	$gBitInstaller->registerSchemaTable( GMAP_PKG_NAME, $tableName, $tables[$tableName] );
}


$gBitInstaller->registerPackageInfo( GMAP_PKG_NAME, array(
	'description' => "For creating wiki-like Google Maps as well as viewing other bitweaver content (with location information) on Google Maps.",
	'license' => '<a href="http://www.gnu.org/licenses/licenses.html#LGPL">LGPL</a>',
) );


$indices = array (
	'gmaps_gmap_id_idx' => array( 'table' => 'gmaps', 'cols' => 'gmap_id', 'opts' => 'UNIQUE' ),
);
$gBitInstaller->registerSchemaIndexes( GMAP_PKG_NAME, $indices );


// ### Sequences
$sequences = array (
  'gmaps_gmap_id_seq' => array( 'start' => 1 ),
  'gmaps_maptypes_maptype_id_seq' => array( 'start' => 1 ),	
  'gmaps_markers_marker_id_seq' => array( 'start' => 1 ),
  'gmaps_icon_styles_icon_id_seq' => array( 'start' => 1 ),
  'gmaps_marker_styles_style_id_seq' => array( 'start' => 1 ),
  'gmaps_marker_sets_set_id_seq' => array( 'start' => 1 ),
  'gmaps_polylines_polyline_id_seq' => array( 'start' => 1 ),
  'gmaps_polyline_styles_style_id_seq' => array( 'start' => 1 ),
  'gmaps_polyline_sets_set_id_seq' => array( 'start' => 1 ),
  'gmaps_polygons_polygon_id_seq' => array( 'start' => 1 ),
  'gmaps_polygon_styles_style_id_seq' => array( 'start' => 1 ),
  'gmaps_polygon_sets_set_id_seq' => array( 'start' => 1 ),
);
$gBitInstaller->registerSchemaSequences( GMAP_PKG_NAME, $sequences );


//$gBitInstaller->registerSchemaDefault( GMAP_PKG_NAME, array() );



//This does not work as one might expect
/*
$moduleHash = array(
	'mod_package_menu' => array(
		'title' => NULL,
		'layout' => 'gmap',
		'module_id' => 4,
		'ord' => 1,
		'pos' => 'r',
		'module_rsrc' => 'bitpackage:kernel/mod_package_menu.tpl'
	)
);

$gBitInstaller->registerModules( $moduleHash );
*/



//
$gBitInstaller->registerUserPermissions( GMAP_PKG_NAME, array(
	array('bit_gm_edit_map', 'Can edit maps', 'editors', GMAP_PKG_NAME),
	array('bit_gm_view_map', 'Can view maps', 'basic', GMAP_PKG_NAME),
	array('bit_gm_remove_map', 'Can remove maps', 'editors', GMAP_PKG_NAME),
) );


$gBitInstaller->registerPreferences( GMAP_PKG_NAME, array(
	array( GMAP_PKG_NAME, 'gmap_api_key', 'you must get a key from google'),
	array( GMAP_PKG_NAME, 'gmap_width', '0'),
	array( GMAP_PKG_NAME, 'gmap_height', '400'),
	array( GMAP_PKG_NAME, 'gmap_lat', 40.0),
	array( GMAP_PKG_NAME, 'gmap_lng', -97.0),
	array( GMAP_PKG_NAME, 'gmap_zoom', 3),
	array( GMAP_PKG_NAME, 'gmap_scale', 'false'),
	array( GMAP_PKG_NAME, 'gmap_maptype_control', 'true'),
	array( GMAP_PKG_NAME, 'gmap_zoom_control', 's'),
	array( GMAP_PKG_NAME, 'gmap_overview_control', 'true'),
	array( GMAP_PKG_NAME, 'gmap_map_type', 0),
	)
);

?>
