{if $gBitUser->hasPermission( 'bit_gm_edit_map' )}
	<div class="floaticon">
		<a href="{$smarty.const.GMAP_PKG_URL}edit.php?gmap_id={$gContent->mInfo.gmap_id}" >{biticon ipackage=liberty iname="edit" iexplain="edit"}</a>
	</div>
{/if}

<div class="header">
	<h1 id="mymaptitle">{$gContent->getTitle()}</h1>
	<h2 id="mymapdesc">{$gContent->mInfo.description}</h2>
	{include file="bitpackage:gmap/gmap_date_bar.tpl"}
</div><!-- end .header -->
