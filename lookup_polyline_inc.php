<?php
/**
 * @version $Header$
 *
 * Copyright (c) 2007 bitweaver.org
 * All Rights Reserved. See below for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details
 * @author Will <will@wjamesphoto.com>
 * 
 * @package gmap
 * @subpackage functions
 */

/**
 * required setup
 */
global $gContent;
require_once( GMAP_PKG_PATH.'BitGmapPolyline.php');
require_once( LIBERTY_PKG_PATH.'lookup_content_inc.php' );

// if we already have a gContent, we assume someone else created it for us, and has properly loaded everything up.
if( empty( $gContent ) || !is_object( $gContent ) || !$gContent->isValid() ) {
	if (!empty($_REQUEST['polyline_id']) && is_numeric($_REQUEST['polyline_id'])) {
		// if sample_id supplied, use that
		$gContent = new BitGmapPolyline( $_REQUEST['polyline_id'] );
	} elseif (!empty($_REQUEST['content_id']) && is_numeric($_REQUEST['content_id'])) {
		// if content_id supplied, use that
		$gContent = new BitGmapPolyline( NULL, $_REQUEST['content_id'] );
	} else {
		// otherwise create new object
		$gContent = new BitGmapPolyline();
	}
	$gContent->load();

	$gBitSmarty->assign_by_ref( "gContent", $gContent );
}
?>