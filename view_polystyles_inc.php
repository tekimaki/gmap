<?php
require_once( '../bit_setup_inc.php' );

require_once( GMAP_PKG_PATH.'lookup_gmap_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'gmap' );

// Now check permissions to access this page
$gBitSystem->verifyPermission( 'p_gmap_view' );

//if a style id is passed try to look it up
if( @BitBase::verifyId( $_REQUEST['polylinestyle_id'] )) {

	$getStyleFunc = $gContent->getPolylineStyle;
	$styleId = $_REQUEST['polylinestyle_id'];
	$tpl = 'bitpackage:gmap/polylinestyle_xml.tpl';
	
}elseif( @BitBase::verifyId( $_REQUEST['polygonstyle_id'] )) {

	$getStyleFunc = $gContent->getPolygonStyle;
	$styleId = $_REQUEST['polylinestyle_id'];
	$tpl = 'bitpackage:gmap/polygonstyle_xml.tpl';
	
}elseif( !empty( $_REQUEST['polytype'] ) ){
	
	if( $_REQUEST['polytype'] == 'polyline' ){
		$getStyleListFunc = 'getPolylineStyles';
	}else{
		$getStyleListFunc = 'getPolygonStyles';
	}

	$gBitSmarty->assign( 'polytype', $_REQUEST['polytype'] );

}



// get one
if( @BitBase::verifyId( $_REQUEST['polylinestyle_id'] ) || @BitBase::verifyId( $_REQUEST['polygonstyle_id'] ) ){
	$XMLContent = "";
	$statusCode = 401;

	if( $result = $getStyleFunc( $styleId )) {
		$statusCode = 200;
		$gBitSmarty->assign_by_ref( 'styleInfo', $result );
	}else{
		$XMLContent = "Requested Style Not Found";
	}

	$gBitSmarty->assign( 'statusCode', $statusCode);
	$gBitSmarty->assign( 'XMLContent', $XMLContent);

	$gBitSystem->display($tpl, null, array( 'format' => 'xml', 'display_mode' => 'display' ));
	
// get a list
} else {
	$_REQUEST['max_records'] = $gBitSystem->getConfig( 'max_records' );
	$styles = $gContent->$getStyleListFunc( $_REQUEST );
	$gBitSmarty->assign( 'styles', $styles );

	$gBitSmarty->assign( 'listInfo', $_REQUEST['listInfo'] );

	$gBitSmarty->display( 'bitpackage:gmap/view_polystyles_inc.tpl', tra( 'Map' ) );
}
?>
