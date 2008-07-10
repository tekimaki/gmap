<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_gmap/BitGmapOverlayBase.php,v 1.15 2008/07/10 22:06:45 wjames5 Exp $
 *
 * Copyright (c) 2007 bitweaver.org
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details
 * @author Will <will@wjamesphoto.com>
 * 
 * @package gmap
 */
 
/**
 * required setup
 */

require_once( LIBERTY_PKG_PATH.'LibertyMime.php' );

/**
 * class BitGmapOverlayBase
 * this is the class that contains all the functions for the package
 * 
 * @package gmap
 */
class BitGmapOverlayBase extends LibertyMime {

	var $mOverlayId;

	var $mOverlayType;

	var $mOverlayTable;
	
	var $mOverlayKeychainTable;

	var $mOverlaySeq;


	/**
	* During initialisation, be sure to call our base constructors
	**/
	function BitGmapOverlayBase() {
		parent::LibertyMime();
		
		// Permission setup
		$this->mViewContentPerm  = 'p_gmap_overlay_view';
		$this->mEditContentPerm  = 'p_gmap_overlay_edit';
		$this->mAdminContentPerm = 'p_gmap_admin';
	}



	//returns array of polyline data and associated style and icon style ids for given gmap_id and set_type
	function load() {
		if( !empty( $this->mOverlayId ) || !empty( $this->mContentId ) ) {
			// LibertyContent::load()assumes you have joined already, and will not execute any sql!
			// This is a significant performance optimization
			$overlayKey = $this->mOverlayType.'_id';
			$lookupColumn = !empty( $this->mOverlayId )? $overlayKey : 'content_id';

			$bindVars = array(); $selectSql = ''; $joinSql = ''; $whereSql = '';
			array_push( $bindVars,  $lookupId = @BitBase::verifyId( $this->mOverlayId )? $this->mOverlayId : $this->mContentId );
			$this->getServicesSql( 'content_load_sql_function', $selectSql, $joinSql, $whereSql, $bindVars );

			$query = "select ot.*, lc.*,
					  uue.`login` AS modifier_user, uue.`real_name` AS modifier_real_name,
					  uuc.`login` AS creator_user, uuc.`real_name` AS creator_real_name $selectSql
					  FROM `".BIT_DB_PREFIX.$this->mOverlayTable."` ot
						INNER JOIN `".BIT_DB_PREFIX."liberty_content` lc ON (lc.`content_id` = ot.`content_id`) $joinSql
						LEFT JOIN `".BIT_DB_PREFIX."users_users` uue ON (uue.`user_id` = lc.`modifier_user_id`)
						LEFT JOIN `".BIT_DB_PREFIX."users_users` uuc ON (uuc.`user_id` = lc.`user_id`)
					  WHERE ot.`$lookupColumn`=? $whereSql";
					  
			$result = $this->mDb->query( $query, $bindVars );

			if( $result && $result->numRows() ) {
				$this->mInfo = $result->fields;
				$this->mOverlayId = $result->fields[$overlayKey];
				$this->mContentId = $result->fields['content_id'];
				$this->mInfo['creator'] =( isset( $result->fields['creator_real_name'] )? $result->fields['creator_real_name'] : $result->fields['creator_user'] );
				$this->mInfo['editor'] =( isset( $result->fields['modifier_real_name'] )? $result->fields['modifier_real_name'] : $result->fields['modifier_user'] );
				$this->mInfo['display_url'] = $this->getDisplayUrl();
				$this->mInfo['parsed_data'] = $this->parseData();
				
				LibertyMime::load();
			}
			
		}
		return( count( $this->mInfo ) );
	}

	
	function store( &$pParamHash ) {
		if( $this->verify( $pParamHash ) ) {
			$overlayKey = $this->mOverlayType.'_id';
			$this->mDb->StartTrans();
			if( LibertyMime::store( $pParamHash ) ) {
				if( $this->mOverlayId ) {
					if ( !empty($pParamHash['overlay_store']) ){
						// store the posted changes
						$this->mDb->associateUpdate( BIT_DB_PREFIX.$this->mOverlayTable, $pParamHash['overlay_store'], array( $overlayKey => $pParamHash[$overlayKey] ) );
					}
				} else {
					$pParamHash['overlay_store']['content_id'] = $this->mContentId;
					$pParamHash['overlay_store'][$overlayKey] = $this->mDb->GenID( $this->mOverlaySeq );
					$this->mDb->associateInsert( BIT_DB_PREFIX.$this->mOverlayTable, $pParamHash['overlay_store'] );
					// if its a new polyline we also get a set_id for the keychain and automaticallly associate it with a polyline set.
					$pParamHash['keychain_store'][$overlayKey] = $pParamHash['overlay_store'][$overlayKey];
					$this->mDb->associateInsert( BIT_DB_PREFIX.$this->mOverlayKeychainTable, $pParamHash['keychain_store'] );													
				}
				$this->mDb->CompleteTrans();

				// re-query to confirm results
				$result = $this->load();

			} else {
				$this->mDb->RollbackTrans();
			}
		}
		return( count( $this->mInfo ) );
	}


	/**
	* This function removes an overlay
	**/
	function expunge() {
		$ret = FALSE;
		if( $this->isValid() ) {
			$overlayKey = $this->mOverlayType.'_id';			
			$this->mDb->StartTrans();
			$query = "DELETE FROM `".BIT_DB_PREFIX.$this->mOverlayTable."` WHERE `content_id` = ?";
			$result = $this->mDb->query( $query, array( $this->mContentId ) );
			if( LibertyMime::expunge() ) {
				$ret = TRUE;
				
				// delete all references to the overlay from the overlay keychain
				$query = "DELETE FROM `".BIT_DB_PREFIX.$this->mOverlayKeychainTable."` WHERE `".$overlayKey."` =?";
				$result = $this->mDb->query( $query, array( $this->mOverlayId ) );
				$this->mDb->CompleteTrans();
			} else {
				$this->mDb->RollbackTrans();
			}
		}
		return $ret;
	}

	
	function verifyRemove( &$pParamHash ) {
		$overlayKey = $this->mOverlayType.'_id';
		$pParamHash['overlay_remove'] = array();

		if( !empty( $pParamHash['set_id'] ) && is_numeric( $pParamHash['set_id'] ) ) {
			$pParamHash['overlay_remove']['set_id'] = $pParamHash['set_id'];
		}
		
		if( !empty( $pParamHash[$overlayKey] ) && is_numeric( $pParamHash[$overlayKey] ) ) {
			$pParamHash['overlay_remove'][$overlayKey] = $pParamHash[$overlayKey];
		}
		return( count( $this->mErrors ) == 0 );
	}	
	/**
	* This function removes a polyline from a set
	**/
	function removeFromSet(&$pParamHash) {
		$ret = FALSE;
		if( $this->verifyRemove( $pParamHash ) ) {
			$overlayKey = $this->mOverlayType.'_id';
			$this->mDb->StartTrans();
			$query = "DELETE FROM `".BIT_DB_PREFIX.$this->mOverlayKeychainTable."` WHERE `set_id` = ? AND `".$overlayKey."` =?";
			$result = $this->mDb->query( $query, $pParamHash['overlay_remove'] );
			$ret = TRUE;
			$this->mDb->CompleteTrans();
		}
		return $ret;
	}


	/**
	* Generates the URL to view a polyline on a standalone page
	* @param pMixed a hash passed in by LibertyContent:getList
	* @return the link to display the polyline data.
	*/
	function getDisplayUrl( $pContentId=NULL, $pMixed=NULL ) {
		$ret = NULL;
		$id = NULL;
		$overlayKey = $this->mOverlayType.'_id';
		if( empty( $this->mOverlayId ) && empty( $pMixed[$overlayKey] ) && !empty( $pContentId ) ) {
  			$this->mDb->StartTrans();
  			$query = "SELECT `".$overlayKey."` FROM `".BIT_DB_PREFIX.$this->mOverlayTable."` WHERE `content_id` = ?";
  			$result = $this->mDb->query( $query, $pContentId );
  			$this->mDb->CompleteTrans();
  			$res = $result->fetchrow();
  			$id = $res[$overlayKey];
  		}
	
		if( empty( $this->mOverlayId ) && !empty( $pMixed[$overlayKey] )) {
			$id = $pMixed[$overlayKey];
		}
		
		if( !empty( $this->mOverlayId ) ) {
			$id = $this->mOverlayId;
		}
		
		if ($id != NULL){
			$ret = GMAP_PKG_URL."view_".$this->mOverlayType.".php?".$overlayKey."=".$id;
		} elseif( @BitBase::verifyId( $pMixed['content_id'] ) ) {
			$ret = BIT_ROOT_URL.'index.php?content_id='.$pMixed['content_id'];
		} elseif( $this->isValid() ) {
			$ret = BIT_ROOT_URL.'index.php?content_id='.$this->mContentId;
		}
		return $ret;
	}

	/**
	 * Custom Permission Convenience Functions
	 * These are not as robust as the full custom permissions options of Liberty
	 * However they offer a quick and intuitive way for users to set commonly
	 * desirable custom access control without having to give site wide custom-permission
	 * access to all basic registered users.
	 */	
	/**
	 * Make the overlay editable by all registered users - for wiki like editing
	 */
	function setEditSharing(&$pParamHash){
		if ( isset( $pParamHash['share_edit'] ) ){
			$this->storePermission( 3, 'p_gmap_overlay_edit' );
		}else{
			$this->removePermission( 3, 'p_gmap_overlay_edit' ); 
		}
	}
	
	function isEditShared(){
		$ret = FALSE;
		if ( isset( $this->mPerms['p_gmap_overlay_edit'] ) && $this->mPerms['p_gmap_overlay_edit']['group_id'] == 3 && $this->mPerms['p_gmap_overlay_edit']['is_revoked'] != "y"){
			$ret = TRUE;
		}
		return $ret;
	}

	/**
	 * Make the overlay hidden (assumes view permission is set to anonymous by default and revokes that permission)
	 */
	function setViewPrivate(&$pParamHash){
		if (isset( $pParamHash['make_private'])){
			$this->storePermission('-1', 'p_gmap_overlay_view', TRUE);
		}else{
			$this->removePermission('-1', 'p_gmap_overlay_view');
		}
	}

	function isViewPrivate(){
		$ret = FALSE;
		if ( isset( $this->mPerms['p_gmap_overlay_view'] ) &&  $this->mPerms['p_gmap_overlay_view']['group_id'] == -1  && $this->mPerms['p_gmap_overlay_view']['is_revoked'] == "y" ){
			$ret = TRUE;
		}
		return $ret;
	}
}
?>
