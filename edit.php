<?php
// Copyright (c) 2005 bitweaver Gmap
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once('../bit_setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage('gmap' );

// Now check permissions to access this page
$gBitSystem->verifyPermission('bit_gm_edit_map' );

// Get the map for specified gmap_id
require_once(GMAP_PKG_PATH.'lookup_gmap_inc.php' );

// Tells smarty to include the Prototype js library
// $gBitSmarty->assign( 'loadAjax', TRUE );

//Preview mode is handled by javascript on the client side.
//There is no callback to the server for previewing changes.

if ( empty($_REQUEST['gmap_id']) && empty($_REQUEST['content_id']) ){
//If its a new map, we first set some defaults and pass a form template to the browser.
//Actual saving is done with a second call to edit.php and store is called the same as an update.
		$gContent->mInfo['title'] = '';
		$gContent->mInfo['description'] = '';
		$gContent->mInfo['data'] = '';
		$gContent->mInfo['parsed_data'] = '';
		$gContent->mInfo['width'] = 0;
		$gContent->mInfo['height'] = 400;
		$gContent->mInfo['lat'] = 0;
		$gContent->mInfo['lon'] = 0;
		$gContent->mInfo['zoom'] = 16;
		$gContent->mInfo['maptype'] = 0;
		$gContent->mInfo['zoom_control'] = 's';
		$gContent->mInfo['maptype_control'] = 'TRUE';
		$gContent->mInfo['overview_control'] = 'TRUE';
		$gContent->mInfo['scale'] = 'TRUE';
		$gContent->mInfo['allow_comments'] = 'TRUE';
}


//Check if this is a update or a new map
if (!empty($_REQUEST["save_map"])) {
    if( $gContent->store( $_REQUEST ) ) {
				//if store is successful we return XML
				$mRet = "<map>"
					."<gmap_id>".$gContent->mInfo['gmap_id']."</gmap_id>"
					."<title>".$gContent->getTitle()."</title>"
					."<desc>".$gContent->mInfo['description']."</desc>"
					."<data>".$gContent->mInfo['xml_data']."</data>"
					."<parsed_data><![CDATA[".$gContent->mInfo['parsed_data']."]]></parsed_data>"				
					."<w>".$gContent->mInfo['width']."</w>"
					."<h>".$gContent->mInfo['height']."</h>"
					."<lat>".$gContent->mInfo['lat']."</lat>"
					."<lon>".$gContent->mInfo['lon']."</lon>"
					."<zoom>".$gContent->mInfo['zoom']."</zoom>"
					."<maptype>".$gContent->mInfo['maptype']."</maptype>"
					."<zoom_con>".$gContent->mInfo['zoom_control']."</zoom_con>"
					."<type_con>".$gContent->mInfo['maptype_control']."</type_con>"
					."<overview_con>".$gContent->mInfo['overview_control']."</overview_con>"
					."<scale>".$gContent->mInfo['show_scale']."</scale>"
					."</map>";
					
      	//since we are returning xml we must report so in the header
      	//we also need to tell the browser not to cache the page
      	//see: http://mapki.com/index.php?title=Dynamic_XML
        // Date in the past
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); 
        // always modified
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        // HTTP/1.1
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        // HTTP/1.0
        header("Pragma: no-cache");
        //XML Header
        header("content-type:text/xml");

			print_r('<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>');
      	print_r($mRet);
								
        die;
								
    }else{
		//@todo - return some sort of store failure message in the xml
      $gBitSmarty->assign_by_ref('errors', $gContent->mErrors );
    }
}else{

		//@todo this was causing a header problem when needed and when not?
    //header("Location: ".$gContent->getDisplayUrl());
				
    // Configure quicktags list
    if ($gBitSystem->isPackageActive( 'quicktags' ) ) {
    	include_once( QUICKTAGS_PKG_PATH.'quicktags_inc.php' );
    }
    
    // WYSIWYG and Quicktag variable
    $gBitSmarty->assign( 'textarea_id', 'editsample' );
		$gBitSmarty->assign( 'loadGoogleMapsAPI', TRUE );
		$gBitSmarty->assign( 'loadMochiKit', TRUE );
		$gBitSmarty->assign( 'loadEditMethods', TRUE );

    //set onload function in body
    $gBodyOnload[] = 'loadMap();';
    
    // Display the template
    $gBitSystem->display( 'bitpackage:gmap/edit_gmap.tpl', tra('Gmap') );    		
}

?>
