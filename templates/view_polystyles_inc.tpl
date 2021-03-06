{strip}
<div id="view_{$polytype}_{$polystyle_type}styles">
	<div class="row">
		<a href="javascript:void(0);" onclick="BitMap.hide('{$polytype}_{$polystyle_type}styles');" style="float:right">{biticon iname=window-close iexplain="Close"}</a>
	</div>

	<ul style="clear:right">
		{if $polytype == 'polygon' && $polystyle_type == 'polyline'}
		{* disabled until polygon storage and constructor can deal with a -1, e.g. no outline, value *}
		<!-- <li><a href="javascript:void(0);" onclick="BitMap.EditSession.setPolyStyle('{$polytype}','{$polystyle_type}',-1,'None');">None</a></li> -->
		{/if}

		<li>
			<a href="javascript:void(0);" onclick="BitMap.EditSession.setPolyStyle('{$polytype}','{$polystyle_type}',0,'Default (blue)');">
				{if $polystyle_type == 'polyline'}
					<div style="border-top:solid 5px #0000ff; width:70px; float:right;"></div>
				{else}
					<div style="background:#0000ff; width:70px; height:10px; float:right;"></div>
				{/if}
				Default (blue)
				<div style="clear:both"></div>
			</a>
		</li>

		{foreach from=$styles item=style name=styles}
		<li>
			<a href="javascript:void(0);" onclick="BitMap.EditSession.setPolyStyle('{$polytype}','{$polystyle_type}',{$style.style_id},'{$style.name}');">
				{if $polystyle_type == 'polyline'}
					<div style="border-top:solid {$style.weight}px #{$style.color}; width:70px; float:right;"></div>
				{else}
					<div style="background:#{$style.color}; width:70px; height:10px; float:right;"></div>
				{/if}
				{$style.name}
				<div style="clear:both"></div>
			</a>
		</li>
		{/foreach}
	</ul>
	{include file="bitpackage:gmap/jspagination.tpl" ajaxHandler="BitMap.EditSession.getPolyStyles" ajaxParams="'`$polytype`','`$polystyle_type`'"}
</div>
{/strip}
