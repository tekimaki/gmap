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

// Access the gmap class
global $gContent;
require_once( GMAP_PKG_PATH.'BitGmap.php');
require_once( LIBERTY_PKG_PATH.'lookup_content_inc.php' );
$gContent = new BitGmap();

//Preview mode is handled by javascript on the client side.
//There is no callback to the server for previewing changes.

if (!empty($_REQUEST["save_markerset"])) {
    if( $result = $gContent->storeMarkerSet( $_REQUEST ) ) {

				/* @todo if markersets ever are editable outside 
				 * the context of a map side_panel, explode, cluster 
				 * will need to be conditional
				 */
				//if store is successful we return XML
				$mRet = "<markerset>"
      		  ."<set_id>".$result->fields['set_id']."</set_id>"
      		  ."<name>".$result->fields['name']."</name>"
      		  ."<description>".$result->fields['description']."</description>"
      		  ."<style_id>".$result->fields['style_id']."</style_id>"
      		  ."<icon_id>".$result->fields['icon_id']."</icon_id>"
      		  ."<plot_on_load>".$result->fields['plot_on_load']."</plot_on_load>"
      		  ."<side_panel>".$result->fields['side_panel']."</side_panel>"
      		  ."<explode>".$result->fields['explode']."</explode>"
      		  ."<cluster>".$result->fields['cluster']."</cluster>"
						."</markerset>";

    }else{
		//@todo - return some sort of store failure message in the xml
      $gBitSmarty->assign_by_ref('errors', $gContent->mErrors );
    }
//Check if this to remove from a set, or to delete completely
}elseif (!empty($_REQUEST["remove_markerset"])) {
    if( $gContent->removeMarkerSetFromMap( $_REQUEST ) ) {
				//if store is successful we return XML
				$mRet = "<remove>success</remove>";

		}else{
		//@todo - return some sort of remove failure message in the xml
      $gBitSmarty->assign_by_ref('errors', $gContent->mErrors );
    }
}elseif (!empty($_REQUEST["expunge_markerset"])) {
    if( $gContent->expungeMarkerSet( $_REQUEST ) ) {
				//if store is successful we return XML
				$mRet = "<remove>success</remove>";

		}else{
		//@todo - return some sort of remove failure message in the xml
      $gBitSmarty->assign_by_ref('errors', $gContent->mErrors );
    }
}

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
     		
print_r($mRet);

die;

?>	