<div>
	<b>Search</b>
	<form name="list-query-form" id="list-query-form">
  		<input name="output" type="hidden" value="json">
		<input name="max_records" type="hidden" value="-1">
		<input name="sort_mode" type="hidden" value="content_type_guid_desc">

	<span>Content Types: </span>
    <select multiple size=5 name="content_type_guid[]">
  		<option value="" selected >Any</option>
		{foreach from=$ContentTypes item=c_type}
			{if $c_type.content_type_guid != 'bitcomment' && $c_type.content_type_guid != 'pigeonholes'}
  				<option value="{$c_type.content_type_guid}" >{if $c_type.content_description == "User Information"}Site Members{else}{$c_type.content_description}s{/if}</option>
  			{/if}
		{/foreach}
    </select><br/>

	<span>Categories: </span>
    <select multiple size=5 name="liberty_categories[]">
  		<option value="" selected >Any</option>
		{foreach from=$pigeonList item=item}
			{section name=ix loop=$item.subtree}
  				<option value="{$item.subtree[ix].content_id}">{$item.subtree[ix].title}</option>
			{/section}
		{/foreach}
    </select><br/>
    
    Date: <input name="hr_date" type="text" size="25px" value="" onchange="BitMap.EvalDate()">
			<a href="javascript:void(null)" onclick="BitMap.ShowCalendar()" title="select date from calendar"><img id="CalLink" src="{$smarty.const.GMAP_PKG_URL}libraries/yahoo/pdate.gif"></a>
		<input name="from_date" type="hidden" value="">
		<input name="until_date" type="hidden" value="">
	<div id="gmap-date-range" style="display:none;">
	Date Range: 
	    <select name="date_range" onchange="return BitMap.SelectDateRange(this)">
	      <option value="selectiononly" >On This Date Only</option>
	      <option value="sinceselection" >Since This Date</option>
	    </select>
    </div>
				 
    <input type="button" value="Submit" onclick="javascript:BitMap.MapData[0].Map.RequestContent(document['list-query-form']);"> 
        
    {* boundries - set this in RequestContent if we want it *}
    {* offset - probably set this dynamically and hidden *}
    {* stars_rating_count - is this supported? *}    

  </form>
</div>
{*we put the calendar object outside the form to save some js processing time, otherwise it gets processed when the form is evaluated*}
<div id="gmap-cal-container" style="position:absolute;z-index:10000;display:none;width:170px;">
	<a style="float:right" href="javascript:void(null)" onclick="BitMap.HideCalendar()"><img src="{$smarty.const.GMAP_PKG_URL}libraries/yahoo/x_d.gif" title="close"></a>
	Select a date 
	<div id="gmap-cal"></div>
</div>
