<?php
$scan_bar_code = lang('scan_bar_code');
$enter_item_no = lang('enter_item_no');
$submit = lang('submit');
$shipping_confirmation = lang('update_shipping_information');
$html =<<< HTML
<center>
<div align="center" style="
    background-color: #E0F0BE;
    border: 1px solid #8CC7FB;
    padding: 1px 10px 10px 10px;
    width: 300px;">
    <h2>$shipping_confirmation</h2><br/>
    <form>
    <input type="text" value="" size="20" name="bar_code" id="bar_code"> $scan_bar_code
	<br/><br/>
    <input type="text" value="" size="20" name="item_no" id="item_no"> $enter_item_no
    <br/><br/>
    <input type="submit" value="$submit" name="submit" id="submit" onClick="return false;">
    </form>
    <br/><br/>
</div>
</center>
HTML;

echo $html;
$url = site_url('shipping/deliver_management/before_update_shipping_information');

?>

<script>
    document.observe('dom:loaded', function() {
        if ($('bar_code'))
        {
            $('bar_code').focus();
            $('submit').observe('click', function() {
                before_confirm_shipping("<?=$url?>");
            });
        }
    });
</script>