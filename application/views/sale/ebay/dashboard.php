<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html class="cufon-active cufon-ready" xmlns="http://www.w3.org/1999/xhtml"><head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<title><?php echo $title; ?></title>

<link href="/static/style/v2style.css" rel="stylesheet" type="text/css" media="screen">
<link href="/static/style/legacy_styles.css" rel="stylesheet" type="text/css" media="screen">
<link rel="icon" type="image/vnd.microsoft.icon" href="http://www.b2c-china.com/favicon.ico">

<script language="JavaScript" type="text/javascript" src="/js/prototype.js"></script>
<script type="text/javascript" src="/js/lib/scriptaculous.js"></script>
<script type="text/javascript" src="/js/lib/builder.js"></script>
<script type="text/javascript" src="/js/lib/effects.js"></script>
<script type="text/javascript" src="/js/lib/dragdrop.js"></script>
<script type="text/javascript" src="/js/lib/controls.js"></script>
<script type="text/javascript" src="/js/lib/slider.js"></script>
<script type="text/javascript" src="/js/lib/sound.js"></script>
<script type="text/javascript" src="/js/lib/scripts.js"></script>
<script language="JavaScript" type="text/javascript" src="/js/ebay_erp.js"></script>

<!-- Here is what i add  -->
<script  type="text/javascript">
    document.observe('dom:loaded', function() {
        checkActionType();
    });
    function checkActionType() {
        var action_type = "<?php echo $action_type; ?>";
        if (action_type == 'copy') {
            var item_id = "<?php echo $item_id; ?>";
            var site = "<?php echo $site; ?>";
            var ebay_id = "<?php echo $ebay_id; ?>";
            copy_item(site, item_id, ebay_id);
        } else {
            initShipping();
        }
    }
</script>

<script type="text/javascript">
function freeShipField(val,id){
    $(id).checked=(val==0?true:false);
}
</script>

<body>
    <div class="headerBar" style="text-align:right;background-color: rgb(251, 250, 246); border: 1px solid rgb(187, 175, 160);">
        <a href="/ebay/" target="rightFrame"><strong>Manage myebay items</strong></a>&nbsp;&nbsp;
    </div>
<div id="v2wrapper">
<div class="v2container">
<div style="clear:both;"></div>
<div style="height:1px;"></div>
<div style="clear:both;"></div>

<div style="clear:both;height:5px;"></div>

<div id="v2Message" class="v2Message" style="display:none;">

</div>

<div id="v2Error" class="v2Error" style="display:none;">

</div>
<div id="v2Notice" class="v2Notice" style="display:none;">

</div>
<div id="v2ProcessingMessage" class="v2ProcessingMessage" style="display:none;">

</div>
<div style="clear:both;height:5px;"></div>



<div class="clearfix" style="position: relative;">

<div style="margin-bottom:30px;border:0px" id="newListing">


    <div class="content v2content clearfix" id="product_data" style="padding-bottom: 10px; margin-top: 40px; display:none;">

    </div>
<div class="content v2content clearfix" id="settings" style="margin-top: 40px;">
<br clear="all">
<div id="listerBox" style="margin-top:10px;">

<div class="scrollArea" id="scroll1">
<div class="content">

<form id="lister_form" name="lister_form" onsubmit="return false;" autocomplete="off">
<input id="auction_id_id" name="auction_id" value="" type="hidden">
<input id="from_id" name="from" value="" type="hidden">
<input id="folder_id_id" name="folder_id" value="0" type="hidden">
<input id="saved_name_id" name="saved_name" value="" type="hidden">
<input id="draft_name_id" name="draft_name" value="" type="hidden">
<input id="what_action" name="what_action" value="" type="hidden">
<input id="scheduled_count" name="scheduled_count" value="0" type="hidden">
<input id="launched_count" name="launched_count" value="0" type="hidden">
<input id="AttributeSetID" name="AttributeSetID" value="0" type="hidden">
<input id="fitmentsEnabled" name="fitmentsEnabled" value="0" type="hidden">
<input id="fitmentData" name="fitmentData" value="" type="hidden">
<input id="itemspecificsenabled" name="itemspecificsenabled" value="0" type="hidden">
<table class="labelData" border="0" cellpadding="0" cellspacing="0">
<colgroup><col class="col1">
<col class="col2">
<col class="col3">

</colgroup><tbody><tr>
<th>Use eBay ID:</th>
<td><div class="noError" id="listingUser">
<select id="eBayID_id" name="EbayID" onchange="load_paypal();loadStoreCategories(0);">
    <?php if (isset($ebay_id_list)): ?>
        <?php foreach ($ebay_id_list as $id) : ?>
            <option value="<?php echo $id; ?>"><?php echo $id; ?></option>
        <?php endforeach; ?>
    <?php endif; ?>

</select>
</div></td>
<td>&nbsp;</td>
</tr>
<tr>
<th>Listing Type: </th>
<td><div class="noError" id="listingType">
<input name="ListingType" id="lt_chinese" value="Chinese" class="byAnIcon" onclick=" change_listing_type(this.value);" type="radio">
<span class="icon-standardAuction"><label for="lt_chinese">Standard Auction</label></span><br>
<span style="display: none;"><input name="ListingType" id="ls_stores" value="StoresFixedPrice" class="byAnIcon" onclick=" change_listing_type(this.value);" type="radio">
<span class="icon-store"><label for="ls_stores">Store</label></span> <br></span>
<input name="ListingType" id="lt_fixed" value="FixedPriceItem" checked="checked" class="byAnIcon" onclick=" change_listing_type(this.value);" type="radio">
<span class="icon-fixedPrice"><label for="lt_fixed">Standard Fixed Price</label></span><br>
<input name="ListingType" id="lt_multisku" value="MultiSKU" class="byAnIcon" onclick=" change_listing_type(this.value);" type="radio">
<span class="icon-multiSKU"><label for="lt_multisku">Multi Sku Variations Fixed Price</label><sup style="color: blue;">Beta</sup></span><br>
</div></td>
<td>&nbsp;</td>
</tr>
<tr id="tr_market">
<th style="vertical-align: top;">Market:</th>
<td style="vertical-align: top;" colspan="2"><div class="noError" id="listingMarket">
<select id="SiteID" name="Site" onchange=" change_market_place(this.value);">
<option value="US" selected="selected">eBay.com</option>
<option value="Australia">eBay.au</option>
<option value="UK">ebay.co.uk</option>
<option value="France">ebay.fr</option>
</select></div>

<div class="research-not-available" id="research-not-available-italy" style="display:none;border:1px solid #aaa;clear:both;background:#f0f0f0;padding:4px;-webkit-border-radius: 5px; -moz-border-radius: 5px; border-radius: 5px;">
Sorry, unfortunately research is not available in this market.  This is due to the limited information available from Terapeak.
</div>

</td>
</tr>

</tbody></table>

<table style="" class="labelData" id="table_inventory" border="0" cellpadding="0" cellspacing="0">
<colgroup><col class="col1">
<col class="col2">
<col class="col3">
</colgroup>
<tbody><tr style="display: none;"><th>Inventory(功能未实现):</th>
<td><select name="inv_crt" onchange="$('use_inventory_tr').hide(); $('create_inventory_tr').hide(); if(this.value == '1') { $('use_inventory_tr').show();} else if(this.value == '2') { $('create_inventory_tr').show();}">
<option selected="selected" value="0">Don't Use Inventory</option>
<option value="1">Use Inventory</option>
<option value="2">Create New Inventory Item </option>
</select></td>
<td>&nbsp;</td>
</tr>
<tr id="use_inventory_tr" style="display: none;">
<th>&nbsp;  </th>
<td>
<div id="inv_item_descr" class="noError">
<span id="inventory_item_descr"></span><br>
<input name="inv_brws" value="Browse Inventory..." onclick="ImportFromInventory();" type="button">
<input id="inventory_id" name="inventory_id" value="" type="hidden">
</div>
</td>
<td>&nbsp;  </td>
</tr>
<tr id="create_inventory_tr" style="display: none;">
<th> Inventory item cost </th>
<td>
<div id="inv_item_cost" class="noError">
<input name="item_cost" size="7" maxlength="12" type="text">
&nbsp;<strong> Custom label/SKU </strong>
<input id="item_sku" name="item_sku" value="Homegarden2012" size="10" maxlength="32" type="text">
</div>
</td>
<td>&nbsp;  </td>
</tr>
</tbody></table>

<table style="" class="labelData" id="tr_category" border="0" cellpadding="0" cellspacing="0">
<colgroup><col class="col1">
<col class="col2">
<col class="col3">
</colgroup><tbody>

<tr class="tr-catsuggest" id="tr-catsuggest-on" style="display: none;">
<th></th>
<td colspan="2"><div class="ra-info" style="display:block;">Did you know the SmartLister can help show you the best categories to choose from based on your product?</div></td>
</tr>

<tr>
<th>Category:</th>
<td>

<input id="PrimaryCategoryID" name="PrimaryCategory" size="50" maxlength="50" onchange="" type="text">

<input name="srchcat1" value="Search..." onclick="showSearchCategoryDiv(this.form.Site.value, 0);" type="button">
&nbsp;
<span class="instructions" id="primary-category-name"></span>
<br>
<span class="instructions" id="primary-category-path"></span>
</td>
<td rowspan="2" class="instructions"></td>
</tr>


<tr>
<th></th>
<td><div id="research-cat"></div></td>
<td></td>
</tr>



<tr id="customer_label_tr">
<th>Custom Label/SKU:</th>
<td>

<input id="CustomerLabelID" name="CustomerLabel" size="50" maxlength="50" type="text"/>
<input type="button" id="getErpData" name="getErpData" value="get erp data..." onClick="dataFromErpBySku();"/>
</td>
</tr>

<tr id="variations_tr" style="display:none;">
<th>Variations:</th>
<td>
<div id="wrapper">
    <div class="container">
<ul class="sku_table">
    <li>
	<ul>
	    <li class="c1">Sub SKU</li>
	    <li class="c2">Quantity</li>
	    <li class="c3">Price</li>
	    <li class="c4"><span id="span_variation_name_0" style="position: relative;"><span id="a_variation_name_0" style="text-decoration: underline;" onclick="show_variations_dropdown_options('variation_name_0', '1', '-1');"></span><input type="text" id="var_variation_name_0" name="variation_names_0" value="" style="width: 58%;"></span></li><li class="c5"><span style="position: relative;" id="span_variation_name_0"><span onclick="show_variations_dropdown_options('variation_name_0', '1', '-1');" style="text-decoration: underline;" id="a_variation_name_0"></span><input type="text" style="width: 58%;" value="" name="variation_names_1" id="var_variation_name_1"></span>&nbsp;</span></li><li class="c6"><span style="position: relative;" id="span_variation_name_0"><span onclick="show_variations_dropdown_options('variation_name_0', '1', '-1');" style="text-decoration: underline;" id="a_variation_name_0"></span><input type="text" style="width: 58%;" value="" name="variation_names_2" id="var_variation_name_2"></span>&nbsp;</span></li><li class="c7"><span style="position: relative;" id="span_variation_name_0"><span onclick="show_variations_dropdown_options('variation_name_0', '1', '-1');" style="text-decoration: underline;" id="a_variation_name_0"></span><input type="text" style="width: 58%;" value="" name="variation_names_3" id="var_variation_name_3"></span>&nbsp;</span></li>
	    <li class="c9 sku_add" style="display: none;">
		<p id="span_new_detail" style="display: none; margin-top: -10px;">
		    <input type="text" id="var_new_detail" style="width: 60px; padding-left: 5px;" name="variation_selector" value="" >
		    <a href="javascript: void(0)" onclick="show_variations_dropdown_options('new_detail', '1');" id="img_new_detail">»</a>
		  
		</p>
		<a style="margin-left: 20px; font-size: 12px; text-decoration: none;" href="javascript: void(0);" onclick="$('span_new_detail').show(); $('aa_new_detail').hide();" id="aa_new_detail" title="Add New Detail Column">New Clmn</a>
	    </li>
	</ul>
    </li>
    <li id="row_t_1">
    <input type="hidden" id="drop_var_flag_1" name="drop_var_flag_1" value="0">
    <ul>
	<li class="c1">
	    <p>
		<input type="text" id="variations_sku_1" name="sku_1" value="">
	    </p>
	</li>
	<li class="c2">
	    <p>
		<input type="text" name="qnt_1" id="qnt_1" value="1">
		Items
	    </p>
	</li>
	<li class="c3">
	    <p>
		$<input type="text" name="price_1" id="price_1" value="">
	    </p>
	</li>
	<li class="c4"><p id="span__1"><span style="text-decoration: underline;" onclick="show_variations_dropdown_options('_1', '1495655791');" id="a__1"></span> <input type="text" id="var__1" name="var__1" style="" value="" ></p></li><li class="c5"><p id="span_test_1"><span style="text-decoration: underline;" onclick="show_variations_dropdown_options('test_1', '2059618027');" id="a_test_1"></span> <input type="text" id="var_a_1" name="var_a_1" style="" value=""></p></li><li class="c6"><p id="span_adf_1"><span style="text-decoration: underline;" onclick="show_variations_dropdown_options('adf_1', '338654218');" id="a_adf_1"></span> <input type="text" id="var_b_1" name="var_b_1" style="" value="" ></p></li><li class="c7"><p id="span_asdf_1"><span style="text-decoration: underline;" onclick="show_variations_dropdown_options('asdf_1', '79340719');" id="a_asdf_1"></span> <input type="text" id="var_c_1" name="var_c_1" style="" value="" ></p></li>
	<li class="c9">
	    <p>
		<span style="margin-left: 20px;" title="Delete Variation Row"></span>
	    </p>
	</li>
    </ul>
</li>
<li id="row_t_2">
    <input type="hidden" id="drop_var_flag_2" name="drop_var_flag_2" value="0">
    <ul>
	<li class="c1">
	    <p>
		<input type="text" id="variations_sku_2" name="sku_2" value="">
	    </p>
	</li>
	<li class="c2">
	    <p>
		<input type="text" name="qnt_2" id="qnt_2" value="1">
		Items
	    </p>
	</li>
	<li class="c3">
	    <p>
		$<input type="text" name="price_2" id="price_2" value="">
	    </p>
	</li>
	<li class="c4"><p id="span__2"><span style="text-decoration: underline;" onclick="show_variations_dropdown_options('_2', '1495655791');" id="a__2"></span> <input type="text" id="var__2" name="var__2" style="" value="" ></p></li><li class="c5"><p id="span_test_2"><span style="text-decoration: underline;" onclick="show_variations_dropdown_options('test_2', '2059618027');" id="a_test_2"></span> <input type="text" id="var_a_2" name="var_a_2" style="" value="" ></p></li><li class="c6"><p id="span_adf_2"><span style="text-decoration: underline;" onclick="show_variations_dropdown_options('adf_2', '338654218');" id="a_adf_2"></span> <input type="text" id="var_b_2" name="var_b_2" style="" value="" ></p></li><li class="c7"><p id="span_asdf_2"><span style="text-decoration: underline;" onclick="show_variations_dropdown_options('asdf_2', '79340719');" id="a_asdf_2"></span> <input type="text" id="var_c_2" name="var_c_2" style="" value="" ></p></li>
	<li class="c9">
	    <p>
		<span style="margin-left: 20px;" title="Delete Variation Row"></span>
	    </p>
	</li>
    </ul>
</li>
<li id="row_t_3">
    <input type="hidden" id="drop_var_flag_3" name="drop_var_flag_3" value="0">
    <ul>
	<li class="c1">
	    <p>
		<input type="text" id="variations_sku_3" name="sku_3" value="">
	    </p>
	</li>
	<li class="c2">
	    <p>
		<input type="text" name="qnt_3" id="qnt_3" value="1">
		Items
	    </p>
	</li>
	<li class="c3">
	    <p>
		$<input type="text" name="price_3" id="price_3" value="">
	    </p>
	</li>
	<li class="c4"><p id="span__3"><span style="text-decoration: underline;" onclick="show_variations_dropdown_options('_3', '1495655791');" id="a__3"></span> <input type="text" id="var__3" name="var__3" style="" value="" ></p></li><li class="c5"><p id="span_test_3"><span style="text-decoration: underline;" onclick="show_variations_dropdown_options('test_3', '2059618027');" id="a_test_3"></span> <input type="text" id="var_a_3" name="var_a_3" style="" value="" ></p></li><li class="c6"><p id="span_adf_3"><span style="text-decoration: underline;" onclick="show_variations_dropdown_options('adf_3', '338654218');" id="a_adf_3"></span> <input type="text" id="var_b_3" name="var_b_3" style="" value="" ></p></li><li class="c7"><p id="span_asdf_3"><span style="text-decoration: underline;" onclick="show_variations_dropdown_options('asdf_3', '79340719');" id="a_asdf_3"></span> <input type="text" id="var_c_3" name="var_c_3" style="" value="" ></p></li>
	<li class="c9">
	    <p>
		<span style="margin-left: 20px;" title="Delete Variation Row"></span>
	    </p>
	</li>
    </ul>
</li>
<li id="row_t_4">
    <input type="hidden" id="drop_var_flag_4" name="drop_var_flag_4" value="0">
    <ul>
	<li class="c1">
	    <p>
		<input type="text" id="variations_sku_4" name="sku_4" value="">
	    </p>
	</li>
	<li class="c2">
	    <p>
		<input type="text" name="qnt_4" id="qnt_4" value="1">
		Items
	    </p>
	</li>
	<li class="c3">
	    <p>
		$<input type="text" name="price_4" id="price_4" value="">
	    </p>
	</li>
	<li class="c4"><p id="span__4"><span style="text-decoration: underline;" onclick="show_variations_dropdown_options('_4', '1495655791');" id="a__4"></span> <input type="text" id="var__4" name="var__4" style="" value=""></p></li><li class="c5"><p id="span_test_4"><span style="text-decoration: underline;" onclick="show_variations_dropdown_options('test_4', '2059618027');" id="a_test_4"></span> <input type="text" id="var_a_4" name="var_a_4" style="" value="" ></p></li><li class="c6"><p id="span_adf_4"><span style="text-decoration: underline;" onclick="show_variations_dropdown_options('adf_4', '338654218');" id="a_adf_4"></span> <input type="text" id="var_b_4" name="var_b_4" style="" value="" ></p></li><li class="c7"><p id="span_asdf_4"><span style="text-decoration: underline;" onclick="show_variations_dropdown_options('asdf_4', '79340719');" id="a_asdf_4"></span> <input type="text" id="var_c_4" name="var_c_4" style="" value="" ></p></li>
	<li class="c9">
	    <p>
		<span style="margin-left: 20px;" title="Delete Variation Row"></span>
	    </p>
	</li>
    </ul>
</li>
<li id="row_t_5">
    <input type="hidden" id="drop_var_flag_5" name="drop_var_flag_5" value="0">
    <ul>
	<li class="c1">
	    <p>
		<input type="text" id="variations_sku_5" name="sku_5" value="">
	    </p>
	</li>
	<li class="c2">
	    <p>
		<input type="text" name="qnt_5" id="qnt_5" value="1">
		Items
	    </p>
	</li>
	<li class="c3">
	    <p>
		$<input type="text" name="price_5" id="price_5" value="">
	    </p>
	</li>
	<li class="c4"><p id="span__5"><span style="text-decoration: underline;" onclick="show_variations_dropdown_options('_5', '1495655791');" id="a__5"></span> <input type="text" id="var__5" name="var__5" style="" value=""></p></li><li class="c5"><p id="span_test_5"><span style="text-decoration: underline;" onclick="show_variations_dropdown_options('test_5', '2059618027');" id="a_test_5"></span> <input type="text" id="var_a_5" name="var_a_5" style="" value="" ></p></li><li class="c6"><p id="span_adf_5"><span style="text-decoration: underline;" onclick="show_variations_dropdown_options('adf_5', '338654218');" id="a_adf_5"></span> <input type="text" id="var_b_5" name="var_b_5" style="" value="" ></p></li><li class="c7"><p id="span_asdf_5"><span style="text-decoration: underline;" onclick="show_variations_dropdown_options('asdf_5', '79340719');" id="a_asdf_5"></span> <input type="text" id="var_c_5" name="var_c_5" style="" value="" ></p></li>
	<li class="c9">
	    <p>
		<span style="margin-left: 20px;" title="Delete Variation Row"></span>
	    </p>
	</li>
    </ul>
</li>
<li id="row_t_6">
    <input type="hidden" id="drop_var_flag_6" name="drop_var_flag_6" value="0">
    <ul>
	<li class="c1">
	    <p>
		<input type="text" id="variations_sku_6" name="sku_6" value="">
	    </p>
	</li>
	<li class="c2">
	    <p>
		<input type="text" name="qnt_6" id="qnt_6" value="1">
		Items
	    </p>
	</li>
	<li class="c3">
	    <p>
		$<input type="text" name="price_6" id="price_6" value="">
	    </p>
	</li>
	<li class="c4"><p id="span__6"><span style="text-decoration: underline;" onclick="show_variations_dropdown_options('_6', '1495655791');" id="a__6"></span> <input type="text" id="var__6" name="var__6" style="" value=""></p></li><li class="c5"><p id="span_test_6"><span style="text-decoration: underline;" onclick="show_variations_dropdown_options('test_6', '2059618027');" id="a_test_6"></span> <input type="text" id="var_a_6" name="var_a_6" style="" value="" ></p></li><li class="c6"><p id="span_adf_6"><span style="text-decoration: underline;" onclick="show_variations_dropdown_options('adf_6', '338654218');" id="a_adf_6"></span> <input type="text" id="var_b_6" name="var_b_6" style="" value="" ></p></li><li class="c7"><p id="span_asdf_6"><span style="text-decoration: underline;" onclick="show_variations_dropdown_options('asdf_6', '79340719');" id="a_asdf_6"></span> <input type="text" id="var_c_6" name="var_c_6" style="" value="" ></p></li>
	<li class="c9">
	    <p>
		<span style="margin-left: 20px;" title="Delete Variation Row"></span>
	    </p>
	</li>
    </ul>
</li>
<li id="row_t_7">
    <input type="hidden" id="drop_var_flag_7" name="drop_var_flag_7" value="0">
    <ul>
	<li class="c1">
	    <p>
		<input type="text" id="variations_sku_7" name="sku_7" value="">
	    </p>
	</li>
	<li class="c2">
	    <p>
		<input type="text" name="qnt_7" id="qnt_7" value="1">
		Items
	    </p>
	</li>
	<li class="c3">
	    <p>
		$<input type="text" name="price_7" id="price_7" value="">
	    </p>
	</li>
	<li class="c4"><p id="span__7"><span style="text-decoration: underline;" onclick="show_variations_dropdown_options('_7', '1495655791');" id="a__7"></span> <input type="text" id="var__7" name="var__7" style="" value=""></p></li><li class="c5"><p id="span_test_7"><span style="text-decoration: underline;" onclick="show_variations_dropdown_options('test_7', '2059618027');" id="a_test_7"></span> <input type="text" id="var_a_7" name="var_a_7" style="" value=""></p></li><li class="c6"><p id="span_adf_7"><span style="text-decoration: underline;" onclick="show_variations_dropdown_options('adf_7', '338654218');" id="a_adf_7"></span> <input type="text" id="var_b_7" name="var_b_7" style="" value="" ></p></li><li class="c7"><p id="span_asdf_7"><span style="text-decoration: underline;" onclick="show_variations_dropdown_options('asdf_7', '79340719');" id="a_asdf_7"></span> <input type="text" id="var_c_7" name="var_c_7" style="" value="" ></p></li>
	<li class="c9">
	    <p>
		<span style="margin-left: 20px;" title="Delete Variation Row"></span>
	    </p>
	</li>
    </ul>
</li>

</ul>
    </div>
</div>


</td>
</tr>

<table class="labelData" id="lister-table-title" style="margin: 5px;" border="0" cellpadding="0" cellspacing="0">
<colgroup><col class="col1">
<!--
<col class="col2" />
<col class="col3" />
-->
</colgroup><tbody><tr>
<th style="vertical-align: top; padding: 8px 0px 0px;">Listing Title:</th>
<td style="vertical-align: top; width: 420px;">
<div id="title" class="noError">
<input id="Title_id" name="Title" size="55" maxlength="55" style="width: 300px;" onchange="" type="text">
</div>
<br><br>
<select onchange="change_title();" name="TitleFromErp" style="display: none; float: left;" id="TitleFromErp">
    
</select>
<div id="title-id-remaining" class="instructions" style="clear:both;float:left;white-space:nowrap;">
    <span id="title-id-remaining-chars">55</span> Characters Remaining (<em>55 Total</em>)
</div>

</td>
<td rowspan="3" style="vertical-align: top;">
<div id="ra-items-area" style="display:none;margin:0px 0px 0px 5px;padding:0px;width:350px;" class="instructions ra-info researchAsset"></div><br>
</td>
</tr>
<tr style="display: none;" id="subtitle-container">
<th style="vertical-align: top;"><span class="optional">Optional</span> Subtitle:</th>
<td style="vertical-align: top;">
<div class="noError" id="subtitle">
<input id="SubTitle" name="SubTitle" size="55" maxlength="55" style="width: 375px;" onchange="" type="text">
<br>
<div id="subtitle-id-remaining" class="instructions" style="clear:both;float:left;"><span id="subtitle-id-remaining-chars">55</span> Characters Remaining (<em>55 Total</em>) (<em>Additional Fee: <a href="http://pages.ebay.com/help/sell/fees.html" target="_blank">$0.50-$1.50</a></em>)</div>

</div>
<br></td>
</tr>

</tbody></table>
<table class="labelData" id="lister-table-price" style="margin: 5px;" border="0" cellpadding="0" cellspacing="0">
<colgroup><col class="col1">

</colgroup><tbody><tr id="tr_condition" style="display: none;">
<th>Item Condition: </th>
<td colspan="2">
<div class="formWrapper" id="ItemConditionDiv">
<select name="ConditionID" id="ConditionID">

<option selected="selected" value="0">--</option></select>
</div>
</td>
</tr>

<tr style="" id="tr_quantity">
<th>Quantity:</th>
<td class="noGap" style="width: 200px;"><div class="noError">
<input name="qnt_sold" value="0" title="only for revise" type="hidden">
<input name="Quantity" id="Quantity" value="499" size="4" onchange="" type="text">
<select name="QuantityOption" onchange="if(this.value == 'Lot') $('lots_div').show(); else $('lots_div').hide();">
<option value="Item" selected="selected">Item(s)</option>
<option value="Lot">Lot</option>
</select>&nbsp;&nbsp;<span id="lots_div" style="display:none;">Lot Size:&nbsp;<input name="LotSize" size="4" onchange="" type="text"></span>
</div></td>
<td></td>
</tr>



<tr style="" id="tr_price">
<th id="starting-fixed-price">Fixed Price:</th>
<td>
<div id="startprice1" class="noError">
<fieldset id="startpriceset">
<input id="StartPrice" name="StartPrice" onchange="" size="9" maxlength="20" type="text">
<select id="Currency" name="Currency" onchange="if(this.form.ShippingType[this.form.ShippingType.selectedIndex].value.indexOf('Flat')>-1)">
<option value="USD" selected="selected">USD</option>
</select>
</fieldset>
</div>
</td>
<td>
<div id="ra-price-info" style="color: rgb(170, 170, 170); width: 300px; display: none;" class="ra-info researchAsset"></div>
<div style="clear:both;"></div>
</td>
</tr>
<tr id="tr_reserve" style="display: none;">
<th><span class="optional">Optional</span> Reserve Price: </th>
<td>
<div id="reserveprice" class="noError">
<input name="ReservePrice" id="ReservePrice" onchange="" size="9" maxlength="20" type="text">
</div></td>
<td>&nbsp;</td>
</tr>
<tr id="tr_buyitnow" style="display: none;">
<th class="noGap" nowrap="nowrap"><span class="optional">Optional</span> Buy It Now Price: </th>
<td class="noGap">
<div id="buyitnowprice" class="noError">
<input id="BuyItNowPriceID" name="BuyItNowPrice" onchange="" size="9" maxlength="20" type="text">
</div>
</td>
<td>&nbsp;</td>
</tr>
<tr style="" id="tr_bestoffer">
<th class="noGap" nowrap="nowrap"><span class="optional">Optional</span> Best Offer: </th>
<td class="noGap" colspan="2">
<table border="0" width="100%"><tbody><tr><td colspan="2">
<input id="BestOfferEnabled" name="BestOfferEnabled" value="1" onclick="if(this.checked == true) $('bestofferoptions').show(); else $('bestofferoptions').hide();" type="checkbox"> <label for="BestOfferEnabled">Allow buyers to send you their Best Offers for your consideration</label>
</td></tr>
<tr id="bestofferoptions" style="display: none;">
<td>&nbsp;&nbsp;</td>
<td>
<table border="0">
<tbody><tr><td><input id="declinelowerbestoffer" name="declinelowerbestoffer" value="1" onclick="if(this.checked == true) { this.form.MinimumBestOfferPrice.disabled = false; } else { this.form.MinimumBestOfferPrice.disabled = true; }" type="checkbox"></td>
<td><label for="declinelowerbestoffer">Automatically decline offers lower than:</label></td><td><input id="MinimumBestOfferPrice" name="MinimumBestOfferPrice" size="8" maxlength="15" disabled="disabled" type="text"></td></tr>
<tr><td><input id="acceptatleastbestoffer" name="acceptatleastbestoffer" value="1" onclick="if(this.checked == true) { this.form.BestOfferAutoAcceptPrice.disabled = false;} else { this.form.BestOfferAutoAcceptPrice.disabled = true;}" type="checkbox"></td>
<td><label for="acceptatleastbestoffer">Automatically accept offers of at least:</label></td><td><input id="BestOfferAutoAcceptPrice" name="BestOfferAutoAcceptPrice" value="0.00" size="8" maxlength="15" disabled="disabled" type="text"></td></tr>
</tbody></table>
</td>
</tr></tbody></table>
</td>
</tr>
</tbody></table>

<table class="labelData" id="lister-table-duration" style="margin: 5px;" border="0" cellpadding="0" cellspacing="0">
<colgroup><col class="col1">
</colgroup><tbody><tr>
<th style="vertical-align: top;"><a name="duration"></a>Duration:</th>
<td colspan="2"><div class="noError" id="listDuration">
<select id="ListingDurationID" style="float:left;" name="ListingDuration" onchange="">
<option value="Days_1">1 Day</option><option value="Days_3">3 Days</option><option value="Days_5">5 Days</option><option value="Days_7">7 Days</option><option value="Days_10">10 Days</option><option selected="selected" value="Days_30">30 Days</option><option value="GTC">Good till cancel</option></select>
<div id="ra-best-duration" style="float:left;display:none;padding:5px;" class="instructions researchAsset">
<div id="ra-best-duration-description" class="ra-info" style="margin:5px;padding:5px;">Best duration for your listing:</div>
<div id="ra-best-duration-graph" style="width:500px;height:120px;padding:0px 0px 0px 5px;"></div>
</div>
</div></td>
<!-- <td>&nbsp;</td> -->
</tr>
</tbody></table>
<table id="lister-table-schedule" class="labelData" border="0" cellpadding="0" cellspacing="0">
<colgroup><col class="col1">
<col class="col2">
<col class="col3">

<!-- RESEARCH INFO -->
</colgroup><tbody><tr>
<th style="vertical-align: top;"></th>
<td colspan="2" style="vertical-align: top;">
<div id="ra-best-hour" style="display:none;padding:10px;text-align:left;margin:0px" class="researchAsset">
<div id="ra-best-hour-description" class="ra-info">Best hour to sell your product / item:</div>
<div id="ra-best-hour-graph" style="width:600px;height:160px;padding:0px 0px 0px 20px;"></div>
<div id="ra-best-hour-link" class="ra-info" style="padding:5px;margin:5px;"></div>
</div>
</td>
</tr>
<!-- END RESEARCH INFO -->

<tr id="schedule-for-later-row">
<th style="vertical-align: top;"><a name="schedule-for-later"></a>Schedule For Later(PDT):</th>
<td colspan="2" style="vertical-align: top;">
<div style="float:left; white-space:nowrap" id="schedule-for-later-row">
<input id="usescheduler" name="usescheduler" value="1" onclick="if(this.checked == true) $('Submit').value = 'Schedule'; else $('Submit').value = 'Launch';" type="checkbox">
<label for="usescheduler">Schedule For</label>
<select name="schedule_hour" id="schedule_hour" onchange="change_lister_to_schedule();">
<option value="01">01</option>
<option value="02">02</option>
<option value="03">03</option>
<option value="04">04</option>
<option value="05">05</option>
<option value="06">06</option>
<option value="07" selected="selected">07</option>
<option value="08">08</option>
<option value="09">09</option>
<option value="10">10</option>
<option value="11">11</option>
<option value="12">12</option>
<option value="13">13</option>
<option value="14">14</option>
<option value="15">15</option>
<option value="16">16</option>
<option value="17">17</option>
<option value="18">18</option>
<option value="19">19</option>
<option value="20">20</option>
<option value="21">21</option>
<option value="22">22</option>
<option value="23">23</option>
</select>
:
<select name="schedule_minute" id="schedule_minute" onchange="change_lister_to_schedule();">
<option value="00">00</option><option value="01">01</option><option value="02">02</option><option value="03">03</option><option value="04">04</option><option value="05">05</option><option value="06">06</option><option value="07">07</option><option value="08">08</option><option value="09">09</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option><option value="31">31</option><option value="32">32</option><option value="33">33</option><option value="34">34</option><option value="35">35</option><option value="36">36</option><option value="37">37</option><option value="38">38</option><option value="39">39</option><option value="40">40</option><option value="41">41</option><option value="42" selected="selected">42</option><option value="43">43</option><option value="44">44</option><option value="45">45</option><option value="46">46</option><option value="47">47</option><option value="48">48</option><option value="49">49</option><option value="50">50</option><option value="51">51</option><option value="52">52</option><option value="53">53</option><option value="54">54</option><option value="55">55</option><option value="56">56</option><option value="57">57</option><option value="58">58</option><option value="59">59</option><option value="60">60</option>
</select>
<!--
<select name="schedule_meridiem" id="schedule_meridiem" onchange="change_lister_to_schedule();">
<option value="am">AM</option><option value="pm" selected="selected">PM</option>
</select>
-->
on
<input class="hasDatepicker" name="auctionDate" id="auctionDateID" size="10" value="<?php echo gmdate('Y-m-d'); ?>" type="text"/>
</div>
<!-- CBS -->
</td>
</tr>
<tr id="tr_use_bulk_opt">
<th>&nbsp;  </th>
<td>
<input id="use_bulk_chk" name="use_bulk_chk" value="1" onclick="show_bulk_lister_options(this.checked);" type="checkbox">
<label for="use_bulk_chk">Use bulk lister options</label>
</td>
<td valign="top">&nbsp;</td>
</tr>
<tr id="bulk_rules_div1" style="display: none;">
<th>&nbsp;  </th>
<td colspan="2">
<div class="noError" id="bulk_intervals">
Total number of listing: <input name="blk_qnt" value="1" size="3" onchange="" type="text"> 
Daily number of listing: <input name="day_qnt" value="1" size="3" onchange=" toggleInterval(this);" type="text"> 
Interval: <input name="interval" value="1" size="2" onchange="" disabled="disabled" type="text"> 
<select name="interval_type" onchange="" disabled="disabled">
<option selected="selected" value="hours">hours</option>
<option value="minutes">minutes</option>
<!--option value="days">days</option-->
</select>
</div>
</td>
</tr>
<tr id="bulk_rules_div2" style="display: none;">
<th>&nbsp;  </th>
<td colspan="2">
<div class="noError" id="bulk_days">
Mon: <input name="week1" value="1" checked="checked" onclick="" type="checkbox">
Tue: <input name="week2" value="1" checked="checked" onclick="" type="checkbox">
Wed: <input name="week3" value="1" checked="checked" onclick="" type="checkbox">
Thu: <input name="week4" value="1" checked="checked" onclick="" type="checkbox">
Fri: <input name="week5" value="1" checked="checked" onclick="" type="checkbox">
Sat: <input name="week6" value="1" checked="checked" onclick="" type="checkbox">
Sun: <input name="week0" value="1" checked="checked" onclick="" type="checkbox">
</div>
</td>
</tr>
</tbody></table>

<table class="labelData" id="variations_table" style="display: none;" border="0" cellpadding="0" cellspacing="0">
<colgroup><col class="col1">
<col class="col2">
<col class="col3">
</colgroup><tbody><tr>
<th style="float: right; margin-right: -10px;" valign="top">
<table style="" boder="1">
<tbody><tr><td>Choose a category:</td></tr>
<tr><td><span class="optional">Optional</span> 2nd category:</td></tr>
<tr><td>Custom Label/SKU:</td></tr>
<tr><td>Variations:</td></tr>
</tbody></table>
</th>
<td colspan="2">
<fieldset id="variationsset" style="width: 100%;">
<input id="init_variations" name="init_variations" value="0" type="hidden">
<div id="variations_content"></div>
</fieldset>
</td>
</tr>
</tbody></table>


<table class="labelData" id="fitmentsTbl" style="display: none;" border="0" cellpadding="0" cellspacing="0">
<colgroup><col class="cl1">
<col class="cl2">
</colgroup><tbody><tr>
<th valign="top">Fitments:</th>
<td><div style="display:block;" id="iframe_wrapper_div">
<!--<iframe id="fitments" src="" class="autoHeight" frameborder="0" scrolling="no" width="100%"></iframe>-->
</div></td>
</tr>
</tbody></table>

<div id="store_category_load">
<table class="labelData" border="0" cellpadding="0" cellspacing="0">
<colgroup><col class="col1">
<col class="col2">
<col class="col3">
</colgroup><tbody><tr>
<th><span class="optional">Optional</span> Store category: </th>
<td><div class="noError" id="storecategory1">
</div>
</td>
<td rowspan="2" class="instructions">Click <a href="javascript:%20void(0)" onclick="loadStoreCategories(1);">here</a> to update your store categories' list from eBay</td>
</tr>

</tbody></table>
</div>

<div id="item_specifics_div">
    
</div>

<table class="labelData" border="0" cellpadding="0" cellspacing="0">
<colgroup><col class="col1">
<col class="col2">
<col class="col3">
</colgroup><tbody><tr>
<td colspan="3" class="divider">&nbsp;</td>
</tr>
</tbody></table>

<!--Description area starts-->
<div class="box grey_lister" id="wo_description" style="display: none;">
<br>
<span style="margin: 15px;">To revise pictures, description, template, or your showcase settings:
<a href="javascript:%20void(0)" onclick="showDescriptionArea();">show/revise description</a>. <br>
</span>
<span style="margin: 15px;"><i>(Leave hidden/hide if you do not need to revise this section.)</i></span>
<br><br>
</div>
<div id="w_description">
<input id="show_revise_description" name="show_revise_description" value="0" title="zero by default when revise" type="hidden">
<div class="box clearfix">

<table style="text-align: right;" id="UseExternalGallery" class="actionButtons" width="100%">
    <tbody>
        <tr>
            <td align="left" style="padding-left:60px;">

External Gallery URL:&nbsp;<input id="ExternalGalleryImageFile" name="ExternalGalleryImageFile" value="http://www.b2c-china.com/images/ebay/" size="65" onblur="$('ExternalImage').src = this.value; $('ExternalImage').height=70; $('ExternalImage').width=70;" type="text">            
            </td>
            <td width="70">
                <a id="ExternalImageLink" target="_blank"><img id="ExternalImage" src="" boder="0" height="70" width="70"></a>
            </td>
        </tr>
        <tr id="image_to_select" style="border:1px solid #C9CCD1;">

        </tr>
    </tbody>
</table>

<ul class="sortable ui-sortable" id="pictures">

</ul>
<!-- this hidden field stores the sort order when altered -->
<input name="sortOrder" id="sortOrder" value="" type="hidden">
</div>

    <div id="DescriptionDiv">
        DESCRIPTION, & TEMPLATE:<br/>
        <textarea id="itemDescription" name="itemDescription" style="width:99%;" rows="20"></textarea>
    </div>

</div>
<!--Description area ends-->



<table class="labelData" border="0" cellpadding="0" cellspacing="0">
<colgroup><col class="col1">
<col class="col2">
<col class="col3">
</colgroup><tbody><tr>
<td colspan="3" class="divider">&nbsp;</td>
</tr>

<tr>
<th valign="top">Item Location: </th>
<td><div class="noError" id="LocationDiv">
<div id="LocationShowDiv">Hong Kong,   &nbsp; &nbsp; &nbsp;Hong Kong<br><a href="javascript:%20void(0)" onclick="$('LocationEditDiv').show(); $('LocationShowDiv').hide();">edit</a></div>
<div id="LocationEditDiv" style="display:none;">
Country: <select name="Country" onchange=""><option value="us">United States</option><option value="ca">Canada</option><option value="gb">United Kingdom</option><option value="de">Germany</option><option value="au">Australia</option><option value="it">Italy</option><option value="fr">France</option><option value="ae">United Arab Emirates</option><option value="al">Albania</option><option value="dz">Algeria</option><option value="ad">Andorra</option><option value="ao">Angola</option><option value="ai">Anguilla</option><option value="ag">Antigua and Barbuda</option><option value="ar">Argentina</option><option value="am">Armenia</option><option value="aw">Aruba</option><option value="at">Austria</option><option value="az">Azerbaijan Republic</option><option value="bs">Bahamas</option><option value="bh">Bahrain</option><option value="bd">Bangladesh</option><option value="bb">Barbados</option><option value="by">Belarus</option><option value="be">Belgium</option><option value="bz">Belize</option><option value="bj">Benin</option><option value="bm">Bermuda</option><option value="bt">Bhutan</option><option value="bo">Bolivia</option><option value="ba">Bosnia and Herzegovina</option><option value="bw">Botswana</option><option value="br">Brazil</option><option value="vg">British Virgin Islands</option><option value="bn">Brunei Darussalam</option><option value="bg">Bulgaria</option><option value="bf">Burkina Faso</option><option value="bi">Burundi</option><option value="kh">Cambodia</option><option value="cm">Cameroon</option><option value="cv">Cape Verde Islands</option><option value="ky">Cayman Islands</option><option value="cf">Central African Republic</option><option value="td">Chad</option><option value="cl">Chile</option><option value="cn">China</option><option value="co">Colombia</option><option value="km">Comoros</option><option value="zr">Congo, Democratic Republic of the</option><option value="cg">Congo, Republic of the</option><option value="ck">Cook Islands</option><option value="cr">Costa Rica</option><option value="ci">Cote d Ivoire (Ivory Coast)</option><option value="hr">Croatia, Republic of</option><option value="cu">Cuba</option><option value="cy">Cyprus</option><option value="cz">Czech Republic</option><option value="dk">Denmark</option><option value="dj">Djibouti</option><option value="dm">Dominica</option><option value="do">Dominican Republic</option><option value="ec">Ecuador</option><option value="eg">Egypt</option><option value="sv">El Salvador</option><option value="gq">Equatorial Guinea</option><option value="qq">Eritrea</option><option value="ee">Estonia</option><option value="et">Ethiopia</option><option value="fk">Falkland Islands (Islas Malvinas)</option><option value="fj">Fiji</option><option value="fi">Finland</option><option value="gf">French Guiana</option><option value="pf">Tahiti</option><option value="ga">Gabon Republic</option><option value="gm">Gambia</option><option value="ge">Georgia</option><option value="gh">Ghana</option><option value="gi">Gibraltar</option><option value="gr">Greece</option><option value="gl">Greenland</option><option value="gd">Grenada</option><option value="gp">Guadeloupe</option><option value="gu">Guam</option><option value="gt">Guatemala</option><option value="gn">Guinea</option><option value="gw">Guinea-Bissau</option><option value="gy">Guyana</option><option value="ht">Haiti</option><option value="hn">Honduras</option><option value="hk" selected="selected">Hong Kong</option><option value="hu">Hungary</option><option value="is">Iceland</option><option value="in">India</option><option value="id">Indonesia</option><option value="ir">Iran</option><option value="iq">Iraq</option><option value="ie">Ireland</option><option value="il">Israel</option><option value="jm">Jamaica</option><option value="sj">Svalbard</option><option value="jp">Japan</option><option value="jo">Jordan</option><option value="kz">Kazakhstan</option><option value="ke">Kenya Coast Republic</option><option value="ki">Kiribati</option><option value="kp">Korea, North</option><option value="kr">Korea, South</option><option value="kw">Kuwait</option><option value="kg">Kyrgyzstan</option><option value="la">Laos</option><option value="lv">Latvia</option><option value="lb">Lebanon-South</option><option value="li">Liechtenstein</option><option value="lt">Lithuania</option><option value="lu">Luxembourg</option><option value="mo">Macau</option><option value="mk">Macedonia</option><option value="mg">Madagascar</option><option value="mw">Malawi</option><option value="my">Malaysia</option><option value="mv">Maldives</option><option value="ml">Mali</option><option value="mt">Malta</option><option value="mh">Marshall Islands</option><option value="mq">Martinique</option><option value="mr">Mauritania</option><option value="mu">Mauritius</option><option value="yt">Mayotte</option><option value="mx">Mexico</option><option value="mc">Monaco</option><option value="mn">Mongolia</option><option value="ms">Montserrat</option><option value="ma">Morocco</option><option value="mz">Mozambique</option><option value="na">Namibia</option><option value="nr">Nauru</option><option value="np">Nepal</option><option value="nl">Netherlands</option><option value="an">Netherlands Antilles</option><option value="nc">New Caledonia</option><option value="nz">New Zealand</option><option value="ni">Nicaragua</option><option value="ne">Niger</option><option value="ng">Nigeria</option><option value="nu">Niue</option><option value="no">Norway</option><option value="om">Oman</option><option value="pk">Pakistan</option><option value="pw">Palau</option><option value="pa">Panama</option><option value="pg">Papua New Guinea</option><option value="py">Paraguay</option><option value="pe">Peru</option><option value="ph">Philippines</option><option value="pl">Poland</option><option value="pt">Portugal</option><option value="pr">Puerto Rico</option><option value="qa">Qatar</option><option value="ro">Romania</option><option value="ru">Russia</option><option value="rw">Rwanda</option><option value="sh">Saint Helena</option><option value="lc">Saint Lucia</option><option value="pm">Saint Pierre and Miquelon</option><option value="vc">Saint Vincent and the Grenadines</option><option value="sm">San Marino</option><option value="sa">Saudi Arabia</option><option value="sn">Senegal</option><option value="sc">Seychelles</option><option value="sl">Sierra Leone</option><option value="sg">Singapore</option><option value="sk">Slovakia</option><option value="si">Slovenia</option><option value="sb">Solomon Islands</option><option value="so">Somalia</option><option value="za">South Africa</option><option value="es">Spain</option><option value="lk">Sri Lanka</option><option value="sd">Sudan</option><option value="sr">Suriname</option><option value="sz">Swaziland</option><option value="se">Sweden</option><option value="ch">Switzerland</option><option value="sy">Syria</option><option value="tw">Taiwan</option><option value="tj">Tajikistan</option><option value="tz">Tanzania</option><option value="th">Thailand</option><option value="tg">Togo</option><option value="to">Tonga</option><option value="tt">Trinidad and Tobago</option><option value="tn">Tunisia</option><option value="tr">Turkey</option><option value="tm">Turkmenistan</option><option value="tv">Tuvalu</option><option value="ug">Uganda</option><option value="ua">Ukraine</option><option value="uy">Uruguay</option><option value="uz">Uzbekistan</option><option value="vu">Vanuatu</option><option value="va">Vatican City State</option><option value="ve">Venezuela</option><option value="vn">Vietnam</option><option value="vi">Virgin Islands (U.S.)</option><option value="wf">Wallis and Futuna</option><option value="eh">Western Sahara</option><option value="ws">Western Samoa</option><option value="ye">Yemen</option><option value="yu">Yugoslavia</option><option value="zm">Zambia</option><option value="zw">Zimbabwe</option></select>
<br><br>
Zip/Postal code: <input name="PostalCode" size="7" maxlength="11" onchange="" type="text">	
&nbsp;&nbsp;Location: <input name="Location" value="Hong Kong" size="20" maxlength="64" onchange="" type="text">
</div>
</div>
</td>
<td>&nbsp;</td>
</tr>
<tr>
<td colspan="3" class="divider">&nbsp;</td>
</tr>



<tr id="paypal">
<th>
<img src="/static/img/check.gif">Accept Payments via: <img id="logo" src="/static/img/ppal.gif">
</th>
<td colspan="2"><div class="formWrapper" id="ppalselector">
<select name="PayPal" id="PayPal" onchange="if(this.selectedIndex == 1) { this.form.PayPalEmailAddress.value = ''; }; ">
<option value="1" selected="selected">yes</option>
<option value="0">no</option>
</select>
PayPal email: <input id="PayPalEmailAddress"  name="PayPalEmailAddress" onchange="if(this.value != '') { this.form.PayPal.selectedIndex = 0; }; " type="text">
</div></td>
</tr>
</tbody></table>

<a name="ship_calc_options"></a>
<table class="labelData" id="shippingOptions" border="0" cellpadding="0" cellspacing="0">
<colgroup><col class="col1">
<col class="col2">
<col class="col3">
</colgroup><tbody><tr>
<td colspan="3" class="divider">&nbsp;</td>
</tr>
<tr>
<th>Shipping type:</th>
<td><select name="ShippingType" id="ShippingType" onchange="change_shipping_type(this.form,0);" style="width:262px">

<option selected="selected" value="Flat">Flat</option><option value="Calculated">Calculated</option><option value="FlatDomesticCalculatedInternational">Flat Domestic &amp; Calculated International</option><option value="CalculatedDomesticFlatInternational">Calculated Domestic &amp; Flat International</option><option value="Digital">Digital Delivery</option><option value="FreightFlat">Freight (over 150 lbs)</option><option value="None">Will Not Ship - Local Pick-Up Only</option></select>
</td>
</tr>
<tr>
<td colspan="3" id="shipDivZip"></td>
</tr>
<tr>
<td colspan="3" id="shipDivTo"><table class="labelDataShipSmall">
<colgroup><col class="col1">
<col class="colShip22">
</colgroup><tbody><tr>
<th>Ship-To Locations:</th>
<td><select name="ship_to_dest" onchange="shipt_to_chg(this.form,this[this.selectedIndex].value);">
<option value="2" selected="selected">Worldwide</option>
</select>
<div id="intrnt_shto_opts" style="margin:10px 0px 0px 0px;display:none;"><table class="shipTableTo" border="0"><tbody><tr><td><input id="ShipToLocation_1" name="ShipToLocation[]" value="Americas" type="checkbox">&nbsp;<label for="ShipToLocation_1">Americas</label></td><td><input id="ShipToLocation_2" name="ShipToLocation[]" value="Asia" type="checkbox">&nbsp;<label for="ShipToLocation_2">Asia</label></td><td><input id="ShipToLocation_3" name="ShipToLocation[]" value="Europe" type="checkbox">&nbsp;<label for="ShipToLocation_3">Europe</label></td><td><input id="ShipToLocation_4" name="ShipToLocation[]" value="CA" type="checkbox">&nbsp;<label for="ShipToLocation_4">Canada</label></td><td><input id="ShipToLocation_5" name="ShipToLocation[]" value="JP" type="checkbox">&nbsp;<label for="ShipToLocation_5">Japan</label></td><td><input id="ShipToLocation_6" name="ShipToLocation[]" value="DE" type="checkbox">&nbsp;<label for="ShipToLocation_6">Germany</label></td><td><input id="ShipToLocation_7" name="ShipToLocation[]" value="MX" type="checkbox">&nbsp;<label for="ShipToLocation_7">Mexico</label></td><td><input id="ShipToLocation_8" name="ShipToLocation[]" value="AU" type="checkbox">&nbsp;<label for="ShipToLocation_8">Australia</label></td><td><input id="ShipToLocation_9" name="ShipToLocation[]" value="GB" type="checkbox">&nbsp;<label for="ShipToLocation_9">UK</label></td><td colspan="1"></td></tr></tbody></table></div>
</td>
</tr>
</tbody></table></td>
<script type="text/javascript">shipToVal=unescape(escape(document.getElementById('shipDivTo').innerHTML));</script>
</tr>
</tbody></table>
<table class="labelData" id="shippingOptions2" style="" border="0" cellpadding="0" cellspacing="0">
</table>

<table class="labelData" border="0" cellpadding="0" cellspacing="0">
<colgroup><col class="col1">
<col class="col2">
<col class="col3">
</colgroup><tbody>
<tr>
<th>Return Policy: </th>
<td colspan="2"><div class="noError" id="returnpolicy">
<select name="ReturnPolicy" id="ReturnPolicy" onchange="if(this.options[this.selectedIndex].value == '1') $('policy_details').show(); else  $('policy_details').hide();">
<option selected="selected" value="0">Returns Not Accepted</option>
<option value="1">Returns Accepted</option>
</select>
<span id="policy_details" style="display:none;">
<br>
Item must be returned within <select id="return_within" name="return_within">
<option selected="selected" value="Days_3">3 Days</option><option value="Days_7">7 Days</option><option value="Days_14">14 Days</option><option value="Days_30">30 Days</option><option value="Days_60">60 Days</option>
</select>
<br><br>
Refund will be given as <select id="return_refund_as" name="return_refund_as">
<option selected="selected" value="Exchange">Exchange</option><option value="MerchandiseCredit">Merchandise Credit</option><option value="MoneyBack">Money Back</option>
</select>
<br><br>
Return shipping will be paid by <select name="return_actor" id="return_actor">
<option selected="selected" value="Buyer">Buyer</option><option value="Seller">Seller</option>
</select>
<br><br>
Return Policy Details(5000 Chars Max. No HTML)
<br>
<textarea id="return_details" name="return_details" rows="6" style="width: 100%;"></textarea>
</span>
</div></td>
</tr>
</tbody></table>
</form>
</div>
</div>
</div>
</div>
<!-- CONTENTS OF PREVIEW TAB -->
<div class="content clearfix" id="preview" style="display: none;">
<div id="previewBox" class="fullBox">
<!-- <iframe id="preview_content" src="" frameborder="0" height="0" width="100%"></iframe> -->
</div>
</div>

</div>

</div>

<div id="listerBottomBar">
<div id="listerBottomBarContainer" style="display:block;">
<img src="http://www.b2c-china.com/favicon.ico" style="float: left; margin: 2px 10px 0px 2px;">
<div id="listerBottomBarContent">
<div style="float:right;padding:0px 20px 0px 0px;margin:5px 0px 0px 0px;">
<input id="Check_Fees" value="Preview Description" class="separator" onclick="previewDescription();" type="button"/>
<input id="Check_Fees" value="Check Errors &amp; Fees" class="separator" onclick="SubmitListerForm('CheckFees');" type="button"/>
<input id="Submit" value="Launch" onclick="SubmitListerForm('Submit');" type="button">
</div>
</div>
<div style="clear:both;"></div>
</div>
<div style="clear:both;"></div>
</div>




<div style="clear:both;"></div>


</div>
</div>


<div class="popup" id="ListerPopup" style="display:none;"></div>

<!-- SHIM (for dimming background) -->
<iframe id="shim" name="shim" style="display: none;" src="/static/html/shim.htm" frameborder="0" scrolling="No"></iframe>


<!-- SHIM (for pop-up windows) -->
<div id="popupshim" style="visibility: hidden"></div>

<!-- CONFIRMATION NOTIFICATION -->
<div class="miniPopup" id="confirm" style="display: none;"></div>



<!-- ABSOLUTELY POSITIONED LAYERS -->

<!-- UPLOADER POPUP -->
<div class="popup" style="display: none;width:570px" id="imageUploader"></div>

<!-- COPY POPUP -->
<div class="popup" style="display: none;width:570px" id="imageHTML">
<h1><table border="0" cellpadding="0" cellspacing="0"><tbody><tr><td class="leftEnd">Image Copy</td><td class="rightEnd"><a href="javascript:%20void(0)" onclick="closePopup('imageHTML');">X</a></td></tr></tbody></table></h1>
<div style="width:560px;margin:3px" id="imageHTMLdiv"></div>
</div>

<!-- IMAGE DETAIL POPUP -->
<div class="popup" style="display: none; left: 270px; top: 60.5px;" id="previewIm">
<h1><table border="0" cellpadding="0" cellspacing="0"><tbody><tr><td class="leftEnd">Image Detail</td><td class="rightEnd"><a href="javascript:%20void(0)" onclick="closePopup('previewIm');">X</a></td></tr></tbody></table></h1>
<div class="content clearfix">
<table class="imgInfo" border="0" cellpadding="0" cellspacing="0">
<colgroup><col class="col1">
<col class="col2">
</colgroup><tbody><tr>
<th>Image Name:</th>
<td><span id="enlargedName1"></span></td>
</tr>
<tr>
<th>Show Details:</th>
<td><input id="showImgDt" onclick="toggleImgDt(this)" type="checkbox"></td>
</tr>
</tbody></table>
<table class="imgInfo" id="imgDetails" style="display: none;" border="0" cellpadding="0" cellspacing="0">
<colgroup><col class="col1">
<col class="col2">
</colgroup><tbody><tr>
<th>Folder:</th>
<td><span id="enlargedFolder"></span></td>
</tr>
<tr>
<th>Thumbnail URL:</th>
<td><span>usbworld/</span><span id="enlargedName2"></span></td>
</tr>
<tr>
<th>Image URL:</th>
<td><span>http://imgs.inkfrog.com/pix/usbworld/</span><span id="enlargedName3"></span></td>
</tr>
<tr>
<th>Image HTML:</th>
<td><span>&lt;img src="http://imgs.inkfrog.com/pix/usbworld/</span><span id="enlargedName4"></span><span>" border="0" alt=""&gt;</span></td>
</tr>
</tbody></table>
<ul class="actionButtons">
<li class="action menu separator"><a href="javascript:%20void(0)" onclick="showMenu('copyImg2');">Copy Image</a>
<ul style="display: none;" id="copyImg2" onmouseout="if (checkMouseLeave(this, event)) hideMenu('copyImg2');" onclick="hideMenu('copyImg2')">
<li><a href="javascript:%20void(0)" title="Copy image URL(s)" onclick="javascript:copyImg('url2');">Copy URL</a></li>
<li><a href="javascript:%20void(0)" title="Copy image HTML codes" onclick="javascript:copyImg('html2');">Copy HTML</a></li>
</ul>
</li>
<li class="action"><a href="javascript:%20void(0)" onclick="javascript: newNames=new Array($('enlargedName4').innerHTML.stripTags()); delImg('removeImageSingle');">Delete</a></li>
</ul>
<p id="nlImg" class="enlargedImg"><img src="/static/img/pix_002.gif" alt="" id="enlargedImg" onload="new Effect.Center($('previewIm'));" border="0"></p>
</div>
</div>




<!-- SPELLCHECKER POPUP -->
<div class="popup" style="display: none;width:400px" id="spellChecker">
<h1><table border="0" cellpadding="0" cellspacing="0"><tbody><tr><td class="leftEnd">Spell Checker</td><td class="rightEnd"><a href="javascript:%20void(0)" onclick="spell2checkClose();">X</a></td></tr></tbody></table></h1>

</div>


<!-- ERROR NOTIFICATION -->
<div class="miniPopup" id="error" style="display: none;"></div>

<!-- Lister NOTIFICATION -->
<div class="miniPopupLister" id="ListerConfirm" style="display: none;">
<div id="cmessage"></div>
<br>
<table border="0" width="100%"><tbody><tr align="center"><td><input value="Close" onclick="HideListerMessage('ListerConfirm');" type="button"></td></tr></tbody></table>
</div>
<!-- Lister ERROR NOTIFICATION -->
<div class="miniPopupLister" id="ListerError" style="display: none;">
<div id="emessage"></div>
<br>
<table border="0" width="100%"><tbody><tr align="center"><td><input value="Close" onclick="HideListerMessage('ListerError');" type="button"></td></tr></tbody></table>
</div>
<!-- Lister processing popup -->
<div class="miniPopupLister" id="processing" style="display: none;">
<div id="processing_cont" align="center">The listing is being processed. Please wait...</div>
<br>
<table border="0" width="100%"><tbody><tr align="center"><td><input id="processing_cancel" value="Cancel" onclick="hidePopupProcessingDiv();" type="button"></td></tr></tbody></table>
</div>

<div id="ui-datepicker-div" class="ui-datepicker ui-widget ui-widget-content ui-helper-clearfix ui-corner-all ui-helper-hidden-accessible"></div></body></html>
