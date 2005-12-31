<?php
/**
 * @version v .0 ?
 * @package bitMapki
 *
 * @author Will <will@wjamesphoto.com>
 *
 *
 * Copyright (c) 2005 bitweaver.org
 * Copyright (c) 2004 bitweaver.org
 * Copyright (c) 2003 tikwiki.org
 * Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details
 *
 */

/**
 * required setup
 */

/**
 * wj: Most of this page is based on the Sample Package
 */

// Initialization
require_once( '../bit_setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage('gmap' );

// Now check permissions to access this page
$gBitSystem->verifyPermission('bit_gm_view_map' );

if (!isset($_REQUEST['gmap_id'] ) ) {
//@toodo how to set up this preference?
//    $_REQUEST['gmap_id'] = $gBitSystem->getPreference("home_gmap");

	 	require_once( GMAP_PKG_PATH.'BitGmap.php');

	  $gmap = new BitGmap();
    $listgmaps = $gmap->getList( $_REQUEST );
    
    
    $gBitSmarty->assign_by_ref('control', $_REQUEST["control"]);
    $gBitSmarty->assign_by_ref('list', $listgmaps["data"]);
    
    // Display the template
    $gBitSystem->display('bitpackage:gmap/list_gmaps.tpl', tra('Gmap') );
}else{
    require_once(GMAP_PKG_PATH.'lookup_gmap_inc.php' );
    
    
    //@todo wj: this line from wiki package - might want to use it
    //include( BITMAP_PKG_URL.'display_gmap_inc.php' );
    
    //set onload function in body
    $gBodyOnload[] = 'loadMap()';
    
    // Display the template
    $gBitSystem->display('bitpackage:gmap/show_gmap.tpl', tra('Gmap') );
}

?>
