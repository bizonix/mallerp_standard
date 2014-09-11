<?php
$scan_bar_code = lang('scan_bar_code');
$submit = lang('submit');
$instock_by_label = lang('instock_by_label');
$html =<<< HTML
<center>
<div align="center" style="
    background-color: #E0F0BE;
    border: 1px solid #8CC7FB;
    padding: 1px 10px 10px 10px;
    width: 300px;">
    <h2>$instock_by_label</h2><br/>
    <form>
    <input type="text" value="" size="20" name="bar_code" id="bar_code"> $scan_bar_code
	<br/><br/>
    <input type="submit" value="$submit" name="submit" id="submit" onClick="return false;">
    </form>
    <br/>
</div>
</center>
HTML;

$select_by_label = lang('select_by_label');
$enter_order_number = lang('enter_order_number');
$html_select =<<< HTML
<center>
<div align="center" style="
    background-color: #E0F0BE;
    border: 1px solid #8CC7FB;
    padding: 1px 10px 10px 10px;
    width: 300px;">
    <h2>$select_by_label</h2><br/>
    <form>
    <input type="text" value="" size="20" name="item_no" id="item_no"> $enter_order_number
	<br/><br/>
    <input type="submit" value="$submit" name="submit_select" id="submit_select" onClick="return false;">
    </form>
    <br/>
</div>
</center>
HTML;

echo $html.'<br/>';
echo $html_select;
echo '<div style="width:600;" id="order_div"></div>';

$url = site_url('stock/inout/proccess_instock_by_label');
$url_select = site_url('stock/inout/select_orders_by_item_no');

if( ! empty ($order))
{
    echo $order;
}

?>

<script>
    document.observe('dom:loaded', function() {
        if ($('bar_code'))
        {
            $('bar_code').focus();
            $('submit').observe('click', function() {
                instock_by_label("<?=$url?>");
            });
        }
        if ($('item_no'))
        {
            $('submit_select').observe('click', function() {
                select_orders("<?=$url_select?>");
            });
        }
    });
</script>