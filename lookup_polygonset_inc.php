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
	require_once( GMAP_PKG_PATH.'BitGmapPolygonSet.php');
	require_once( LIBERTY_PKG_PATH.'lookup_content_inc.php' );

	// if we already have a gContent, we assume someone else created it for us, and has properly loaded everything up.
	if( empty( $gContent ) || !is_object( $gContent ) || !$gContent->isValid() ) {
		if (!empty($_REQUEST['set_id']) && is_numeric($_REQUEST['set_id'])) {
			// if sample_id supplied, use that
			$gContent = new BitGmapPolygonSet( $_REQUEST['set_id'] );
		} elseif (!empty($_REQUEST['content_id']) && is_numeric($_REQUEST['content_id'])) {
			// if content_id supplied, use that
			$gContent = new BitGmapPolygonSet( NULL, $_REQUEST['content_id'] );
		} else {
			// otherwise create new object
			$gContent = new BitGmapPolygonSet();
		}
		if (empty($_REQUEST['gmap_id']) || !is_numeric($_REQUEST['gmap_id'])){
			$_REQUEST['gmap_id'] = NULL;
		}
		$gContent->load( $_REQUEST['gmap_id'] );

		$gBitSmarty->assign_by_ref( "gContent", $gContent );
	}
?>