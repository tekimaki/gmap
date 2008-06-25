<?php
/**
 * @version $Header: 
 *
 * Copyright (c) 2007 bitweaver.org
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details
 * @author Will <will@wjamesphoto.com>
 * 
 * @package gmap
 * @subpackage functions
 */

/**
 * required setup
 */
require_once('../bit_setup_inc.php' );
require_once( GMAP_PKG_PATH.'BitGmap.php');

// Is package installed and enabled
$gBitSystem->verifyPackage('gmap' );

// Now check permissions to access this page
$gBitSystem->verifyPermission('p_gmap_view');

//if there is no API key don't even bother
if ($gBitSystem->isFeatureActive('gmap_api_key')){
	/* mass-remove:
	   the checkboxes are sent as the array $_REQUEST["checked[]"], values are the wiki-PageNames,
	   e.g. $_REQUEST["checked"][3]="HomePage"
	   $_REQUEST["submit_mult"] holds the value of the "with selected do..."-option list
	   we look if any page's checkbox is on and if remove_samples is selected.
	   then we check permission to delete samples.
	   if so, we call histlib's method remove_all_versions for all the checked samples.
	*/
	if (isset($_REQUEST["submit_mult"]) && isset($_REQUEST["checked"]) && $_REQUEST["submit_mult"] == "remove_gmaps") {
		// Now check permissions to remove the selected gmap
		$gBitSystem->verifyPermission( 'p_gmap_remove' );
																																											
		if( !empty( $_REQUEST['cancel'] ) ) {
		// user cancelled - just continue on, doing nothing
		} elseif( empty( $_REQUEST['confirm'] ) ) {
			$formHash['delete'] = TRUE;
			$formHash['submit_mult'] = 'remove_gmaps';
			foreach( $_REQUEST["checked"] as $del ) {
				$formHash['input'][] = '<input type="hidden" name="checked[]" value="'.$del.'"/>';
			}
			$gBitSystem->confirmDialog( $formHash, array( 'warning' => 'Are you sure you want to delete '.count($_REQUEST["checked"]).' gmaps?', 'error' => 'This cannot be undone!' ) );
		} else {
			foreach ($_REQUEST["checked"] as $deleteId) {
				$tmpPage = new BitGmap( $deleteId );
				if( !$tmpPage->load() || !$tmpPage->expunge() ) {
					array_merge( $errors, array_values( $tmpPage->mErrors ) );
				}
			}
			if( !empty( $errors ) ) {
				$gBitSmarty->assign_by_ref( 'errors', $errors );
			}
		}
	}
	
	
	$gmap = new BitGmap();
	$listgmaps = $gmap->getList( $_REQUEST );
	
	
	$gBitSmarty->assign_by_ref('control', $_REQUEST["control"]);
	$gBitSmarty->assign_by_ref('list', $listgmaps["data"]);
	
	// Display the template
	$gBitSystem->display('bitpackage:gmap/list_gmaps.tpl', tra('Gmap') , array( 'display_mode' => 'list' ));
}else{
	$gBitSystem->display('bitpackage:gmap/error_nokey.tpl', tra('Gmap') , array( 'display_mode' => 'list' ));
}
?>
